<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Inventory;
use App\Menu;
use App\MenuItem;
use App\ActivityLog;

use DB;
use Log;
use Exception;
use NumberFormatter;
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
		$menuItemValidation = [];
		$amountValidation = [];

		foreach ($req->menu_item as $k => $v) {
			$menuItemValidation["menu_item.{$k}"] = "required_unless:amount.{$k},null|numeric|exists:inventories,id";
			$amountValidation["amount.{$k}"] = "required_unless:menu_item.{$k},null|numeric|" . ($req->is_unlimited[$k] ? '' : 'min:1|') . "max:4294967295";
		}

		$validator = Validator::make($req->all(), array_merge([
			'menu_name' => 'required|string|max:255',
			'menu_item' => 'array|nullable',
			'price' => 'required|numeric|min:0|max:4294967295',
			'amount' => 'array|nullable',
			'duration' => 'required|date_format:H:i|before_or_equal:12:00|after:00:00'
		], $menuItemValidation, $amountValidation), [
			'menu_name.required' => 'A menu name is required',
			'menu_name.string' => 'The menu name should be a string',
			'menu_name.max' => 'Menu name should not exceed 255 characters',
			'menu_item.array' => '',
			'menu_item.nullable' => '',
			'price.required' => 'A price is required',
			'price.numeric' => 'The price should consists of numbers only',
			'price.min' => 'The minimum allowed price is ' . (new NumberFormatter(app()->currentLocale()."@currency=JPY", NumberFormatter::CURRENCY))->getSymbol(NumberFormatter::CURRENCY_SYMBOL) . '0.00 (Free)',
			'price.max' => 'The maximum allowed price is ' . (new NumberFormatter(app()->currentLocale()."@currency=JPY", NumberFormatter::CURRENCY))->getSymbol(NumberFormatter::CURRENCY_SYMBOL) . number_format(4294967295, 2),
			'amount.array' => '',
			'amount.nullable' => '',
			'duration.required' => 'Please set a duration for this menu',
			'duration.date_format' => 'Please refrain from modifying the page',
			'duration.before_or_equal' => 'Duration is capped at 12 hours',
			'duration.after' => 'Minimum duration is 1 minute',
			'menu_item.*.required_unless' => 'An item for this is required',
			'menu_item.*.numeric' => 'Please provide a proper item',
			'menu_item.*.exists' => 'The selected item does not exists',
			'amount.*.required_unless' => 'An amount of this item is required',
			'amount.*.numeric' => 'Please provide a proper amount',
			'amount.*.min' => 'Minimum amount is capped to 1',
			'amount.*.' => 'Maximum amount is capped to ' . number_format(4294967295),
		]);

		// Comporess the arrays
		$newIndex = [];
		$menuItem = [];
		$amount = [];
		$isUnlimited = [];
		for ($i = 0; $i < count($req->menu_item); $i++) {
			if ($req->menu_item[$i] || $req->amount[$i] || ($req->amount[$i] > 0)) {
				array_push($newIndex, $i);
				array_push($menuItem, $req->menu_item[$i]);
				array_push($amount, $req->amount[$i]);
				array_push($isUnlimited, $req->is_unlimited[$i]);
			}
		}

		// Replaces the old arrays with the new compressed one, adding their new indexes in the process as well to properly display validation messages
		$req->merge([
			'new_index' => $newIndex,
			'menu_item' => $menuItem,
			'amount' => $amount
		]);

		if ($validator->fails()) {
			return redirect()
				->back()
				->withErrors($validator)
				->withInput();
		}

		// If the menu already existed...
		$existing = Menu::withTrashed()->where('name', '=', $req->menu_name)->first();
		// Just update the existing entry
		if ($existing)
			return $this->update($req, $existing->id);

		try {
			DB::beginTransaction();

			$menu = Menu::create([
				'name' => $req->menu_name,
				'price' => $req->price,
				'duration' => $req->duration,
			]);

			for ($i = 0; $i < count($menuItem); $i++) {
				MenuItem::create([
					'menu_id' => $menu->id,
					'inventory_id' => $menuItem[$i],
					'amount' => $amount[$i],
					'is_unlimited' => $isUnlimited[$i]
				]);
			}

			DB::commit();
		} catch (Exception $e) {
			DB::rollback();
			Log::error($e);

			return redirect()
				->route('admin.menu.index')
				->with('flash_error', 'Something went wrong, please try again later');
		}

		ActivityLog::log(
			"Menu '{$req->menu_name}' created.",
			$menu->id,
			"Menu",
			Auth::user()->id
		);

		return redirect()
			->route('admin.menu.index')
			->with('flash_success', "Successfully added {$req->menu_name}");
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
		$menu = Menu::find($id);

		if ($menu == null) {
			return redirect()
				->route('admin.menu.index')
				->with('flash_error', 'The menu either does not exists or is already deleted.');
		}

		$items = Inventory::get();
		
		return view('admin.menu.edit', [
			'menu' => $menu,
			'items' => $items
		]);
	}

	protected function update(Request $req, $id) {
		$menuItemValidation = [];
		$amountValidation = [];

		$menu = Menu::withTrashed()->where('name', '=', $req->menu_name)->first();
		// Just update the existing entry
		if (!$menu)
			return redirect()
				->route('admin.menu.index')
				->with('flash_error', 'Menu either does not exists or is already deleted');

		foreach ($req->menu_item as $k => $v) {
			$menuItemValidation["menu_item.{$k}"] = "required_unless:amount.{$k},null|numeric|exists:inventories,id";
			$amountValidation["amount.{$k}"] = "required_unless:menu_item.{$k},null|numeric|min:1|max:4294967295";
		}

		$validator = Validator::make($req->all(), array_merge([
			'menu_name' => 'required|string|max:255',
			'menu_item' => 'array|nullable',
			'price' => 'required|numeric|min:0|max:4294967295',
			'amount' => 'array|nullable',
			'duration' => 'required|date_format:H:i|before_or_equal:12:00|after:00:00'
		], $menuItemValidation, $amountValidation), [
			'menu_name.required' => 'A menu name is required',
			'menu_name.string' => 'The menu name should be a string',
			'menu_name.max' => 'Menu name should not exceed 255 characters',
			'menu_item.array' => '',
			'menu_item.nullable' => '',
			'price.required' => 'A price is required',
			'price.numeric' => 'The price should consists of numbers only',
			'price.min' => 'The minimum allowed price is ' . (new NumberFormatter(app()->currentLocale()."@currency=JPY", NumberFormatter::CURRENCY))->getSymbol(NumberFormatter::CURRENCY_SYMBOL) . '0.00 (Free)',
			'price.max' => 'The maximum allowed price is ' . (new NumberFormatter(app()->currentLocale()."@currency=JPY", NumberFormatter::CURRENCY))->getSymbol(NumberFormatter::CURRENCY_SYMBOL) . number_format(4294967295, 2),
			'amount.array' => '',
			'amount.nullable' => '',
			'duration.required' => 'Please set a duration for this menu',
			'duration.date_format' => 'Please refrain from modifying the page',
			'duration.before_or_equal' => 'Duration is capped at 12 hours',
			'duration.after' => 'Minimum duration is 1 minute',
			'menu_item.*.required_unless' => 'An item for this is required',
			'menu_item.*.numeric' => 'Please provide a proper item',
			'menu_item.*.exists' => 'The selected item does not exists',
			'amount.*.required_unless' => 'An amount of this item is required',
			'amount.*.numeric' => 'Please provide a proper amount',
			'amount.*.min' => 'Minimum amount is capped to 1',
			'amount.*.' => 'Maximum amount is capped to ' . number_format(4294967295),
		]);

		// Comporess the arrays
		$newIndex = [];
		$menuItem = [];
		$amount = [];
		for ($i = 0; $i < count($req->menu_item); $i++) {
			if ($req->menu_item[$i] || $req->amount[$i] || ($req->amount[$i] > 0)) {
				array_push($newIndex, $i);
				array_push($menuItem, $req->menu_item[$i]);
				array_push($amount, $req->amount[$i]);
			}
		}

		// Replaces the old arrays with the new compressed one, adding their new indexes in the process as well to properly display validation messages
		$req->merge([
			'new_index' => $newIndex,
			'menu_item' => $menuItem,
			'amount' => $amount
		]);

		if ($validator->fails()) {
			return redirect()
				->back()
				->withErrors($validator)
				->withInput();
		}

		try {
			DB::beginTransaction();

			// Update menu information
			$menu->name = $req->menu_name;
			$menu->price = $req->price;
			$menu->duration = $req->duration;

			// Prepare the data to be synchronized with...
			$sync = array();
			for ($i = 0; $i < count($menuItem); $i++) {
				if (array_key_exists($menuItem[$i], $sync)) {
					$amount[$i] = $sync[$menuItem[$i]]['amount'] + $amount[$i];
					$sync[$menuItem[$i]] = ['amount' => $amount[$i]];
				}
				else {
					$sync[$menuItem[$i]] = ['amount' => $amount[$i]];
				}
			}

			// Many-to-Many update (Menu Item update)	
			$menu->items()->sync($sync);

			// Saving...
			$menu->save();

			DB::commit();
		} catch (Exception $e) {
			DB::rollback();
			Log::error($e);

			return redirect()
				->route('admin.menu.index')
				->with('flash_error', 'Something went wrong, please try again later');
		}

		ActivityLog::log(
			"Menu '{$menu->menu_name}' updated.",
			$menu->id,
			"Menu",
			Auth::user()->id
		);

		return redirect()
			->route('admin.menu.index')
			->with('flash_success', "Successfully updated {$req->menu_name}");
	}

	protected function delete(Request $req, $id) {
		$menu = Menu::find($id);

		if ($menu == null) {
			return redirect()
				->route('admin.menu.index')
				->with('flash_error', 'The menu either does not exists or is already deleted.');
		}

		try {
			DB::beginTransaction();			
			$menu->delete();
			DB::commit();
		} catch (Exception $e) {
			DB::rollback();
			Log::error($e);

			return redirect()
				->route('admin.menu.index')
				->with('flash_error', 'Something went wrong, please try again later');
		}

		ActivityLog::log(
			"Menu '{$menu->name}' deactivated.",
			$menu->id,
			"Menu",
			Auth::user()->id
		);

		return redirect()
			->back()
			->with('flash_success', 'Successfully deactivated menu.');
	}

	protected function restore(Request $req, $id) {
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
			DB::commit();
		} catch (Exception $e) {
			DB::rollback();
			Log::error($e);

			return redirect()
				->route('admin.menu.index')
				->with('flash_error', 'Something went wrong, please try again later');
		}

		ActivityLog::log(
			"Menu '{$menu->name}' activated.",
			$menu->id,
			"Menu",
			Auth::user()->id
		);

		return redirect()
			->back()
			->with('flash_success', 'Successfully activated menu.');
	}
}