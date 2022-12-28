<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

use App\User;

use Hash;

class UsersTableSeeder extends Seeder
{
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		User::create([
			'first_name' => 'アドミン',
			'last_name' => 'アカウント',
			'avatar' => 'Karl Satchi-Navida-DP.png',
			'email' => 'privatelaravelmailtester@gmail.com',
			'password' => Hash::make('admin'),
			'type_id' => 1
		]);
	}
}