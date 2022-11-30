<?php

declare(strict_types = 1);

use App\Models\Base;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEventsTable extends Migration
{
    public function up()
    {
        Schema::create('events', function (Blueprint $table) {
            $length = Base::DEFAULT_STRING_LENGTH;

            $table->id();
            $table->string('title', $length);
            $table->string('website', $length)->nullable();
            $table->string('phone', 20)->nullable();
            $table->string('email', $length)->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            $table->text('description')->nullable();
            $table->tinyInteger('status')->unsigned()->nullable();
            $table->bigInteger('eventable_id')->unsigned()->nullable();
            $table->string('eventable_type', 100)->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('events');
    }
}
