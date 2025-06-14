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
