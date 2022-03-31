<?php

declare(strict_types = 1);

use App\Models\Base;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAdminsTable extends Migration
{
    public function up()
    {
        Schema::create('admins', function (Blueprint $table) {
            $length = Base::DEFAULT_STRING_LENGTH;

            $table->id();
            $table->string('first_name', 100);
            $table->string('last_name', 100);
            $table->string('email', $length)->unique();
            $table->string('phone', 20)->nullable();
            $table->string('password');
            $table->boolean('system')->nullable();
            $table->boolean('active')->nullable();
            $table->rememberToken();
            $table->timestamps();

            $table->index(['first_name', 'last_name', 'email', 'phone']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('admins');
    }
}
