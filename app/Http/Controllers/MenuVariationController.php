<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Inventory;
use App\Menu;
use App\MenuVariation;

use DB;

class MenuVariationController extends Controller
{
	protected function index(Request $req, $mid) {
		$menu = Menu::withTrashed()->find($mid);
		if ($menu == null) {
			return redirect()
				->route('admin.menu.index')
				->with('flash_error', 'The menu either does not exists or is already deleted.');
		}

		$variations = $menu->menuVariations()->withTrashed()->get();

		return view('admin.menu.variation.index', [
			'menu' => $menu,
			'variations' => $variations
		]);
	}

	protected function create($mid) {
		$menu = Menu::withTrashed()->find($mid);
		if ($menu == null) {
			return redirect()
				->route('admin.menu.index')
				->with('flash_error', 'The menu either does not exists or is already deleted.');
		}

		$items = Inventory::get();

		return view('admin.menu.variation.create', [
			'menu' => Menu::withTrashed()->find($mid),
			'items' => $items
		]);
	}

	protected function store(Request $req, $mid) {
		$menu = Menu::withTrashed()->find($mid);
		if ($menu == null) {
			return redirect()
				->route('admin.menu.index')
				->with('flash_error', 'The menu either does not exists or is already deleted.');
		}

		extract(MenuVariation::validate($req));

		if ($validator->fails()) {
			return redirect()
				->back()
				->withErrors($validator)
				->withInput();
		}
		
		try {
			DB::beginTransaction();
			
			$variation = MenuVariation::create([
				'menu_id' => $mid,
				'name' => $req->variation_name,
				'price' => $req->price,
				'duration' => $req->duration,
			]);
			
			for ($i = 0; $i < count($variationItem); $i++) {
				$variation->items()->attach($variationItem[$i], [
					'amount' => $amount[$i],
					'is_unlimited' => $isUnlimited[$i]
				]);
			}
			
			activity('menu-variation')
				->by(auth()->user())
				->on($variation)
				->event('create')
				->withProperties([
					'menu_id' => $mid,
					'name' => $req->variation_name,
					'price' => $req->price,
					'duration' => $req->duration,
				])
				->log("Menu Variation '{$req->menu_name}' created.");
			
			DB::commit();
		} catch (Exception $e) {
			DB::rollback();
			Log::error($e);
			return redirect()
				->route('admin.menu.variation.index', [$mid])
				->with('flash_error', 'Something went wrong, please try again later');
		}
		return redirect()
			->route('admin.menu.variation.index', [$mid])
			->with('flash_success', "Successfully added {$req->menu_name}");
	}

	protected function show($mid, $vid) {
		$menu = Menu::withTrashed()->find($mid);
		if ($menu == null) {
			return redirect()
				->route('admin.menu.index')
				->with('flash_error', 'The menu either does not exists or is already deleted.');
		}
		
		$variation = MenuVariation::withTrashed()
			->with(["items"])
			->find($vid);
		if ($variation == null) {
			return redirect()
				->route('admin.menu.variation.index', [$mid])
				->with('flash_error', 'The menu variation either does not exists or is already deleted.');
		}

		return view('admin.menu.variation.show', [
			'menu' => $menu,
			'variation' => $variation
		]);
	}

	protected function edit($mid, $vid) {
		$menu = Menu::withTrashed()->find($mid);
		if ($menu == null) {
			return redirect()
				->route('admin.menu.index')
				->with('flash_error', 'The menu either does not exists or is already deleted.');
		}

		$variation = MenuVariation::withTrashed()->find($vid);
		if ($variation == null) {
			return redirect()
				->route('admin.menu.variation.index')
				->with('flash_error', 'The menu variation either does not exists or is already deleted.');
		}

		$items = Inventory::get();
		
		return view('admin.menu.variation.edit', [
			'menu' => $menu,
			'variation' => $variation,
			'items' => $items
		]);
	}

