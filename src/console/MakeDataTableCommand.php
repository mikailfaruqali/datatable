<?php

namespace Snawbar\DataTable\Console;

use Illuminate\Console\GeneratorCommand;

class MakeDataTableCommand extends GeneratorCommand
{
    protected $signature = 'make:datatable {name}';

    protected function getStub()
    {
        return __DIR__ . '/../../stubs/datatable.stub';
    }

    protected function getDefaultNamespace($rootNamespace)
    {
        return sprintf('%s\\Components\\DataTables', $rootNamespace);
    }
}
