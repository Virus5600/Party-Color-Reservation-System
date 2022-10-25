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
		// RESERVATIONS
		$reservationPerm = Permission::create([
			'name' => 'Reservations Tab Access',
			'slug' => 'reservations_tab_access'
		]);

		Permission::create([
			'parent_permission' => $reservationPerm->id,
			'name' => 'Reservations Tab Respond',
			'slug' => 'reservations_tab_respond'
		]);

		Permission::create([
			'parent_permission' => $reservationPerm->id,
			'name' => 'Reservations Tab Delete',
			'slug' => 'reservations_tab_delete'
		]);

		Permission::create([
			'parent_permission' => $reservationPerm->id,
			'name' => 'Reservations Tab Perma Delete',
			'slug' => 'reservations_tab_perma_delete'
		]);

		// INVENTORY
		$invPerm = Permission::create([
			'name' => 'Inventory Tab Access',
			'slug' => 'inventory_tab_access'
		]);

		Permission::create([
			'parent_permission' => $invPerm->id,
			'name' => 'Inventory Tab Create',
			'slug' => 'inventory_tab_create'
		]);

		Permission::create([
			'parent_permission' => $invPerm->id,
			'name' => 'Inventory Tab Edit',
			'slug' => 'inventory_tab_edit'
		]);

		Permission::create([
			'parent_permission' => $invPerm->id,
			'name' => 'Inventory Tab Delete',
			'slug' => 'inventory_tab_delete'
		]);

		Permission::create([
			'parent_permission' => $invPerm->id,
			'name' => 'Inventory Tab Perma Delete',
			'slug' => 'inventory_tab_perma_delete'
		]);

		// ANNOUNCEMENTS
		 $annPerm = Permission::create([
		 	'name' => 'Announcements Tab Access',
		 	'slug' => 'announcements_tab_access'
		 ]);

		 Permission::create([
		 	'parent_permission' => $annPerm->id,
		 	'name' => 'Announcements Tab Create',
		 	'slug' => 'announcements_tab_create'
		 ]);

		 Permission::create([
		 	'parent_permission' => $annPerm->id,
		 	'name' => 'Announcements Tab Edit',
		 	'slug' => 'announcements_tab_edit'
		 ]);

		 Permission::create([
		 	'parent_permission' => $annPerm->id,
		 	'name' => 'Announcements Tab Publish',
		 	'slug' => 'announcements_tab_publish'
		 ]);

		 Permission::create([
		 	'parent_permission' => $annPerm->id,
		 	'name' => 'Announcements Tab Unpublish',
		 	'slug' => 'announcements_tab_unpublish'
		 ]);

		 Permission::create([
		 	'parent_permission' => $annPerm->id,
		 	'name' => 'Announcements Tab Send Mail',
		 	'slug' => 'announcements_tab_send_mail'
		 ]);

		 Permission::create([
		 	'parent_permission' => $annPerm->id,
		 	'name' => 'Announcements Tab Delete',
		 	'slug' => 'announcements_tab_delete'
		 ]);

		 Permission::create([
		 	'parent_permission' => $annPerm->id,
		 	'name' => 'Announcements Tab Perma Delete',
		 	'slug' => 'announcements_tab_perma_delete'
		 ]);

		// USERS
		$userPerm = Permission::create([
			'name' => 'Users Tab Access',
			'slug' => 'users_tab_access'
		]);

		Permission::create([
			'parent_permission' => $userPerm->id,
			'name' => 'Users Tab Create',
			'slug' => 'users_tab_create'
		]);

		Permission::create([
			'parent_permission' => $userPerm->id,
			'name' => 'Users Tab Edit',
			'slug' => 'users_tab_edit'
		]);

		Permission::create([
			'parent_permission' => $userPerm->id,
			'name' => 'Users Tab Permissions',
			'slug' => 'users_tab_permissions'
		]);

		Permission::create([
			'parent_permission' => $userPerm->id,
			'name' => 'Users Tab Delete',
			'slug' => 'users_tab_delete'
		]);

		Permission::create([
			'parent_permission' => $userPerm->id,
			'name' => 'Users Tab Perma Delete',
			'slug' => 'users_tab_perma_delete'
		]);

		// PERMISSION - 11
		$permsPerm = Permission::create([
			'name' => 'Permissions Tab Access',
			'slug' => 'permissions_tab_access'
		]);

		Permission::create([
			'parent_permission' => $permsPerm->id,
			'name' => 'Permissions Tab Manage',
			'slug' => 'permissions_tab_manage'
		]);

		// SETTINGS - 13
		$settingsPerm = Permission::create([
			'name' => 'Settings Tab Access',
			'slug' => 'settings_tab_access'
		]);

		Permission::create([
			'parent_permission' => $settingsPerm->id,
			'name' => 'Settings Tab Edit',
			'slug' => 'settings_tab_edit'
		]);
	}
}