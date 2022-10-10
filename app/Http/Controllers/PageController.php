<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PageController extends Controller
{
	protected function fallback() {
		return redirect()
			->route('home');
	}

	protected function index() {
		return view('index');
	}

	protected function redirectToDashboard() {
		return redirect()
			->route('admin.dashboard');
	}

	protected function dashboard() {
		return view('admin.dashboard');
	}
}