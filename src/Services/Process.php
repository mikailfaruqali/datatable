<?php

namespace Snawbar\DataTable\Services;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Facades\Excel;
use Snawbar\DataTable\Export\Exportable;

class Process
{
    protected Request $request;

    private array $tables = [];

    public function __construct(Request $request, $datatableClassesOrInstances)
    {
        $this->request = $request;

        $this->initializeTables($datatableClassesOrInstances);
    }

    public function view($view = NULL, array $data = [])
    {
        if ($this->request->ajax() || $this->request->hasAny(['print', 'excel'])) {
            return $this->handleAjax();
        }

        return $this->renderView($view, $data);
    }

    public function with(array $attributes): self
    {
        array_map(fn ($table) => $table->setAttributes($attributes), $this->tables);

        return $this;
    }

    private function handleAjax()
    {
        $datatable = collect($this->tables)->first(fn ($table) => $table->jsSafeTableId() === $this->request->input('tableId'))->builder();

        if ($this->request->ajax()) {
            return $datatable->ajax();
        }

        throw_if(blank($datatable->exportTitle()), Exception::class, 'Export title is not set for the datatable');

        if ($this->request->has('print')) {
            return $this->handlePrintPage($datatable);
        }

        if ($this->request->has('excel')) {
            return $this->handleExcelExport($datatable);
        }

        return NULL;
    }

    private function handlePrintPage($datatable)
    {
        $headers = $datatable->processColumns->pluck('title', 'data');
        $response = $datatable->ajax()->getData();

        $rows = collect($response->data)->map(fn ($row) => (object) $this->formatPrintRow($row, $headers));

        return view('snawbar-datatable::export.print', [
            'rows' => $rows,
            'headers' => $headers->values()->all(),
            'title' => $datatable->exportTitle(),
            'totals' => (array) $response->totals,
        ]);
    }

    private function formatPrintRow($row, $headers): array
    {
        return $headers->mapWithKeys(fn ($title, $key) => [$title => strip_tags((string) $row->{$key})])->merge(collect($row)->only(['subItems']))->all();
    }

    private function handleExcelExport($datatable)
    {
        $columns = $datatable->processColumns;
        $headers = $columns->pluck('title', 'data');
        $response = $datatable->ajax()->getData();

        $rows = collect($response->data)->map(fn ($row) => $this->formatExcelRow($row, $columns))->toArray();

        return Excel::download(new Exportable(
            $rows,
            $headers->values()->all(),
            $columns->toArray(),
            $datatable->exportTitle(),
            $this->formatTotalsForExport((array) $response->totals),
        ), sprintf('%s.xlsx', $datatable->exportTitle()));
    }

    private function formatExcelRow($row, $columns): Collection
    {
        return $columns->mapWithKeys(fn ($column) => [
            $column->title => $this->formatColumnType($column->type, strip_tags((string) $row->{$column->data})),
        ]);
    }

    private function formatColumnType($type, $value): string
    {
        switch ($type) {
            case 'number':
            case 'float':
                return datatableNumberPatch($value);
            default:
                return (string) $value;
        }
    }

    private function formatTotalsForExport(array $totals): array
    {
        return array_combine(array_column($totals, 'title'), array_map(fn ($total) => datatableNumberPatch($total->value), $totals));
    }

    private function renderView($view = NULL, array $data = [])
    {
        $tables = $this->renderTables();

        return is_null($view) ? $tables : view($view, $tables->toArray() + $data);
    }

    private function renderTables()
    {
        return collect($this->tables)->mapWithKeys(fn ($table) => [
            $table->jsSafeTableId() => (object) [
                'tableRedraw' => $table->tableRedrawFunction(),
                'tableTotalableHtml' => $table->tableTotalableHtml(),
                'buttonHtml' => $table->buttonHtml(),
                'datatable' => $table->html(),
                'tableId' => $table->tableId(),
            ],
        ]);
    }

    private function initializeTables($datatables): void
    {
        $this->tables = array_map(fn ($datatable) => $this->resolveDatatable($datatable, $this->request), is_array($datatables) ? $datatables : [$datatables]);
    }

    private function resolveDatatable($datatable, $request): object
    {
        return is_string($datatable) && class_exists($datatable) ? new $datatable($request) : $datatable;
    }
}
