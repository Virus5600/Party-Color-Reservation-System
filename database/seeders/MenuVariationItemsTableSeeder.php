<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

use App\MenuVariationItem;

class MenuVariationItemsTableSeeder extends Seeder
{
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		// BBQ Plan
		for ($i = 1; $i <= 3; $i++) {
			MenuVariationItem::insert([
				'menu_variation_id' => $i,
				'inventory_id' => 1,
				'amount' => 5
			]);

			MenuVariationItem::insert([
				'menu_variation_id' => $i,
				'inventory_id' => 2,
				'amount' => 5
			]);

			MenuVariationItem::insert([
				'menu_variation_id' => $i,
				'inventory_id' => 3,
				'amount' => 5
			]);
		}

		// Drink All You Can
		{
			MenuVariationItem::insert([
				'menu_variation_id' => 4,
				'inventory_id' => 4,
				'amount' => 0,
				'is_unlimited' => 1
			]);

			MenuVariationItem::insert([
				'menu_variation_id' => 4,
				'inventory_id' => 5,
				'amount' => 0,
				'is_unlimited' => 1
			]);
		}
	}
}