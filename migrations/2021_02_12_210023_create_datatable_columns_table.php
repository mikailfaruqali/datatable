<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('datatable_columns', function (Blueprint $blueprint) {
            $blueprint->string('datatable', 255);
            $blueprint->string('column', 40);
            $blueprint->foreignId('user_id')->references('id')->on('users')->cascadeOnDelete();
            $blueprint->primary(['datatable', 'column', 'user_id']);
        });
    }
};
