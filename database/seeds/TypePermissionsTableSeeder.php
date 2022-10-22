<?php

use Illuminate\Database\Seeder;

use App\Permission;
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
		for ($i = 1; $i <= Permission::get()->count(); $i++)
			TypePermission::insert([
				'type_id' => 1,
				'permission_id' => $i
			]);
	}
}