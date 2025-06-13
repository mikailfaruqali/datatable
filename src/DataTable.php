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

    private bool $iteration = FALSE;

    private Builder $builder;

    public function __construct(Request $request)
    {
        $this->builder = $this->query($request);
        $this->request = $request;
    }

    abstract protected function query(Request $request): Builder;

    abstract public function columns(): array;

    abstract public function tableClass(): ?string;

    public function tableId(): string
    {
        return 'datatable';
    }

    public function length(): int
    {
        return config()->int('snawbar-datatable.default-length');
    }

    public function isOrderable(): bool
    {
        return FALSE;
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

    public function iteration()
    {
        $this->iteration = TRUE;

        return $this;
    }

    public function withData($data)
    {
        $this->additionalData = is_array($data) ? array_merge($this->additionalData, $data) : [$data];

        return $this;
    }

    public function make()
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

    public function render()
    {
        if ($this->request->ajax()) {
            return $this->make();
        }

        return [
            'html' => $this->renderHtml(),
        ];
    }

    private function renderHtml()
    {
        return view('snawbar-datatable::table-builder', [
            'tableId' => $this->tableId(),
            'tableClass' => $this->tableClass(),
            'isOrderable' => $this->isOrderable(),
            'length' => $this->length(),
            'columns' => $this->columns(),
            'ajaxUrl' => $this->request->fullUrl(),
        ])->render();
    }

    private function prepareRows()
    {
        $start = request()->input('start', 0);
        $length = request()->input('length', 10);

        $rows = $this->builder->skip($start)->take($length)->get();

        $rows->each(function ($row, $index) use ($start) {
            if ($this->iteration) {
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
}
