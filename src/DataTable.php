<?php

namespace Snawbar\DataTable;

use Illuminate\Contracts\View\View;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

abstract class DataTable
{
    protected Request $request;

    private array $editColumns = [];

    private array $addColumns = [];

    private array $additionalData = [];

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

    public function editColumn(string $column, callable $callback, ?callable $condition = NULL)
    {
        $this->editColumns[$column] = function ($row) use ($callback, $condition) {
            if (is_callable($condition) && $condition($row) === FALSE) {
                return NULL;
            }

            return ($result = $callback($row)) instanceof View ? $result->render() : $result;
        };

        return $this;
    }

    public function addColumn(string $column, callable $callback, ?callable $condition = NULL)
    {
        $this->addColumns[$column] = function ($row) use ($callback, $condition) {
            if (is_callable($condition) && $condition($row) === FALSE) {
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

    public function withData($data)
    {
        $this->additionalData = is_array($data) ? array_merge($this->additionalData, $data) : [$data];

        return $this;
    }

    public function ajax()
    {
        $totalRecords = $this->totalRecords();

        $rows = $this->prepareRows();

        return response()->json(array_merge([
            'draw' => request('draw', 1),
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $totalRecords,
            'data' => $rows,
        ], $this->additionalData));
    }

    public function html()
    {
        return view('snawbar-datatable::table-builder', [
            'tableId' => $this->tableId(),
            'jsSafeTableId' => $this->jsSafeTableId(),
            'tableClass' => $this->tableClass(),
            'isOrderable' => $this->isOrderable(),
            'defaultOrderBy' => $this->defaultOrderBy(),
            'length' => $this->length(),
            'shouldJumpToLastPage' => $this->shouldJumpToLastPage(),
            'columns' => $this->columns(),
            'ajaxUrl' => $this->request->fullUrl(),
            'tableRedrawFunction' => $this->tableRedrawFunction(),
            'filterContainer' => $this->filterContainer(),
            'printButtonSelector' => $this->printButtonSelector(),
            'excelButtonSelector' => $this->excelButtonSelector(),
            'exportTitle' => $this->exportTitle(),
        ])->render();
    }

    public function tableRedrawFunctionString()
    {
        return sprintf('%s()', $this->tableRedrawFunction());
    }

    public function jsSafeTableId()
    {
        return str_replace('-', '_', $this->tableId());
    }

    private function prepareRows()
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

    private function totalRecords()
    {
        return DB::query()
            ->fromSub($this->builder, 'totals')
            ->selectRaw('count(*) as total_records')
            ->value('total_records');
    }

    private function tableRedrawFunction()
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

    private function shouldUseDefaultSort(?string $column, string $direction): bool
    {
        return blank($column) || ! in_array($direction, ['ASC', 'DESC']) || $column === 'iteration';
    }

    private function defaultOrderByString(): string
    {
        return implode(' ', $this->defaultOrderBy());
    }
}
