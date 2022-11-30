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
		$this->call(SettingsTableSeeder::class);
		$this->call(PermissionsTableSeeder::class);
		$this->call(TypesTableSeeder::class);
		$this->call(TypePermissionsTableSeeder::class);
		$this->call(UsersTableSeeder::class);
		// $this->call(UserPermissionsTableSeeder::class);
	}
}