<?php

namespace Snawbar\DataTable\Components;

use Illuminate\Contracts\View\View;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Fluent;
use Snawbar\DataTable\Services\Column;
use Snawbar\DataTable\Services\Total;

abstract class DataTable
{
    public Collection $processColumns;

    public Fluent $attributes;

    protected Request $request;

    protected Collection $processColumnInstance;

    protected Collection $processTotalableColumns;

    private Builder $builder;

    private array $editColumns = [];

    private array $addColumns = [];

    private array $hiddenColumns = [];

    public function __construct(Request $request)
    {
        $this->request = $request;

        $this->setupColumns();

        $this->hiddenColumns = $this->getHiddenColumns();

        $this->processColumnInstance = $this->processColumnInstance();

        $this->processColumns = $this->processColumns();

        $this->processTotalableColumns = $this->processTotalableColumns();
    }

    abstract protected function query(Request $request): Builder;

    abstract public function columns(): array;

    abstract public function tableId(): string;

    public function builder(): self
    {
        $this->builder = $this->query($this->request);

        return $this;
    }

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

    public function hasToolbar(): bool
    {
        return TRUE;
    }

    public function exportTitle(): ?string
    {
        return NULL;
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

    public function callbacks(): ?array
    {
        return NULL;
    }

    public function setAttributes(array $attributes): self
    {
        $this->attributes = new Fluent($attributes);

        return $this;
    }

    public function editColumn($column, $callback, $condition = NULL): self
    {
        $this->editColumns[$column] = fn ($row) => $this->resolveColumnCallback($callback, $condition, $row);

        return $this;
    }

    public function addColumn($column, $callback, $condition = NULL): self
    {
        $this->addColumns[$column] = fn ($row) => $this->resolveColumnCallback($callback, $condition, $row);

        return $this;
    }

    public function tableTotalableHtml(): ?string
    {
        $columns = $this->processTotalableColumns;

        if ($columns->isEmpty()) {
            return NULL;
        }

        return view('snawbar-datatable::totalable.index', [
            'loadTotatableFunction' => $this->loadTotatableFunction(),
            'columns' => $columns,
        ])->render();
    }

    public function ajax(): JsonResponse
    {
        [$totalRecords, $aggregateQuery] = $this->prepareAggregateQuery();

        $rows = $this->prepareRows();

        return response()->json([
            'draw' => $this->request->input('draw', 1),
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $totalRecords,
            'totals' => $aggregateQuery,
            'data' => $rows,
        ]);
    }

    public function html(): string
    {
        return view('snawbar-datatable::table.index', [
            'tableId' => $this->tableId(),
            'jsSafeTableId' => $this->jsSafeTableId(),
            'tableClass' => $this->tableClass(),
            'isOrderable' => $this->isOrderable(),
            'defaultOrderBy' => $this->defaultOrderBy(),
            'callbackJs' => $this->callbackJs(),
            'length' => $this->length(),
            'shouldJumpToLastPage' => $this->shouldJumpToLastPage(),
            'columns' => $this->processColumns->values()->toJson(),
            'ajaxUrl' => $this->request->fullUrl(),
            'tableRedrawFunction' => $this->tableRedrawFunction(),
            'loadTotatableFunction' => $this->loadTotatableFunction(),
            'filterContainer' => $this->filterContainer(),
            'buttonPrintFunction' => $this->buttonPrintFunction(),
            'buttonExcelFunction' => $this->buttonExcelFunction(),
            'buttonColumnVisibilityFunction' => $this->buttonColumnVisibilityFunction(),
            'exportableModalId' => $this->exportableModalId(),
            'exportableModalHtml' => $this->exportableModalHtml(),
            'columnModalId' => $this->columnModalId(),
            'columnModalHtml' => $this->columnModalHtml(),
            'exportTitle' => $this->exportTitle(),
            'className' => static::class,
        ])->render();
    }

    public function buttonHtml(): ?string
    {
        if (! $this->hasToolbar()) {
            return NULL;
        }

        return view('snawbar-datatable::toolbar.buttons', [
            'exportableModalId' => $this->exportableModalId(),
            'columnModalId' => $this->columnModalId(),
            'buttonPrintFunction' => $this->buttonPrintFunction(),
            'buttonExcelFunction' => $this->buttonExcelFunction(),
        ])->render();
    }

    public function processColumns(): Collection
    {
        return $this->processColumnInstance
            ->filter(fn ($column) => $this->shouldIncludeColumn($column) && ! in_array($column->getData(), $this->hiddenColumns))
            ->when($this->request->hasAny(['print', 'excel']), fn ($columns) => $this->filterByRequestedColumns($columns))
            ->map(fn ($column) => (object) [
                'data' => $column->getData(),
                'title' => $column->getTitle(),
                'orderable' => $column->getOrderable(),
                'exportable' => $column->getExportable(),
                'visible' => $column->getVisible(),
                'responsivePriority' => $column->getResponsivePriority(),
                'className' => $column->getClassName(),
                'type' => $column->getType(),
            ]);
    }

    public function tableRedrawFunction(): string
    {
        return sprintf('%s_redraw()', $this->jsSafeTableId());
    }

    public function jsSafeTableId(): string
    {
        return str_replace('-', '_', $this->tableId());
    }

    private function processColumnInstance(): Collection
    {
        return collect($this->columns())->map(fn ($column) => $column instanceof Column ? $column : Column::make($column));
    }

    private function getHiddenColumns(): array
    {
        return DB::table('datatable_columns')
            ->where('datatable', $this->jsSafeTableId())
            ->pluck('column')
            ->toArray();
    }

    private function prepareRows(): Collection
    {
        $start = $this->request->input('start', 0);
        $length = $this->request->input('length', 10);

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
        $columns = $this->processTotalableColumns;

        $rawQuery = DB::query()
            ->fromSub($this->builder, 'totals')
            ->selectRaw('COUNT(*) as total_records')
            ->when($this->isTotalable(), fn ($q) => $q->addSelect($columns->whereNotNull('raw')->pluck('raw')->all()))
            ->first();

        $totalableResults = $this->isTotalable() ? $columns->mapWithKeys(fn ($column) => [
            $column->alias => [
                'title' => $column->title,
                'value' => ($column->resolve)($column->query ? ($column->query)() : $rawQuery->{$column->alias}),
            ],
        ]) : [];

        return [
            $rawQuery->total_records,
            $totalableResults,
        ];
    }

    private function processTotalableColumns(): Collection
    {
        return collect($this->totalableColumns())
            ->map(fn ($totalableColumn) => $totalableColumn instanceof Total ? $totalableColumn : Total::make($totalableColumn))
            ->filter(fn ($totalableColumn) => $this->shouldIncludeTotalableColumns($totalableColumn))
            ->map(fn ($totalableColumn) => (object) [
                'title' => $totalableColumn->getTitle(),
                'alias' => $totalableColumn->getAlias(),
                'raw' => $totalableColumn->getRawExpression(),
                'query' => $totalableColumn->getQuery(),
                'resolve' => fn ($value) => $totalableColumn->getFormatter() ? $totalableColumn->getFormatter()($value) : $value,
            ]);
    }

    private function shouldIncludeColumn($column): bool
    {
        $evaluate = fn ($value) => is_callable($value) ? $value() : $value;

        if ($evaluate($column->getVisible()) == FALSE) {
            return FALSE;
        }

        return ! ($this->request->hasAny(['print', 'excel']) && $evaluate($column->getExportable()) == FALSE);
    }

    private function shouldIncludeTotalableColumns($totalableColumn): bool
    {
        $evaluate = fn ($value) => is_callable($value) ? $value() : $value;

        if ($evaluate($totalableColumn->getVisible()) == FALSE) {
            return FALSE;
        }

        if ($totalableColumn->getRelatedColumn()) {
            return $this->processColumns->pluck('data')->contains($totalableColumn->getRelatedColumn());
        }

        return TRUE;
    }

    private function filterByRequestedColumns($columns): Collection
    {
        $requestColumns = explode(',', $this->request->input('columns', ''));

        return $columns->filter(fn ($column) => in_array($column->getData(), $requestColumns));
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

    private function callbackJs(): ?string
    {
        $callbacks = $this->callbacks() ?: [];

        return implode(", \n", array_map(fn ($name, $js) => sprintf('%s: %s,', $name, $js), array_keys($callbacks), $callbacks));
    }

    private function loadTotatableFunction(): string
    {
        return sprintf('%s_loadTotalable()', $this->jsSafeTableId());
    }

    private function defaultOrderByString(): string
    {
        return implode(' ', $this->defaultOrderBy());
    }

    private function isTotalable(): bool
    {
        return filled($this->request->input('totalable'));
    }

    private function buttonPrintFunction(): string
    {
        return sprintf('%s_print()', $this->jsSafeTableId());
    }

    private function buttonExcelFunction(): string
    {
        return sprintf('%s_excel()', $this->jsSafeTableId());
    }

    private function buttonColumnVisibilityFunction(): string
    {
        return sprintf('%s_column_visibility()', $this->jsSafeTableId());
    }

    private function exportableModalId(): string
    {
        return sprintf('%s_exportable_modal', $this->jsSafeTableId());
    }

    private function columnModalId(): string
    {
        return sprintf('%s_column_modal', $this->jsSafeTableId());
    }

    private function exportableModalHtml(): ?string
    {
        if (! $this->hasToolbar()) {
            return NULL;
        }

        $columns = $this->processColumns
            ->filter(fn ($column) => $column->exportable)
            ->map(fn ($column) => (object) [
                'data' => $column->data,
                'title' => $column->title,
                'checked' => ! in_array($column->data, $this->hiddenColumns),
            ]);

        if ($columns->isEmpty()) {
            return NULL;
        }

        return view('snawbar-datatable::modal.exportable', [
            'exportableModalId' => $this->exportableModalId(),
            'columns' => $columns,
        ])->render();
    }

    private function columnModalHtml(): ?string
    {
        if (! $this->hasToolbar()) {
            return NULL;
        }

        $columns = $this->processColumnInstance
            ->filter(fn ($column) => $this->shouldIncludeColumn($column))
            ->map(fn ($column) => (object) [
                'data' => $column->getData(),
                'title' => $column->getTitle(),
                'checked' => ! in_array($column->getData(), $this->hiddenColumns),
            ]);

        return view('snawbar-datatable::modal.column', [
            'buttonColumnVisibilityFunction' => $this->buttonColumnVisibilityFunction(),
            'columnModalId' => $this->columnModalId(),
            'columns' => $columns,
        ])->render();
    }

    private function resolveColumnCallback($callback, $condition, $row)
    {
        if (is_callable($condition) && ! $condition($row)) {
            return NULL;
        }

        $result = $callback($row);

        if ($result instanceof View) {
            return $result->render();
        }

        return $result;
    }
}
