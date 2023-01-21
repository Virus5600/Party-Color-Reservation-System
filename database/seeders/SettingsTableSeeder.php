<?php

namespace Database\Seeders;

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
			'value' => 'default.png',
			'is_file' => 1
		]);

		Settings::create([
			'name' => 'web-name',
			'value' => 'Party Color'
		]);

		Settings::create([
			'name' => 'web-desc',
			'value' => 'Party Color website that offers reservation for barbecue plan, promos etc'
		]);

		Settings::create([
			'name' => 'address',
			'value' => '2-2-12 Nakahara Building 3F Tsuboya Naha city Okinawa, Japan'
		]);

		Settings::create([
			'name' => 'contacts',
			'value' => '080-3980-4560'
		]);

		Settings::create([
			'name' => 'emails',
			'value' => 'partycolor3f@gmail.com'
		]);

		Settings::create([
			'name' => 'capacity',
			'value' => '50'
		]);

		Settings::create([
			'name' => 'opening',
			'value' => '17:00'
		]);

		Settings::create([
			'name' => 'closing',
			'value' => '22:00'
		]);

		Settings::create([
			'name' => 'day-schedule',
			'value' => '0,3,4,5,6'
		]);
	}
}
