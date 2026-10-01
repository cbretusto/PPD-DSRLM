<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDeviceResetHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('device_reset_histories', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('device_code');
            $table->date('from')->nullable();
            $table->date('to')->nullable();
            $table->integer('variance')->nullable();
            $table->string('uploaded_file')->nullable();
            $table->string('reset_by')->nullable()->comment('RapidX User ID');
            $table->string('approve_by')->nullable()->comment('RapidX User ID');
            $table->dateTime('approved_by_date_time')->nullable();
            $table->string('approved_by_remark')->nullable();
            $table->unsignedTinyInteger('approval_status')->default(0)->comment('0 = Pending, 1 = Approved, 2 = Disapproved');
            $table->unsignedTinyInteger('status')->default(0)->comment('0-pending, 1-reset');
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
        Schema::dropIfExists('device_reset_histories');
    }
}
