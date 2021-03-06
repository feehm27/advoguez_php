<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableMessagesReceived extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('message_receiveds', function (Blueprint $table) {

            $table->id();
            $table->unsignedBigInteger('code_message');
            $table->string('subject');
            $table->text('message');

            $table->unsignedBigInteger('client_id');
            $table->foreign('client_id')->references('id')->on('clients');
            
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
        Schema::dropIfExists('message_receiveds');
    }
}
