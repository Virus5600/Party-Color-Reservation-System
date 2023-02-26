<?php

namespace Database\Seeders;

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
		for ($i = 1; $i <= Permission::count(); $i++)
			TypePermission::insert([
				'type_id' => 1,
				'permission_id' => $i
			]);

		// Manager
		$bookingAcc = Permission::where('slug', '=', 'bookings_tab_access')->first();
		$bookingPerm = Permission::where('parent_permission', '=', $bookingAcc->id)->orWhere('slug', '=', $bookingAcc->slug)->get();
		foreach ($bookingPerm as $r)
			TypePermission::insert([
				'type_id' => 2,
				'permission_id' => $r->id
			]);

		$inventoryAcc = Permission::where('slug', '=', 'inventory_tab_access')->first();
		$inventoryPerm = Permission::where('parent_permission', '=', $inventoryAcc->id)->orWhere('slug', '=', $inventoryAcc->slug)->get();
		foreach ($inventoryPerm as $i)
			TypePermission::insert([
				'type_id' => 2,
				'permission_id' => $i->id
			]);

		$menuAcc = Permission::where('slug', '=', 'menu_tab_access')->first();
		$menuPerm = Permission::where('parent_permission', '=', $menuAcc->id)->orWhere('slug', '=', $menuAcc->slug)->get();
		foreach ($menuPerm as $m)
			TypePermission::insert([
				'type_id' => 2,
				'permission_id' => $m->id
			]);

		$menuVarAcc = Permission::where('slug', '=', 'menu_var_tab_access')->first();
		$menuVarPerm = Permission::where('parent_permission', '=', $menuVarAcc->id)->orWhere('slug', '=', $menuVarAcc->slug)->get();
		foreach ($menuVarPerm as $mv)
			TypePermission::insert([
				'type_id' => 2,
				'permission_id' => $mv->id
			]);

		$announcementsAcc = Permission::where('slug', '=', 'announcements_tab_access')->first();
		$announcementsPerm = Permission::where('parent_permission', '=', $announcementsAcc->id)->orWhere('slug', '=', $announcementsAcc->slug)->get();
		foreach ($announcementsPerm as $a)
			TypePermission::insert([
				'type_id' => 2,
				'permission_id' => $a->id
			]);

		// Staff
		$staffAccess = Permission::whereIn('slug', [
			'inventory_tab_access',
			'inventory_tab_create',
			'inventory_tab_edit',
			'bookings_tab_access',
			'bookings_tab_create',
			'bookings_tab_respond',
		])->get();

		foreach ($staffAccess as $s)
			TypePermission::insert([
				'type_id' => 3,
				'permission_id' => $s->id
			]);
	}
}