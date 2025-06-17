<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Table CSS Classes
    |--------------------------------------------------------------------------
    |
    | These classes will be applied to the <table> element by default.
    | You can override this value when rendering the <x-datatable> component
    | by passing the "class" attribute manually.
    |
    */

    'table-style' => 'table table-bordered w-100',

    /*
    |--------------------------------------------------------------------------
    | Font URL
    |--------------------------------------------------------------------------
    |
    | This font will be optionally loaded in your layout or datatable component.
    | You can disable it by setting this value to null or removing the usage
    | in your Blade files. This example uses Roboto from Google Fonts.
    |
    */

    'font' => NULL,

    /*
    |--------------------------------------------------------------------------
    | Total Template
    |--------------------------------------------------------------------------
    |
    | This template wraps all total items in a container.
    | The ":items" placeholder will be replaced with all total item HTML.
    |
    */

    'totalable-template' => <<<'HTML'
        <div class="row">
            :items
        </div>
    HTML,

    /*
    |--------------------------------------------------------------------------
    | Total Item Template
    |--------------------------------------------------------------------------
    |
    | This template is used for each total item.
    | The ":title" and ":value" placeholders will be replaced with real data.
    |
    */

    'totalable-item-template' => <<<'HTML'
        <div class="col">
            <div class="border p-2 rounded d-flex justify-content-between">
                <span>:title</span> 
                <strong id=":key"></strong> 
            </div>
        </div>
    HTML,
];
