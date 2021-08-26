<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldsToUsersTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('users', function (Blueprint $table) {
			$table->boolean('is_client')->default(0)->after('email');
			$table->boolean('is_advocate')->default(0)->after('is_client');
			$table->string('linkedin_id')->after('is_advocate');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('users', function (Blueprint $table) {
			$table->dropColumn('is_client');
			$table->dropColumn('is_advocate');
			$table->dropColumn('linkedin_id');
		});
	}
}
