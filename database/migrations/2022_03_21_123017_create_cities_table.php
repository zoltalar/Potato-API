<?php

declare(strict_types = 1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCitiesTable extends Migration
{
    public function up()
    {
        Schema::create('cities', function (Blueprint $table) {
            $table->id();
            $table->string('name', 60);
            $table->string('name_ascii', 60)->nullable();
            $table->text('zips')->nullable();
            $table->integer('population')->unsigned()->nullable();
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->string('timezone', 35)->nullable();
            $table->integer('state_id')->unsigned();

            $table->index(['name', 'name_ascii']);
            $table->unique(['name', 'state_id']);

            $table->foreign('state_id')
                ->references('id')
                ->on('states')
                ->onDelete('cascade')
                ->onUpdate('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('cities');
    }
}
