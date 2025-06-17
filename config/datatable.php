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
        <div class="row align-items-center mb-2">
            <div class="col-lg-12 col-xl-12">
                <div class="row g-1 align-items-center">
                    :items
                </div>
            </div>
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
            <div class="card shadow-lg h-100">
                <div class="card-body rounded p-2 text-truncate d-flex flex-column justify-content-center align-items-center">
                    <h6 class="card-title mb-2 text-center text-truncate">:title</h6>
                    <div id=":alias" class="d-flex justify-content-center align-items-center">
                        <a href="javascript:void(0)" onclick=":load_function" class="btn btn-link p-0">
                            :load_text
                        </a>
                    </div>
                </div>
            </div>
        </div>
    HTML,
];
