<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAdvocatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('advocates', function (Blueprint $table) {
            $table->id();
			
			$table->string('name', 200);
			$table->string('cpf', 11)->unique();
			$table->string('nationality', 200);
			$table->string('civil_status', 50);
			$table->string('register_oab', 8)->unique();
			$table->string('email')->unique();
			$table->string('cep', 8);
			$table->string('street', 200);
			$table->integer('number');
			$table->string('complement', 200)->nullable();
			$table->string('district', 150);
			$table->string('state', 200);
			$table->string('city', 200);
			$table->string('agency', 6)->nullable();
			$table->string('account', 30)->nullable();
			$table->string('bank', 100)->nullable();

			$table->unsignedBigInteger('user_id');
			$table->foreign('user_id')->references('id')->on('users');

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
        Schema::dropIfExists('advocates');
    }
}
