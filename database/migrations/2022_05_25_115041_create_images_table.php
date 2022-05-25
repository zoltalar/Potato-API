<?php

declare(strict_types = 1);

use App\Models\Base;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateImagesTable extends Migration
{
    public function up()
    {
        Schema::create('images', function (Blueprint $table) {
            $length = Base::DEFAULT_STRING_LENGTH;

            $table->id();
            $table->string('title', $length)->nullable();
            $table->string('file', 40);
            $table->json('variations')->nullable();
            $table->string('mime', 127)->nullable();
            $table->bigInteger('size')->unsigned()->nullable();
            $table->boolean('primary')->default(0)->nullable();
            $table->boolean('cover')->default(0)->nullable();
            $table->bigInteger('imageable_id')->unsigned()->nullable();
            $table->string('imageable_type', 100)->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('images');
    }
}
