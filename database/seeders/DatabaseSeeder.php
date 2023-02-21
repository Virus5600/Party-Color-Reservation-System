<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
	/**
	 * Seed the application's database.
	 *
	 * @return void
	 */
	public function run()
	{
		$this->call([
			SettingsTableSeeder::class,
			PermissionsTableSeeder::class,
			TypesTableSeeder::class,
			TypePermissionsTableSeeder::class,
			UsersTableSeeder::class,
			AnnouncementsTableSeeder::class,
			InventoryTableSeeder::class,
			MenuTableSeeder::class,
			MenuVariationsTableSeeder::class,
			MenuVariationItemsTableSeeder::class
		]);
	}
}