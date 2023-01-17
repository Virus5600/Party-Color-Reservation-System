<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\ActivityLog;

class ActivityLogController extends Controller
{
	protected function index(Request $req) {
		$activity = ActivityLog::get();

		return view('admin.activity-log.index', [
			'activity' => $activity
		]);
	}
}

?>