<?php

declare(strict_types = 1);

use App\Models\Base;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('categories', function (Blueprint $table) {
            $length = Base::DEFAULT_STRING_LENGTH;

            $table->increments('id');
            $table->string('name', $length);
            $table->smallInteger('type')->unsigned();
            $table->smallInteger('list_order')->unsigned()->nullable();
            $table->boolean('system')->nullable();
            $table->boolean('active')->nullable();

            $table->index(['name']);
            $table->unique(['name', 'type']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('categories');
    }
};
