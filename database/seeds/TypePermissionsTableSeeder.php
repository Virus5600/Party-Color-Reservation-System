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

		// Manager
		$reservationAcc = Permission::where('slug', '=', 'reservations_tab_access')->first();
		$reservationPerm = Permission::where('parent_permission', '=', $reservationAcc->id)->orWhere('slug', '=', $reservationAcc->slug)->get();
		foreach ($reservationPerm as $r)
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

		$announcementsAcc = Permission::where('slug', '=', 'announcements_tab_access')->first();
		$announcementsPerm = Permission::where('parent_permission', '=', $announcementsAcc->id)->orWhere('slug', '=', $announcementsAcc->slug)->get();
		foreach ($announcementsPerm as $a)
			TypePermission::insert([
				'type_id' => 2,
				'permission_id' => $a->id
			]);

		// Staff
		$staffAccess = [
			Permission::where('slug', '=', 'inventory_tab_access')->first(),
			Permission::where('slug', '=', 'inventory_tab_create')->first(),
			Permission::where('slug', '=', 'inventory_tab_edit')->first()
		];

		foreach ($staffAccess as $s)
			TypePermission::insert([
				'type_id' => 3,
				'permission_id' => $s->id
			]);
	}
}