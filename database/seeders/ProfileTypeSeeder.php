<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ProfileType;

class ProfileTypeSeeder extends Seeder
{
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		ProfileType::create([
			'id' 		=> 1,
			'name'		=> 'Advogado'
		]);

		ProfileType::create([
			'id' 		=> 2,
			'name'		=> 'Cliente'
		]);
	}
}
