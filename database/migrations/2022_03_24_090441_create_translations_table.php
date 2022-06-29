<?php

declare(strict_types = 1);

use App\Models\Base;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTranslationsTable extends Migration
{
    public function up()
    {
        Schema::create('translations', function (Blueprint $table) {
            $length = Base::DEFAULT_STRING_LENGTH;

            $table->increments('id');
            $table->string('name', $length);
            $table->unsignedSmallInteger('language_id')->nullable();
            $table->unsignedInteger('translatable_id')->nullable();
            $table->string('translatable_type', 100)->nullable();

            $table->index(['name']);

            $table->unique(['language_id', 'translatable_id', 'translatable_type'], 'fk_translations_language_translatable');

            $table->foreign('language_id')
                ->references('id')
                ->on('languages')
                ->onDelete('cascade')
                ->onUpdate('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('translations');
    }
}
