<?php

use Snawbar\DataTable\DatatableProcess;

function datatableProcess(array $datatables): DatatableProcess
{
    return new DatatableProcess($datatables);
}
