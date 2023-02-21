<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

use App\MenuVariation;

class MenuVariationsTableSeeder extends Seeder
{
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		MenuVariation::create([
			'menu_id' => 1,
			'name' => "Adult",
			'price' => 3500,
			'duration' => '02:00'
		]);

		MenuVariation::create([
			'menu_id' => 1,
			'name' => "Senior High",
			'price' => 2000,
			'duration' => '02:00'
		]);

		MenuVariation::create([
			'menu_id' => 1,
			'name' => "Elementary",
			'price' => 1000,
			'duration' => '02:00'
		]);

		MenuVariation::create([
			'menu_id' => 2,
			'price' => 1200,
			'duration' => '01:00'
		]);
	}
}