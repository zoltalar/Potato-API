<?php

declare(strict_types = 1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('prices', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('productable_id')->unsigned()->nullable();
            $table->string('productable_type', 100)->nullable();
            $table->integer('inventory_id')->unsigned();
            $table->date('date');
            $table->double('price', 10, 2)->unsigned()->nullable();
            $table->smallInteger('currency_id')->unsigned()->nullable();
            $table->string('price_unit', 5)->nullable();

            $table->unique(['productable_id', 'productable_type', 'inventory_id', 'date'], 'price_analytics_unique');

            $table->foreign('inventory_id')
                ->references('id')
                ->on('inventory')
                ->onDelete('cascade')
                ->onUpdate('cascade');

            $table->foreign('currency_id')
                ->references('id')
                ->on('currencies')
                ->onDelete('set null')
                ->onUpdate('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('prices');
    }
};
