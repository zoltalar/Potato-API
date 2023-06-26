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
        Schema::create('substances', function (Blueprint $table) {
            $length = Base::DEFAULT_STRING_LENGTH;
            
            $table->smallIncrements('id');
            $table->string('name', $length)->unique();
            $table->smallInteger('list_order')->unsigned()->nullable();
            $table->boolean('active')->nullable();
        });
    }

    public function down()
    {
        Schema::dropIfExists('substances');
    }
};
