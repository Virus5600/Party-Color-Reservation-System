<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Inventory;
use App\Menu;

use DB;
use Log;
use Exception;
use Validator;

class MenuController extends Controller
{
	protected function index(Request $req) {
		$menus = Menu::withTrashed()->get();

		return view('admin.menu.index', [
			'menus' => $menus
		]);
	}

	protected function create() {
		$items = Inventory::get();

		return view('admin.menu.create', [
			'items' => $items
		]);
	}

	protected function store(Request $req) {
		$validator = Validator::make($req->all(), [
			''
		]);

		$existing = Inventory::withTrashed()->where('name', '=', $req->menu_name)->first();

		if ($existing)
			return $this->update($req, $existing->id);
	}

	protected function show($id) {
		$menu = Menu::withTrashed()->find($id);

		if ($menu == null) {
			return redirect()
				->route('admin.menu.index')
				->with('flash_error', 'The menu either does not exists or is already deleted.');
		}

		return view('admin.menu.show', [
			'menu' => $menu
		]);
	}

	protected function edit($id) {
		
	}
}