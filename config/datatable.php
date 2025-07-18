<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Session Key for Direction
    |--------------------------------------------------------------------------
    |
    | This key is used to store and retrieve the text direction (e.g., 'ltr' or 'rtl')
    | in the session. It helps maintain consistent directionality across requests.
    |
    */
    'local-direction-session-key' => 'direction',

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
    | CSS Assets URLs (DataTables, Buttons, etc.)
    |--------------------------------------------------------------------------
    */

    'datatable-css' => [
        //
    ],

    /*
    |--------------------------------------------------------------------------
    | JS Assets URLs (jQuery, DataTables, Buttons, etc.)
    |--------------------------------------------------------------------------
    */

    'datatable-js' => [
        //
    ],

    /*
    |--------------------------------------------------------------------------
    | Print View CSS (Used for window.print())
    |--------------------------------------------------------------------------
    |
    | These styles will be injected when printing the DataTable using the
    | print button. Add any external stylesheets or inline CSS that
    | should be applied to the print version of the table.
    |
    */

    'datatable-print-css' => [
        //
    ],
];
