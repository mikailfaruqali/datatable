<?php

namespace Snawbar\DataTable\Components;

use Illuminate\Contracts\View\View;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Snawbar\DataTable\Services\SummableColumn;

abstract class DataTable
{
    protected Request $request;

    private array $editColumns = [];

    private array $addColumns = [];

    private Builder $builder;

    public function __construct(Request $request)
    {
        $this->builder = $this->query($request);

        $this->request = $request;

        $this->setupColumns();
    }

    abstract protected function query(Request $request): Builder;

    abstract public function columns(): array;

    abstract public function tableId(): string;

    public function tableClass(): ?string
    {
        return NULL;
    }

    public function filterContainer(): ?string
    {
        return NULL;
    }

    public function isOrderable(): bool
    {
        return FALSE;
    }

    public function defaultOrderBy(): array
    {
        return [0, 'ASC'];
    }

    public function length(): int
    {
        return 10;
    }

    public function shouldJumpToLastPage(): bool
    {
        return FALSE;
    }

    public function printButtonSelector(): ?string
    {
        return NULL;
    }

    public function excelButtonSelector(): ?string
    {
        return NULL;
    }

    public function exportTitle(): ?string
    {
        return NULL;
    }

    public function editColumn($column, $callback, $condition = NULL): self
    {
        $this->editColumns[$column] = function ($row) use ($callback, $condition) {
            if (is_callable($condition) && $condition($row) == FALSE) {
                return NULL;
            }

            return ($result = $callback($row)) instanceof View ? $result->render() : $result;
        };

        return $this;
    }

    public function addColumn($column, $callback, $condition = NULL): self
    {
        $this->addColumns[$column] = function ($row) use ($callback, $condition) {
            if (is_callable($condition) && $condition($row) == FALSE) {
                return NULL;
            }

            return ($result = $callback($row)) instanceof View ? $result->render() : $result;
        };

        return $this;
    }

    public function setupColumns(): void {}

    public function iteration(): bool
    {
        return FALSE;
    }

    public function totalableColumns(): ?array
    {
        return NULL;
    }

    public function ajax(): JsonResponse
    {
        [$totalRecords, $aggregateQuery] = $this->prepareAggregateQuery();

        $rows = $this->prepareRows();

        return response()->json([
            'draw' => request('draw', 1),
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $totalRecords,
            'totals' => $aggregateQuery,
            'data' => $rows,
        ]);
    }

    public function html(): string
    {
        return view('snawbar-datatable::table-builder', [
            'tableId' => $this->tableId(),
            'jsSafeTableId' => $this->jsSafeTableId(),
            'tableClass' => $this->tableClass(),
            'isOrderable' => $this->isOrderable(),
            'defaultOrderBy' => $this->defaultOrderBy(),
            'length' => $this->length(),
            'shouldJumpToLastPage' => $this->shouldJumpToLastPage(),
            'columns' => $this->processColumns()->values()->toJson(),
            'ajaxUrl' => $this->request->fullUrl(),
            'tableRedrawFunction' => $this->tableRedrawFunction(),
            'filterContainer' => $this->filterContainer(),
            'printButtonSelector' => $this->printButtonSelector(),
            'excelButtonSelector' => $this->excelButtonSelector(),
            'exportTitle' => $this->exportTitle(),
        ])->render();
    }

    public function tableRedrawFunctionString(): string
    {
        return sprintf('%s()', $this->tableRedrawFunction());
    }

    public function jsSafeTableId(): string
    {
        return str_replace('-', '_', $this->tableId());
    }

    public function processColumns(): Collection
    {
        return collect($this->columns())
            ->map(fn ($column) => is_array($column) ? $column : $column->toArray())
            ->filter(fn ($column) => $this->shouldIncludeColumn($column));
    }

    private function shouldIncludeColumn($column): bool
    {
        if (blank($column['data'])) {
            return FALSE;
        }

        $evaluate = fn ($value) => is_callable($value) ? $value() : $value;

        if ($evaluate($column['visible'] ?? TRUE) == FALSE) {
            return FALSE;
        }

        return ! (request()->hasAny(['print', 'excel']) && $evaluate($column['exportable'] ?? TRUE) == FALSE);
    }

