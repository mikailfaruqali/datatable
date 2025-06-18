<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Snawbar\DataTable\Services\Column;

Route::post('/datatable/columns', function (Request $request) {
    $request->validate([
        'tableId' => 'required|string',
        'className' => 'required|string',
        'columns' => 'nullable|array',
    ]);

    abort_unless(class_exists($request->className), 404, 'Class not found');

    DB::table('datatable_columns')
        ->where('datatable', $request->tableId)
        ->where('user_id', auth()->id())
        ->delete();

    $instance = new $request->className($request);

    collect($instance->columns())
        ->map(fn ($column) => $column instanceof Column ? $column : Column::make($column))
        ->reject(fn ($column) => in_array($column->getData(), $request->columns ?? []))
        ->values()
        ->each(fn ($column) => DB::table('datatable_columns')->updateOrInsert([
            'datatable' => $request->tableId,
            'column' => $column->getData(),
            'user_id' => auth()->id(),
        ]));

    return response()->json([
        'message' => 'Columns updated successfully',
    ]);
})->middleware('auth')->name('datatable.columns');
