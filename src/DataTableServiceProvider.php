<?php

namespace Snawbar\DataTable;

use Illuminate\Support\ServiceProvider;

class DataTableServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->publishAssets();
    }

    private function publishAssets()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/datatables.php' => config_path('snawbar-datatables.php'),
            ], 'snawbar-datatables-config');
        }
    }
}
