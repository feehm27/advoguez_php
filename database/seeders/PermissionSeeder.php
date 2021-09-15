<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		Permission::create([
			'id' 		=> 1,
			'name'		=> 'Cadastrar'
		]);
		Permission::create([
			'id' 		=> 2,
			'name'		=> 'Visualizar'
		]);
		Permission::create([
			'id' 		=> 3,
			'name'		=> 'Editar'
		]);
		Permission::create([
			'id' 		=> 4,
			'name'		=> 'Exportar'
		]);
		Permission::create([
			'id' 		=> 5,
			'name'		=> 'Bloquear'
		]);
		Permission::create([
			'id' 		=> 6,
			'name'		=> 'Excluir'
		]);
	}
}
