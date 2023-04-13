<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('currencies', function (Blueprint $table) {
            $table->smallIncrements('id');
            $table->string('name', 50)->unique();
            $table->char('code', 3)->unique();
            $table->string('symbol', 2);
            $table->smallInteger('number')->unsigned()->nullable();

            $table->index(['name', 'code', 'symbol', 'number']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('currencies');
    }
};
