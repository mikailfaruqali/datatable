<?php

namespace Snawbar\DataTable;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use Snawbar\DataTable\Console\MakeDataTableCommand;

class DataTableServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->macros();
        $this->publishAssets();
    }

    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/datatable.php', 'snawbar-datatable');
        $this->loadTranslationsFrom(__DIR__ . '/../resources/lang', 'snawbar-datatable');
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'snawbar-datatable');

        $this->commands([
            MakeDataTableCommand::class,
        ]);
    }

    private function publishAssets()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/datatable.php' => config_path('snawbar-datatable.php'),
            ], 'snawbar-datatable-assets');
        }
    }

    private function macros()
    {
        Blade::directive('datatableRowSpace', fn ($expression) => str_repeat('<tr><td colspan="100%" class="border-none"></td></tr>', $expression));
    }
}
