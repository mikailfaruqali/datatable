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
    | Default Pagination Length
    |--------------------------------------------------------------------------
    |
    | This value sets the default number of rows shown per page in the DataTable.
    | It will be used when no `length` parameter is passed in the request.
    |
    */

    'default-length' => 10,
];
