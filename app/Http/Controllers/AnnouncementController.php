<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Announcement;

use DB;
use Exception;
use File;
use Location;
use Log;
use Mail;
use Validator;

class AnnouncementController extends Controller
{
	protected function index(Request $req) {
		$announcements = Announcement::get();

		if ($req->has('sd') && $req->sd == 1)
			$announcements = Announcement::withTrashed()->get();

		return view('admin.announcements.index', [
			'announcements' => $announcements,
			'show_softdeletes' => ($req->has('sd') && $req->sd == 1 ? true : false)
		]);
	}

	protected function create() {
		return view('admin.announcements.create');
	}

	protected function store(Request $req) {
	}
}