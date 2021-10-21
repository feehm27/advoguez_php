<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClientsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('clients', function (Blueprint $table) {

            $table->id();
			
			$table->string('name', 200);
			$table->string('email')->unique();
			$table->string('cpf', 11)->unique();
			$table->string('rg', 10);
			$table->string('issuing_organ', 50);
            $table->string('nationality', 200);
			$table->date('birthday');
            $table->string('gender', 50);
            $table->string('civil_status', 50);
            $table->string('telephone', 10)->nullable();
            $table->string('cellphone',11);
			$table->string('cep', 8);
			$table->string('street', 200);
			$table->integer('number');
			$table->string('complement', 200)->nullable();
			$table->string('district', 150);
			$table->string('state', 200);
			$table->string('city', 200);
			$table->unsignedBigInteger('advocate_user_id');
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
        Schema::dropIfExists('clients');
    }
}
