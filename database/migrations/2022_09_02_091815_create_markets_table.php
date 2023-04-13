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
        Schema::create('markets', function (Blueprint $table) {
            $length = Base::DEFAULT_STRING_LENGTH;

            $table->id();
            $table->string('name', $length);
            $table->string('first_name', 100)->nullable();
            $table->string('last_name', 100)->nullable();
            $table->string('phone', 20)->nullable();
            $table->boolean('publish_phone')->nullable();
            $table->boolean('publish_address')->nullable();
            $table->boolean('publish_mailing_address')->nullable();
            $table->string('fax', 20)->nullable();
            $table->string('email', $length)->nullable();
            $table->string('website', $length)->nullable();
            $table->text('description')->nullable();
            $table->string('facebook', 100)->nullable();
            $table->string('twitter', 100)->nullable();
            $table->string('pinterest', 100)->nullable();
            $table->string('instagram', 100)->nullable();
            $table->boolean('promote')->nullable();
            $table->boolean('active')->nullable();
            $table->text('deactivation_reason')->nullable();
            $table->timestamp('deactivated_at')->nullable();
            $table->bigInteger('user_id')->unsigned()->nullable();
            $table->timestamps();

            $table->index(['name', 'first_name', 'last_name', 'phone', 'fax']);

            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('set null')
                ->onUpdate('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('markets');
    }
};
