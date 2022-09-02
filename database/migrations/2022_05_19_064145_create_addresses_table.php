<?php

declare(strict_types = 1);

use App\Models\Base;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAddressesTable extends Migration
{
    public function up()
    {
        Schema::create('addresses', function (Blueprint $table) {
            $length = Base::DEFAULT_STRING_LENGTH;

            $table->id();
            $table->string('address', 100);
            $table->string('address_2', 100)->nullable();
            $table->string('city', 60);
            $table->integer('state_id')->unsigned()->nullable();
            $table->string('zip', 15);
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->string('timezone', 35)->nullable();
            $table->text('directions')->nullable();
            $table->string('stand', $length)->nullable();
            $table->tinyInteger('type')->unsigned()->nullable();
            $table->bigInteger('addressable_id')->unsigned()->nullable();
            $table->string('addressable_type', 100)->nullable();
            $table->timestamps();

            $table->index(['address', 'address_2', 'city', 'zip']);

            $table->unique(['type', 'addressable_id', 'addressable_type']);

            $table->foreign('state_id')
                ->references('id')
                ->on('states')
                ->onDelete('set null')
                ->onUpdate('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('addresses');
    }
}
