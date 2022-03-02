<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableClientReport extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('client_reports', function (Blueprint $table) {
            $table->id();
            $table->date('birthday')->nullable();
            $table->date('registration_date')->nullable();
            $table->string('gender')->nullable();
            $table->string('civil_status')->nullable();
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
        Schema::dropIfExists('client_reports');
    }
}
