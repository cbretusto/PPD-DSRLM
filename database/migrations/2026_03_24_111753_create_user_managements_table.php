<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserManagementsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_managements', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('rapidx_user_id')->comment('from RapidX User');
            $table->string('department')->nullable();
            $table->string('position')->nullable();
            $table->string('email')->nullable();
            $table->unsignedTinyInteger('user_level')->nullable()->comment('1-User, 2-Admin');
            $table->string('classification')->default(0)->comment('0-N/A, 1-Approve By, 2-Reset By, 3-Registered By');
            $table->unsignedTinyInteger('status')->default(0)->comment('0-active, 1-deactivate');
            $table->unsignedTinyInteger('logdel')->default(0)->comment('0-Show, 1-Hide');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_managements');
    }
}
