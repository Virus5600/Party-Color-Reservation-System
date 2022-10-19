<?php

use Illuminate\Database\Seeder;

use App\TypePermission;

class TypePermissionsTableSeeder extends Seeder
{
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{	
		// Master Admin
		for ($i = 1; $i <= 14; $i++)
			TypePermission::insert([
				'type_id' => 1,
				'permission_id' => $i
			]);
	}
}