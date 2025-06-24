<?php

namespace Snawbar\DataTable\Services;

use Exception;
use Illuminate\Support\Fluent;
use Maatwebsite\Excel\Facades\Excel;
use Snawbar\DataTable\Export\Exportable;

class Process
{
    private array $tables = [];

    private array $attributes = [];

    public function __construct($datatableClassesOrInstances)
    {
        $this->initializeTables($datatableClassesOrInstances);
    }

    public function view($view = NULL, array $data = [])
    {
        if (request()->ajax() || request()->hasAny(['print', 'excel'])) {
            return $this->handleAjax();
        }

        return $this->renderView($view, $data);
    }

    public function with(array $attributes): self
    {
        $this->attributes = array_merge($this->attributes, $attributes);

        return $this;
    }

    private function handleAjax()
    {
        $datatable = collect($this->tables)->first(fn ($table) => $table->jsSafeTableId() === request('tableId'));

        if (request()->ajax()) {
            return $datatable->ajax();
        }

        throw_if(blank($datatable->exportTitle()), new Exception('Export title is not set for the datatable'));

        if (request()->has('print')) {
            return $this->handlePrintPage($datatable);
        }

        if (request()->has('excel')) {
            return $this->handleExcelExport($datatable);
        }

        return NULL;
    }

    private function handlePrintPage($datatable)
    {
        $columns = $this->exportColumns($datatable);
        $response = $this->exportData($datatable);

        return view('snawbar-datatable::export.print', [
            'rows' => $this->mapDataToColumns($response->data, $columns),
            'headers' => $columns->values()->all(),
            'title' => $datatable->exportTitle(),
            'totals' => (array) $response->totals,
        ]);
    }

    private function handleExcelExport($datatable)
    {
        $columns = $this->exportColumns($datatable);
        $response = $this->exportData($datatable);

        return Excel::download(new Exportable(
            $this->mapDataToColumns($response->data, $columns),
            $columns->values()->all()
        ), sprintf('%s.xlsx', $datatable->exportTitle()));
    }

    private function exportData($datatable)
    {
        return $datatable->ajax()->getData();
    }

    private function mapDataToColumns($data, $columns): array
    {
        return collect($data)
            ->map(fn ($row) => $columns->mapWithKeys(fn ($title, $key) => [$title => $row->{$key}]))
            ->toArray();
    }

    private function exportColumns($datatable)
    {
        return $datatable->processColumns->pluck('title', 'data');
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
        $datatables = is_array($datatables) ? $datatables : [$datatables];

        $this->tables = array_map([$this, 'makeDatatableInstance'], $datatables);
    }

    private function makeDatatableInstance($datatable)
    {
        $instance = $this->resolveDatatable($datatable, request());

        $instance->attributes = new Fluent($this->attributes);

        return $instance;
    }

    private function resolveDatatable($datatable, $request): object
    {
        return is_string($datatable) && class_exists($datatable) ? new $datatable($request) : $datatable;
    }
}
