<?php

declare(strict_types = 1);

use App\Models\Base;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInventoryTable extends Migration
{
    public function up()
    {
        Schema::create('inventory', function (Blueprint $table) {
            $length = Base::DEFAULT_STRING_LENGTH;

            $table->increments('id');
            $table->string('name', $length);
            $table->unsignedInteger('category_id')->nullable();
            $table->string('photo', 40)->nullable();
            $table->boolean('system')->nullable();

            $table->index(['name']);

            $table->unique(['name', 'category_id']);

            $table->foreign('category_id')
                ->references('id')
                ->on('categories')
                ->onDelete('set null')
                ->onUpdate('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('inventory');
    }
}
