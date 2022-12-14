<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

use App\Menu;

class MenuTableSeeder extends Seeder
{
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		Menu::create([
			'name' => 'BBQ Plan',
			'price' => 3500,
			'duration' => '02:00'
		]);

		Menu::create([
			'name' => 'Drink All You Can',
			'price' => 1200,
			'duration' => '01:00'
		]);
	}
}