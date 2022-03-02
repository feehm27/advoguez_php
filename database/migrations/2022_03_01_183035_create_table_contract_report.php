<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableContractReport extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('contract_reports', function (Blueprint $table) {
            $table->id();
            $table->date('start_date')->nullable();
            $table->date('finish_date')->nullable();
            $table->string('canceled_at')->nullable();
            $table->string('status')->nullable();
            $table->string('payment_day')->nullable();
            $table->string('link_report')->nullable(true)->default(null);
            $table->unsignedBigInteger('report_id');
            $table->foreign('report_id')->references('id')->on('reports');
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
        Schema::dropIfExists('contract_reports');
    }
}
