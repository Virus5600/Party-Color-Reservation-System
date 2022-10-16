<?php

use Illuminate\Database\Seeder;

use App\Settings;

class SettingsTableSeeder extends Seeder
{
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		Settings::create([
			'name' => 'web-logo',
			'value' => 'party_color.png',
			'is_file' => 1
		]);

		Settings::create([
			'name' => 'web-name',
			'value' => 'Municipality of Taytay, Rizal'
		]);

		Settings::create([
			'name' => 'web-desc',
			'value' => 'The official website of Taytay Municipal'
		]);

		Settings::create([
			'name' => 'address',
			'value' => 'Don Hilario Avenue, Club Manila East Compound, Barangay San Juan Taytay, Rizal 1920 Philippines'
		]);

		Settings::create([
			'name' => 'contacts',
			'value' => '8 284-4771, 8 286-6149, 8 284-4770'
		]);

		Settings::create([
			'name' => 'email',
			'value' => 'privatelaravelmailtester@gmail.com'
		]);
	}
}
