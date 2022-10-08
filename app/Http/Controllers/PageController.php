<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PageController extends Controller
{
	protected function fallback() {
		return view('welcome');
	}

	protected function index() {
		return view('index');
	}
}