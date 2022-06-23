<?php

declare(strict_types = 1);

use App\Models\Base;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReviewsTable extends Migration
{
    public function up()
    {
        Schema::create('reviews', function (Blueprint $table) {
            $length = Base::DEFAULT_STRING_LENGTH;

            $table->id();
            $table->string('title', $length)->nullable();
            $table->text('content');
            $table->tinyInteger('rating')->unsigned();
            $table->boolean('active')->default(0)->nullable();
            $table->bigInteger('rateable_id')->unsigned()->nullable();
            $table->string('rateable_type', 100)->nullable();
            $table->bigInteger('user_id')->unsigned();
            $table->timestamps();

            $table->unique(['user_id', 'rateable_id', 'rateable_type']);

            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade')
                ->onUpdate('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('reviews');
    }
}
