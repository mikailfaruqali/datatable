<?php

use Snawbar\DataTable\DatatableProcess;

function datatableProcess($datatables): DatatableProcess
{
    return new DatatableProcess($datatables);
}
