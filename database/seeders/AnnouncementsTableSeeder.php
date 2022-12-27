<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

use App\Announcement;

class AnnouncementsTableSeeder extends Seeder
{
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		Announcement:create([
			'id' => 1,
			'poster' => "1-63ab36cd649ecポスター.png",
			'title' => 'Halloween 15% Discount Promo',
			'slug' => 'Halloween_15%_Discount_Promo',
			'summary' => 'Limited time discount available this Holloween!',
			'content' => "<p>BBQ &amp; Drinks Plan<p>Adult - Senior High: &#65509;3,500 to &#65509; 2,975 BBQ &amp; Drinks Plan</p></p>",
			'is_draft' => 0,
			'user_id' => 1
		]);
	}
}