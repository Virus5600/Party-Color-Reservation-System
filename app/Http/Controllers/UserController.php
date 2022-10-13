<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UserController extends Controller
{
	protected function index(Request $req) {
		// $users = User::get();
		$users = array();

		// if ($req->has('sd') && $req->sd == 1)
		// 	$users = User::withTrashed()->get();

		return view('admin.users.index', [
			'users' => $users,
			'show_softdeletes' => ($req->has('sd') && $req->sd == 1 ? true : false)
		]);
	}
}