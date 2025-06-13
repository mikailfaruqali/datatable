<?php

namespace Snawbar\DataTable;

use Illuminate\Support\ServiceProvider;

class DataTableServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->publishAssets();
    }

    public function register()
    {
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'snawbar-datatable');
    }

    private function publishAssets()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/datatable.php' => config_path('snawbar-datatable.php'),
            ], 'snawbar-datatable-config');

            $this->publishes([
                __DIR__ . '/../resources/lang' => resource_path('lang/vendor/snawbar-datatable'),
            ], 'snawbar-datatable-lang');
        }
    }
}
