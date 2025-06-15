<?php

use Snawbar\DataTable\DatatableProcess;

function datatableProcess($datatables): DatatableProcess
{
    return new DatatableProcess($datatables);
}

function asset_or_url(?string $path)
{
    if (blank($path)) {
        return NULL;
    }

    return filter_var($path, FILTER_VALIDATE_URL) ? $path : asset($path);
}

function datatable_when($condition, callable|string $true, callable|string|null $false = NULL): string
{
    $result = $condition ? $true : $false;

    return is_callable($result) ? $result() : (string) $result;
}

function datatable_print_html($html): void
{
    echo $html;
}
