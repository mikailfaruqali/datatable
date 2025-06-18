<?php

namespace Snawbar\DataTable\Services;

use Exception;
use Maatwebsite\Excel\Facades\Excel;
use Snawbar\DataTable\Export\Exportable;

class Process
{
    private array $tables = [];

    public function __construct($datatableClassesOrInstances)
    {
        $this->initializeTables($datatableClassesOrInstances);
    }

    public function view($view = NULL, $data = [])
    {
        if (request()->ajax() || request()->hasAny(['print', 'excel'])) {
            return $this->handleAjax();
        }

        return $this->renderView($view, $data);
    }

    private function initializeTables($datatables): void
    {
        $this->tables = array_map(fn ($datatable) => $this->resolveDatatable($datatable, request()), is_array($datatables) ? $datatables : [$datatables]);
    }

    private function resolveDatatable($datatable, $request): object
    {
        return is_string($datatable) && class_exists($datatable) ? new $datatable($request) : $datatable;
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

    private function renderView($view = NULL, $data = [])
    {
        return is_null($view) ? $this->renderTables() : view($view, $this->renderTables()->toArray() + $data);
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

    private function handlePrintPage($datatable)
    {
        $exportColumns = $this->exportColumns($datatable);
        $responseData = $this->exportData($datatable);

        return view('snawbar-datatable::export.print', [
            'rows' => $this->exportDataForColumns($responseData->data, $exportColumns),
            'headers' => $this->getExportHeaders($exportColumns),
            'title' => $datatable->exportTitle(),
            'totals' => (array) $responseData->totals,
        ]);
    }

    private function handleExcelExport($datatable)
    {
        $exportColumns = $this->exportColumns($datatable);
        $responseData = $this->exportData($datatable);

        return Excel::download(new Exportable(
            $this->exportDataForColumns($responseData->data, $exportColumns),
            $this->getExportHeaders($exportColumns),
        ), sprintf('%s.xlsx', $datatable->exportTitle()));
    }

    private function exportData($datatable)
    {
        return $datatable->ajax()->getData();
    }

    private function exportDataForColumns($data, $exportColumns = NULL)
    {
        return $this->mapDataKeysToTitles(collect($data), $exportColumns);
    }

    private function exportColumns($datatable)
    {
        return $datatable->processColumns()->pluck('title', 'data');
    }

    private function mapDataKeysToTitles($rows, $columns): array
    {
        return $rows->map(fn ($row) => $columns->mapWithKeys(fn ($title, $key) => [$title => $row->{$key}]))->toArray();
    }

    private function getExportHeaders($columns): array
    {
        return $columns->values()->all();
    }
}
