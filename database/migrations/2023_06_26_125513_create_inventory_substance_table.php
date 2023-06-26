<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('inventory_substance', function (Blueprint $table) {
            $table->unsignedInteger('inventory_id');
            $table->unsignedSmallInteger('substance_id');
            $table->decimal('value')->unsigned();
            $table->string('value_unit', 5)->nullable();

            $table->primary(['inventory_id', 'substance_id']);
            
            $table->foreign('inventory_id')
                ->references('id')
                ->on('inventory')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            
            $table->foreign('substance_id')
                ->references('id')
                ->on('substances')
                ->onDelete('cascade')
                ->onUpdate('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('inventory_substance');
    }
};
