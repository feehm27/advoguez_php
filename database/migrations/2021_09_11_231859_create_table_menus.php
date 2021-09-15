<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableMenus extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('menus', function (Blueprint $table) {
			$table->id();
			$table->string('name');
			$table->boolean('is_active')->default(true);
			$table->json('permissions_ids');
			$table->unsignedBigInteger('profile_type_id');
			$table->timestamps();
			$table->foreign('profile_type_id')->references('id')->on('profile_types');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::dropIfExists('menus');
	}
}
