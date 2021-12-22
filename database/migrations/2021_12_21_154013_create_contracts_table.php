<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateContractsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('contracts', function (Blueprint $table) 
        {
            $table->id();
            $table->date('start_date');
            $table->date('finish_date');
            $table->integer('payment_day');
            $table->double('contract_price', 8,2);
            $table->double('fine_price', 8,2);
            $table->string('agency', 6);
			$table->string('account', 30);
			$table->string('bank', 100);

            $table->datetime('canceled_at')->nullable();

            $table->unsignedBigInteger('advocate_id');
            $table->foreign('advocate_id')->references('id')->on('advocates');

            $table->unsignedBigInteger('client_id');
            $table->foreign('client_id')->references('id')->on('clients');

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
        Schema::dropIfExists('contracts');
    }
}
