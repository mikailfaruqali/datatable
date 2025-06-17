<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

Route::post('/datatable/columns', function (Request $request) {
    DB::table('datatable_columns')
        ->where('datatable', $request->tableId)
        ->where('user_id', auth()->id())
        ->delete();

    collect((new $request->className($request))->columns())
        ->reject(fn ($col) => in_array($col['data'], $request->columns ?? []))
        ->values()
        ->each(fn ($column) => DB::table('datatable_columns')->updateOrInsert([
            'datatable' => $request->tableId,
            'column' => $column['data'],
            'user_id' => auth()->id(),
        ]));
})->middleware('auth')->name('datatable.columns');
