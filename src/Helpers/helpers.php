<?php

use Snawbar\DataTable\Services\DatatableProcess;

function datatableProcess($datatables): DatatableProcess
{
    return new DatatableProcess($datatables);
}

function assetOrUrl($path): ?string
{
    if (blank($path)) {
        return NULL;
    }

    return filter_var($path, FILTER_VALIDATE_URL) ? $path : asset($path);
}

function datatableWhen($condition, $true, $false = NULL): string
{
    $result = $condition ? $true : $false;

    return is_callable($result) ? $result() : (string) $result;
}

function datatablePrintHtml($html): void
{
    echo $html;
}

function datatableChecked($condition): string
{
    return $condition ? 'checked' : '';
}
