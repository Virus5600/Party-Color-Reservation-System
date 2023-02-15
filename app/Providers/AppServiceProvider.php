<?php

namespace App\Providers;

use Illuminate\Database\Eloquent\Relations\Relation;

use Illuminate\Pagination\Paginator;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
	/**
	 * Register any application services.
	 *
	 * @return void
	 */
	public function register()
	{
		//
	}

	/**
	 * Bootstrap any application services.
	 *
	 * @return void
	 */
	public function boot()
	{
		// Set pagination to bootstrap
		Paginator::useBootstrap();

		// Use https when on peoduction
		if(config('app.env') === 'production') {
			\URL::forceScheme('https');
		}

		// Uses alias instead of their path names
		Relation::enforceMorphMap([
			'booking' => 'App\Booking',
			'additional_order' => 'App\AdditionalOrder',
		]);
	}
}
