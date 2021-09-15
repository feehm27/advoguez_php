<?php

namespace Database\Seeders;

use App\Http\Utils\ProfileTypesUtils;
use App\Models\Menu;
use Illuminate\Database\Seeder;

class MenuSeeder extends Seeder
{
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		Menu::create([
			'id' 		 	  => 1,
			'name'			  => 'Dashboard',
			'is_active'		  => true,
			'profile_type_id' => ProfileTypesUtils::ADVOCATE,
			'permissions_ids' => [2]
		]);
		Menu::create([
			'id' 		 	  => 2,
			'name'			  => 'Meus Dados',
			'is_active'		  => true,
			'profile_type_id' => ProfileTypesUtils::ADVOCATE,
			'permissions_ids' => [1]
		]);
		Menu::create([
			'id' 		 	  => 3,
			'name'			  => 'Identidade Visual',
			'is_active'		  => true,
			'profile_type_id' => ProfileTypesUtils::ADVOCATE,
			'permissions_ids' => [1]
		]);
		Menu::create([
			'id' 		 	  => 4,
			'name'			  => 'Gestão de Clientes',
			'is_active'		  => true,
			'profile_type_id' => ProfileTypesUtils::ADVOCATE,
			'permissions_ids' => [1, 2, 3, 4, 6]
		]);
		Menu::create([
			'id' 		 	  => 5,
			'name'			  => 'Gestão de Contratos',
			'is_active'		  => true,
			'profile_type_id' => ProfileTypesUtils::ADVOCATE,
			'permissions_ids' => [1, 2, 3, 4, 6]
		]);
		Menu::create([
			'id' 		 	  => 6,
			'name'			  => 'Gestão de Processos',
			'is_active'		  => true,
			'profile_type_id' => ProfileTypesUtils::ADVOCATE,
			'permissions_ids' => [1, 2, 3, 4, 6]
		]);
		Menu::create([
			'id' 		 	  => 7,
			'name'			  => 'Agenda de Reuniões',
			'is_active'		  => true,
			'profile_type_id' => ProfileTypesUtils::ADVOCATE,
			'permissions_ids' => [1, 2]
		]);
		Menu::create([
			'id' 		 	  => 8,
			'name'			  => 'Mensagens',
			'is_active'		  => true,
			'profile_type_id' => ProfileTypesUtils::ADVOCATE,
			'permissions_ids' => [2]
		]);
		Menu::create([
			'id' 		 	  => 9,
			'name'			  => 'Lembretes',
			'is_active'		  => true,
			'profile_type_id' => ProfileTypesUtils::ADVOCATE,
			'permissions_ids' => [1, 2, 3, 6]
		]);
		Menu::create([
			'id' 		 	  => 10,
			'name'			  => 'Relatórios',
			'is_active'		  => true,
			'profile_type_id' => ProfileTypesUtils::ADVOCATE,
			'permissions_ids' => [1, 2, 3, 6]
		]);
		Menu::create([
			'id' 		 	  => 11,
			'name'			  => 'Usuários',
			'is_active'		  => true,
			'profile_type_id' => ProfileTypesUtils::ADVOCATE,
			'permissions_ids' => [1, 2, 3, 5, 6]
		]);
		Menu::create([
			'id' 		 	  => 12,
			'name'			  => 'Página Inicial',
			'is_active'		  => true,
			'profile_type_id' => ProfileTypesUtils::CLIENT,
			'permissions_ids' => [2]
		]);
		Menu::create([
			'id' 		 	  => 13,
			'name'			  => 'Agendar Reunião',
			'is_active'		  => true,
			'profile_type_id' => ProfileTypesUtils::CLIENT,
			'permissions_ids' => [1, 2]
		]);
		Menu::create([
			'id' 		 	  => 14,
			'name'			  => 'Contato',
			'is_active'		  => true,
			'profile_type_id' => ProfileTypesUtils::CLIENT,
			'permissions_ids' => [1]
		]);
	}
}
