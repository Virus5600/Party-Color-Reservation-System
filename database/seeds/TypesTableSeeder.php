<?php

use Illuminate\Database\Seeder;

use App\Type;

class TypesTableSeeder extends Seeder
{
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		Type::create(['name' => 'Master Admin']);
		Type::create(['name' => 'Manager']);
		Type::create(['name' => 'Staff']);
	}
}