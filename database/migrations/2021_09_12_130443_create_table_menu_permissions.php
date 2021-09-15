<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableMenuPermissions extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('menu_permissions', function (Blueprint $table) {
			$table->id();
			$table->unsignedBigInteger('menu_id');
			$table->unsignedBigInteger('permission_id');
			$table->unsignedBigInteger('user_id');
			$table->boolean('permission_is_active')->default(true);
			$table->boolean('menu_is_active')->default(true);
			$table->foreign('menu_id')->references('id')->on('menus');
			$table->foreign('permission_id')->references('id')->on('permissions');
			$table->foreign('user_id')->references('id')->on('users');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::dropIfExists('menu_permissions');
	}
}
