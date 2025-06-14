<?php

namespace Snawbar\DataTable;

class DatatableProcess
{
    private array $tables = [];

    public function __construct(array $datatableClassesOrInstances)
    {
        $this->initializeTables($datatableClassesOrInstances);
    }

    public function render(?string $view = NULL)
    {
        return request()->ajax() ? $this->handleAjax() : $this->renderView($view);
    }

    private function initializeTables(array $datatables): void
    {
        $this->tables = array_map(fn ($dt) => $this->resolveDatatable($dt, request()), $datatables);
    }

    private function resolveDatatable(mixed $datatable, $request): object
    {
        return is_string($datatable) && class_exists($datatable) ? new $datatable($request) : $datatable;
    }

    private function handleAjax()
    {
        return collect($this->tables)
            ->first(fn ($t) => $t->jsSafeTableId() === request('table_id'))
            ?->ajax()
            ?? abort(404, 'Unknown datatable requested');
    }

    private function renderView(?string $view = NULL)
    {
        $tablesHtml = $this->renderTables();

        return is_null($view) ? $tablesHtml : view($view, [
            'datatables' => $tablesHtml,
        ]);
    }

    private function renderTables()
    {
        return collect($this->tables)->mapWithKeys(fn ($table) => [
            $table->jsSafeTableId() => [
                'tableRedraw' => $table->tableRedrawFunctionString(),
                'datatable' => $table->html(),
                'tableId' => $table->tableId(),
            ],
        ]);
    }
}
