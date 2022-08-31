<?php

declare(strict_types = 1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOperatingHoursTable extends Migration
{
    public function up()
    {
        Schema::create('operating_hours', function (Blueprint $table) {
            $table->id();
            $table->tinyInteger('type')->unsigned()->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->tinyInteger('start_month')->unsigned()->nullable();
            $table->tinyInteger('end_month')->unsigned()->nullable();
            $table->json('monday')->nullable();
            $table->json('tuesday')->nullable();
            $table->json('wednesday')->nullable();
            $table->json('thursday')->nullable();
            $table->json('friday')->nullable();
            $table->json('saturday')->nullable();
            $table->json('sunday')->nullable();
            $table->text('exceptions')->nullable();
            $table->bigInteger('operatable_id')->unsigned()->nullable();
            $table->string('operatable_type', 100)->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('operating_hours');
    }
}
