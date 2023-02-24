<?php

namespace App\Observers;

use Spatie\Activitylog\Models\Activity;

class ActivityObserver
{
	/**
     * Handle events after all transactions are committed.
     *
     * @var bool
     */
	public $afterCommit = true;

	/**
	 * Handle the Activity "created" event.
	 *
	 * @param  \App\Spatie\ActivityLog\Models\Activity  $activity
	 * @return void
	 */
	public function created(Activity $activity) {
		$activity->ip_address = request()->ip();
		$activity->save();
	}
}