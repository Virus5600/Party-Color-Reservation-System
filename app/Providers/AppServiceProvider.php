<?php

namespace App\Providers;

use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Eloquent\Builder;

use Illuminate\Pagination\Paginator;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;

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

		// Macros
		$this->initiateMacros();
	}

	private function initiateMacros() {
		Builder::macro('toSqlWithBindings', function () {
			$bindings = array_map(
				fn ($value) => is_numeric($value) ? $value : "'{$value}'",
				$this->getBindings()
			);

			return Str::replaceArray('?', $bindings, $this->toSql());
		});
	}
}
