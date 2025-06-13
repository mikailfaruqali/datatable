<?php

namespace Snawbar\DataTable\Console;

use Illuminate\Console\GeneratorCommand;

class MakeDataTableCommand extends GeneratorCommand
{
    protected $signature = 'make:datatable {name}';

    protected $defaultNamespace = 'App\\DataTables';

    public function handle()
    {
        $name = $this->qualifyClass($this->getNameInput());

        if ($this->alreadyExists($name)) {
            return $this->error('DataTable already exists!');
        }

        parent::handle();

        return $this->info('DataTable created successfully.');
    }

    protected function getStub()
    {
        return __DIR__ . '/../../stubs/datatable.stub';
    }

    protected function getDefaultNamespace($rootNamespace)
    {
        return $this->defaultNamespace;
    }
}
