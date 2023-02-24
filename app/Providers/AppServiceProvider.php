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
		Relation::morphMap([
			'activity' => 'Spatie\Activitylog\Models\Activity',
			'additional_order' => 'App\AdditionalOrder',
			'announcement' => 'App\Announcement',
			'announcement-content-image' => 'App\AnnouncementContentImage',
			'booking' => 'App\Booking',
			'contact-information' => 'App\ContactInformation',
			'inventory' => 'App\Inventory',
			'menu' => 'App\Menu',
			'menu-variation' => 'App\MenuVariation',
			'password-reset' => 'App\PasswordReset',
			'permission' => 'App\Permission',
			'settings' => 'App\Settings',
			'type' => 'App\Type',
			'user' => 'App\User',
		]);
	}
}
