<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableAdvocateSchedules extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('advocate_schedules', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->json('horarys');
            $table->integer('time_type');
            $table->string('color');
            $table->unsignedBigInteger('client_id')->nullable();
            $table->unsignedBigInteger('advocate_user_id');
            $table->foreign('advocate_user_id')->references('id')->on('users');
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
        Schema::dropIfExists('advocate_schedules');
    }
}
