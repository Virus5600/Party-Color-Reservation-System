<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Announcement;

class ReactApiController extends Controller
{
	// ANNOUNCEMENTS
	protected function fetchAnnouncements(Request $req) {
		$announcements = Announcement::where('is_draft', '!=', '1')->get();

		return response()->json([
			'announcements' => $announcements
		]);
	}
}