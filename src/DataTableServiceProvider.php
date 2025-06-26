<?php

namespace Snawbar\DataTable;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use Snawbar\DataTable\Console\MakeDataTableCommand;

class DataTableServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->directives();
        $this->publishAssets();
    }

    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/datatable.php', 'snawbar-datatable');
        $this->loadTranslationsFrom(__DIR__ . '/../lang', 'snawbar-datatable');
        $this->loadViewsFrom(__DIR__ . '/../views', 'snawbar-datatable');
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
                __DIR__ . '/../lang' => resource_path('lang/vendor/snawbar-datatable'),
                __DIR__ . '/../views' => resource_path('views/vendor/snawbar-datatable'),
            ], 'snawbar-datatable-assets');
        }
    }

    private function directives()
    {
        Blade::directive('datatableRowSpace', fn ($expression) => str_repeat('<tr><td colspan="100%" class="border-none"></td></tr>', $expression));

        Blade::directive('datatableCss', fn () => "<?php
            \$links = [];

            foreach (config('snawbar-datatable.datatable-css', []) as \$url) {
                \$links[] = sprintf('<link href=\"%s\" rel=\"stylesheet\">', e(assetOrUrl(\$url)));
            }

            echo implode(\"\\n\", \$links);
        ?>");

        Blade::directive('datatableJs', fn () => "<?php
            \$scripts = [];

            foreach (config('snawbar-datatable.datatable-js', []) as \$url) {
                \$scripts[] = sprintf('<script src=\"%s\"></script>', e(assetOrUrl(\$url)));
            }

            echo implode(\"\\n\", \$scripts);
        ?>");

        Blade::directive('datatablePrintCss', fn () => "<?php
            \$links = [];

            foreach (config('snawbar-datatable.datatable-print-css', []) as \$url) {
                \$links[] = sprintf('<link href=\"%s\" rel=\"stylesheet\">', e(assetOrUrl(\$url)));
            }

            echo implode(\"\\n\", \$links);
        ?>");
    }
}
