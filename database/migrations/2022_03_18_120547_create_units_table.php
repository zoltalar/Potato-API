<?php

declare(strict_types = 1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('units', function (Blueprint $table) {
            $table->smallIncrements('id');
            $table->string('abbreviation', 5)->unique();
            $table->string('name', 50)->unique();
            $table->tinyInteger('type')->unsigned();
            $table->tinyInteger('system')->unsigned()->nullable();

            $table->index(['abbreviation', 'name']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('units');
    }
};
