<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDevicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('devices', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('device_code')->nullable();
            $table->string('device_name')->nullable();
            $table->integer('tool_life')->nullable();
            $table->string('attachment')->nullable();
            $table->unsignedTinyInteger('classification')->default(0)->comment('0-N/A, 1-For Maintenance, 2-Waiting for Longevity Parts, 3-For YEC Approval');
            $table->unsignedTinyInteger('status')->default(0)->comment('0-active, 1-deactivate');
            $table->string('process_type')->nullable()->comment('0 - PPD-CN, 1 - PPD - TS, 2 - STAMPING');
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
        Schema::dropIfExists('devices');
    }
}