    private function prepareRows(): Collection
    {
        $start = request()->input('start', 0);
        $length = request()->input('length', 10);

        $rows = $this->builder
            ->when($this->request->ajax(), fn ($query) => $query->skip($start)->take($length))
            ->when($this->isOrderable(), fn ($query) => $query->orderByRaw($this->buildSortClause()))
            ->get();

        $rows->each(function ($row, $index) use ($start) {
            if ($this->iteration()) {
                $row->iteration = $start + $index + 1;
            }

            foreach ($this->addColumns as $name => $callback) {
                $row->{$name} = $callback($row);
            }

            foreach ($this->editColumns as $name => $callback) {
                if (isset($row->{$name})) {
                    $row->{$name} = $callback($row);
                }
            }
        });

        return $rows;
    }

    private function prepareAggregateQuery(): array
    {
        $totalableColumns = $this->processTotalableColumns();

        $aggregateQuery = DB::query()
            ->fromSub($this->builder, 'totals')
            ->selectRaw('COUNT(*) as total_records')
            ->when($this->isSummable(), function ($query) use ($totalableColumns) {
                $query->addSelect($totalableColumns->pluck('raw')->all());
            })
            ->first();

        $totalRecords = $aggregateQuery->total_records;

        unset($aggregateQuery->total_records);

        $aggregateQuery = collect($aggregateQuery)
            ->mapWithKeys(function ($value, $alias) use ($totalableColumns) {
                $column = $totalableColumns->firstWhere('alias', $alias);

                return [
                    $alias => [
                        'title' => $column['title'],
                        'selector' => $column['selector'],
                        'value' => $column['resolve']($value),
                    ],
                ];
            })
            ->all();

        return [$totalRecords, $aggregateQuery];
    }

    private function processTotalableColumns(): Collection
    {
        return collect($this->totalableColumns())
            ->map(fn ($totalableColumn) => $totalableColumn instanceof SummableColumn ? $totalableColumn : SummableColumn::make($totalableColumn))
            ->filter(fn ($totalableColumn) => $this->shouldIncludeSummableColumns($totalableColumn))
            ->map(fn ($totalableColumn) => [
                'selector' => $totalableColumn->getSelector(),
                'title' => $totalableColumn->getTitle(),
                'alias' => $totalableColumn->getAlias(),
                'raw' => $totalableColumn->rawExpression(),
                'resolve' => fn ($value) => $totalableColumn->getFormmater() ? $totalableColumn->getFormmater()($value) : $value,
            ]);
    }

    private function shouldIncludeSummableColumns($totalableColumn): bool
    {
        if (blank($totalableColumn->getKey())) {
            return FALSE;
        }

        $evaluate = fn ($value) => is_callable($value) ? $value() : $value;

        if ($evaluate($totalableColumn->getVisible()) == FALSE) {
            return FALSE;
        }

        if ($totalableColumn->getColumn()) {
            return $this->processColumns()->pluck('data')->contains($totalableColumn->getColumn());
        }

        return TRUE;
    }

    private function tableRedrawFunction(): string
    {
        return sprintf('%s_redraw', $this->jsSafeTableId());
    }

    private function buildSortClause(): string
    {
        $columnIndex = $this->request->input('order.0.column', $this->request->input('sortable'));
        $columnName = $this->extractSortColumn($this->request->columns, $this->request->column, $columnIndex);
        $direction = mb_strtoupper($this->request->input('order.0.dir', $this->request->dir));

        if ($this->shouldUseDefaultSort($columnName, $direction)) {
            return $this->defaultOrderByString();
        }

        return sprintf('%s %s', $columnName, $direction);
    }

    private function extractSortColumn($indexedColumns, $fallbackColumns, $index): ?string
    {
        if ($indexedColumns) {
            return $indexedColumns[$index]['data'] ?? NULL;
        }

        $fallback = explode(',', (string) $fallbackColumns);

        return in_array($index, $fallback) ? $index : NULL;
    }

    private function shouldUseDefaultSort($column, $direction): bool
    {
        return blank($column) || ! in_array($direction, ['ASC', 'DESC']) || $column === 'iteration';
    }

    private function defaultOrderByString(): string
    {
        return implode(' ', $this->defaultOrderBy());
    }

    private function isSummable(): bool
    {
        return request()->ajax() || request()->has('print');
    }
}
