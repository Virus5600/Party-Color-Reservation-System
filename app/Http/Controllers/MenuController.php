<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Controllers\MenuVariationController;

use App\Inventory;
use App\Menu;
use App\MenuItem;

use DB;
use Log;
use Exception;
use NumberFormatter;
use Validator;

class MenuController extends Controller
{
	protected function index(Request $req) {
		$menus = Menu::withTrashed()
			->without(['menuVariations'])
			->withCount('menuVariations')
			->get();

		return view('admin.menu.index', [
			'menus' => $menus
		]);
	}

	protected function store(Request $req) {
		$validator = Validator::make($req->all(), [
			'menu_name' => 'required|unique:menus,name|string|max:255'
		], [
			'menu_name.required' => 'A menu name is required',
			'menu_name.unique' => 'A menu name should be unique',
			'menu_name.string' => 'The menu name should be a string',
			'menu_name.max' => 'Menu name should not exceed 255 characters'
		]);

		if ($validator->fails()) {
			return response()
				->json([
					'success' => false,
					'title' => "Validation Error",
					'message' => $validator->messages()->first()
				]);
		}

		try {
			DB::beginTransaction();

			$menu = Menu::create([
				'name' => $req->menu_name,
			]);

			// LOGGER
			activity('menu')
				->by(auth()->user())
				->on($menu)
				->event('create')
				->withProperties([
					'name' => $menu->menu_name
				])
				->log("Menu '{$req->menu_name}' created.");

			DB::commit();
		} catch (Exception $e) {
			DB::rollback();
			Log::error($e);

			return response()
				->json([
					'success' => false,
					'message' => 'Something went wrong, please try again later.',
				]);
		}

		return response()
			->json([
				'success' => true,
				'message' => "Successfully added {$req->menu_name}"
			]);
	}

	protected function update(Request $req, $id) {
		$menu = Menu::withTrashed()->find($id);
		
		if ($menu == null) {
			return response()
				->json([
					'flash_error' => 'Menu either does not exists or is already deleted'
				]);
		}

		$validator = Validator::make($req->all(), [
			'menu_name' => 'required|unique:menus,name|string|max:255'
		], [
			'menu_name.required' => 'A menu name is required',
			'menu_name.unique' => 'A menu name should be unique',
			'menu_name.string' => 'The menu name should be a string',
			'menu_name.max' => 'Menu name should not exceed 255 characters'
		]);

		if ($validator->fails()) {
			return response()
				->json([
					'success' => false,
					'title' => "Validation Error",
					'message' => $validator->messages()->first()
				]);
		}

		try {
			DB::beginTransaction();
			
			$oldName = $menu->name;

			// Update menu name
			$menu->name = $req->menu_name;
			$menu->save();

			// LOGGER
			activity('menu')
				->by(auth()->user())
				->on($menu)
				->event('create')
				->withProperties([
					'name' => $menu->menu_name
				])
				->log("Updated menu name from '{$oldName}' to {$req->menu_name}.");

			DB::commit();
		} catch (Exception $e) {
			DB::rollback();
			Log::error($e);

			return response()
				->json([
					'success' => false,
					'message' => 'Something went wrong, please try again later.',
				]);
		}

		return response()
			->json([
				'success' => true,
				'message' => "Succesfully updated '{$oldName}' to '{$req->menu_name}'"
			]);
	}

	public function delete(Request $req, $id) {
		$menu = Menu::find($id);

		if ($menu == null) {
			return redirect()
				->route('admin.menu.index')
				->with('flash_error', 'The menu either does not exists or is already deleted.');
		}

		try {
			DB::beginTransaction();			
			
			$menu->delete();

			// LOGGER
			activity('menu')
				->by(auth()->user())
				->on($menu)
				->event('create')
				->withProperties([
					'name' => $menu->menu_name
				])
				->log("Menu '{$menu->name}' deactivated.");
			
			DB::commit();
		} catch (Exception $e) {
			DB::rollback();
			Log::error($e);

			return redirect()
				->route('admin.menu.index')
				->with('flash_error', 'Something went wrong, please try again later');
		}

		return redirect()
			->back()
			->with('flash_success', 'Successfully deactivated menu.');
	}

	protected function restore($id) {
		$menu = Menu::withTrashed()->find($id);

		if ($menu == null) {
			return redirect()
				->route('admin.menu.index')
				->with('flash_error', 'Menu either does not exists or is already deleted permanently.');
		}
		else if (!$menu->trashed()) {
			return redirect()
				->back()
				->with('flash_error', 'The menu is already activated.');
		}

		try {
			DB::beginTransaction();
			
			$menu->restore();
			
			// LOGGER
			activity('menu')
				->by(auth()->user())
				->on($menu)
				->event('create')
				->withProperties([
					'name' => $menu->menu_name
				])
				->log("Menu '{$menu->name}' activated.");
			
			DB::commit();
		} catch (Exception $e) {
			DB::rollback();
			Log::error($e);

			return redirect()
				->route('admin.menu.index')
				->with('flash_error', 'Something went wrong, please try again later');
		}


		return redirect()
			->back()
			->with('flash_success', 'Successfully activated menu.');
	}
}