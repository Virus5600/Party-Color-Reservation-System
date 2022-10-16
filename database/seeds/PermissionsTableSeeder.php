<?php

use Illuminate\Database\Seeder;

use App\Permission;

class PermissionsTableSeeder extends Seeder
{
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		// USERS - 1
		Permission::create([
			'name' => 'Users Tab Access',
			'slug' => 'users_tab_access'
		]);

		Permission::create([
			'parent_permission' => 1,
			'name' => 'Users Tab Create',
			'slug' => 'users_tab_create'
		]);

		Permission::create([
			'parent_permission' => 1,
			'name' => 'Users Tab Edit',
			'slug' => 'users_tab_edit'
		]);

		Permission::create([
			'parent_permission' => 1,
			'name' => 'Users Tab Permissions',
			'slug' => 'users_tab_permissions'
		]);

		Permission::create([
			'parent_permission' => 1,
			'name' => 'Users Tab Delete',
			'slug' => 'users_tab_delete'
		]);

		Permission::create([
			'parent_permission' => 1,
			'name' => 'Users Tab Perma Delete',
			'slug' => 'users_tab_perma_delete'
		]);

		// RESERVATIONS - 7
		Permission::create([
			'name' => 'Reservations Tab Access',
			'slug' => 'reservations_tab_access'
		]);

		Permission::create([
			'parent_permission' => 7,
			'name' => 'Reservations Tab Respond',
			'slug' => 'reservations_tab_respond'
		]);

		Permission::create([
			'parent_permission' => 7,
			'name' => 'Reservations Tab Delete',
			'slug' => 'reservations_tab_delete'
		]);

		Permission::create([
			'parent_permission' => 7,
			'name' => 'Reservations Tab Perma Delete',
			'slug' => 'reservations_tab_perma_delete'
		]);


		// PERMISSION - 11
		Permission::create([
			'name' => 'Permissions Tab Access',
			'slug' => 'permissions_tab_access'
		]);

		Permission::create([
			'parent_permission' => 11,
			'name' => 'Permissions Tab Manage',
			'slug' => 'permissions_tab_manage'
		]);

		// SETTINGS - 13
		Permission::create([
			'name' => 'Settings Tab Access',
			'slug' => 'settings_tab_access'
		]);

		Permission::create([
			'parent_permission' => 13,
			'name' => 'Settings Tab Edit',
			'slug' => 'settings_tab_edit'
		]);
	}
}