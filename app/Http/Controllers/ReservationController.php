<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Inventory;
use App\Menu;

class ReservationController extends Controller
{
	protected function index(Request $req) {
		return view('admin.reservations.index');
	}

	protected function create() {
		$menus = Menu::get();

		return view('admin.reservations.create', [
			'menus' => $menus
		]);
	}

	protected function store(Request $req) {
		dd($req);
	}
}