	protected function update(Request $req, $mid, $vid) {
		$menu = Menu::withTrashed()->find($mid);
		if ($menu == null) {
			return redirect()
				->route('admin.menu.index')
				->with('flash_error', 'The menu either does not exists or is already deleted.');
		}

		$variation = MenuVariation::withTrashed()->find($vid);
		if ($variation == null) {
			return redirect()
				->route('admin.menu.variation.index')
				->with('flash_error', 'The menu variation either does not exists or is already deleted.');
		}

		extract(MenuVariation::validate($req, $vid));

		if ($validator->fails()) {
			return redirect()
				->back()
				->withErrors($validator)
				->withInput();
		}

		try {
			DB::beginTransaction();

			// Update menu variation information
			$variation->name = $req->variation_name;
			$variation->price = $req->price;
			$variation->duration = $req->duration;

			// Prepare the data to be synchronized with...
			$sync = array();
			for ($i = 0; $i < count($variationItem); $i++) {
				if (array_key_exists($variationItem[$i], $sync)) {
					$amount[$i] = $sync[$variationItem[$i]]['amount'] + $amount[$i];
					$sync[$variationItem[$i]] = ['amount' => $amount[$i]];
				}
				else {
					$sync[$variationItem[$i]] = ['amount' => $amount[$i]];
				}
			}

			// Many-to-Many update (Menu Variation Item update)	
			$variation->items()->sync($sync);

			// Saving...
			$variation->save();

			// LOGGER
			activity('menu-variation')
				->by(auth()->user())
				->on($variation)
				->event('create')
				->withProperties([
					'menu_id' => $mid,
					'name' => $req->variation_name,
					'price' => $req->price,
					'duration' => $req->duration,
				])
				->log("Menu Variation '{$variation->name}' updated.");

			DB::commit();
		} catch (Exception $e) {
			DB::rollback();
			Log::error($e);

			return redirect()
				->route('admin.menu.variation.index', [$mid])
				->with('flash_error', 'Something went wrong, please try again later');
		}

		return redirect()
			->route('admin.menu.variation.index', [$mid])
			->with('flash_success', "Successfully updated {$req->variation_name}");
	}

	protected function delete(Request $req, $mid, $vid) {
		$menu = Menu::withTrashed()->find($mid);
		if ($menu == null) {
			return redirect()
				->route('admin.menu.index')
				->with('flash_error', 'The menu either does not exists or is already deleted.');
		}

		$variation = MenuVariation::withTrashed()->find($vid);
		if ($variation == null) {
			return redirect()
				->route('admin.menu.variation.index', [$mid])
				->with('flash_error', 'The menu variation either does not exists or is already deleted.');
		}

		try {
			DB::beginTransaction();
			
			$variation->delete();

			// LOGGER
			activity('menu-variation')
				->by(auth()->user())
				->on($variation)
				->event('create')
				->withProperties([
					'menu_id' => $mid,
					'name' => $req->variation_name,
					'price' => $req->price,
					'duration' => $req->duration,
				])
				->log("Menu Variation '{$req->menu_name}' deactivated.");
			
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

	protected function restore(Request $req, $mid, $vid) {
		$menu = Menu::withTrashed()->find($mid);
		if ($menu == null) {
			return redirect()
				->route('admin.menu.index')
				->with('flash_error', 'The menu either does not exists or is already deleted.');
		}

		$variation = MenuVariation::withTrashed()->find($vid);
		if ($variation == null) {
			return redirect()
				->route('admin.menu.variation.index', [$mid])
				->with('flash_error', 'The menu variation either does not exists or is already deleted.');
		}

		try {
			DB::beginTransaction();

			$fromMenu = $req->has('from_menu') ? $req->from_menu : false;
			
			$variation->restore();

			// LOGGER
			activity('menu-variation')
				->by(auth()->user())
				->on($variation)
				->event('create')
				->withProperties([
					'menu_id' => $mid,
					'name' => $req->variation_name,
					'price' => $req->price,
					'duration' => $req->duration,
				])
				->log("Menu Variation '{$req->menu_name}' activated.");
			
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