<?php

declare(strict_types = 1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCountriesTable extends Migration
{
    public function up()
    {
        Schema::create('countries', function (Blueprint $table) {
            $table->smallIncrements('id');
            $table->string('name', 40)->unique();
            $table->string('native', 50)->nullable();
            $table->char('code', 2)->unique();
            $table->string('date_format', 10)->nullable();
            $table->string('time_format', 10)->nullable();
            $table->boolean('system')->nullable();
            $table->boolean('active')->nullable();

            $table->index(['name', 'native', 'code']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('countries');
    }
}
