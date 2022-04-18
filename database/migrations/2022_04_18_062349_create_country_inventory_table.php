<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCountryInventoryTable extends Migration
{
    public function up()
    {
        Schema::create('country_inventory', function (Blueprint $table) {
            $table->unsignedSmallInteger('country_id');
            $table->unsignedInteger('inventory_id');

            $table->primary(['country_id', 'inventory_id']);

            $table->foreign('country_id')
                ->references('id')
                ->on('countries')
                ->onDelete('cascade')
                ->onUpdate('cascade');

            $table->foreign('inventory_id')
                ->references('id')
                ->on('inventory')
                ->onDelete('cascade')
                ->onUpdate('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('country_inventory');
    }
}
