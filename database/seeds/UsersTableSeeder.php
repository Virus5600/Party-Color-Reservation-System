<?php

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
			'name' => 'アドミン',
			'email' => 'privatelaravelmailtester@gmail.com',
			'password' => Hash::make('admin'),
			'email_verified_at' => \Carbon\Carbon::now();
		]);
	}
}