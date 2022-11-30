<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ReservationController extends Controller
{
	protected function index(Request $req) {
		return view('admin.reservations.index');
	}
}