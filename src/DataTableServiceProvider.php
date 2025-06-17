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
        $this->loadMigrationsFrom(__DIR__ . '/../migrations');
        $this->loadRoutesFrom(__DIR__ . '/../routes/web.php');

        $this->commands([
            MakeDataTableCommand::class,
        ]);
    }

    private function publishAssets()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/datatable.php' => config_path('snawbar-datatable.php'),
                __DIR__ . '/../resources/lang' => resource_path('lang/vendor/snawbar-datatable'),
                __DIR__ . '/../resources/views' => resource_path('views/vendor/snawbar-datatable'),
            ], 'snawbar-datatable-assets');
        }
    }

    private function macros()
    {
        Blade::directive('datatableRowSpace', fn ($expression) => str_repeat('<tr><td colspan="100%" class="border-none"></td></tr>', $expression));
    }
}
