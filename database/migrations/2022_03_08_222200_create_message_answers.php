<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMessageAnswers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('message_answers', function (Blueprint $table) 
        {
            $table->id();
            $table->text('answer');
            $table->unsignedBigInteger('code_message')->index();
        
            $table->unsignedBigInteger('message_received_id');
            $table->foreign('message_received_id')->references('id')->on('message_receiveds');

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
        Schema::dropIfExists('message_answers');
    }
}
