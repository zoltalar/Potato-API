<?php

declare(strict_types = 1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLanguagesTable extends Migration
{
    public function up()
    {
        Schema::create('languages', function (Blueprint $table) {
            $table->smallIncrements('id');
            $table->string('name', 100)->unique();
            $table->string('native', 100);
            $table->char('code', 2)->unique();
            $table->boolean('system')->nullable();
            $table->boolean('active')->nullable();
        });
    }

    public function down()
    {
        Schema::dropIfExists('languages');
    }
}
