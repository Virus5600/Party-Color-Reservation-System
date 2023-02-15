<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Inventory;
use App\ActivityLog;

use Auth;
use DB;
use Log;
use Exception;
use Validator;

class InventoryController extends Controller
{
	protected function index(Request $req) {
		$items = Inventory::withTrashed()->get();

		return view('admin.inventory.index', [
			'items' => $items
		]);
	}

	protected function create(Request $req) {
		$measurement_unit = [];

		foreach (Inventory::select('measurement_unit')->withTrashed()->distinct()->get() as $i)
			array_push($measurement_unit, $i->measurement_unit);

		return view('admin.inventory.create', [
			'measurement_unit' => $measurement_unit
		]);
	}

	protected function store(Request $req) {
		$validator = Validator::make($req->all(), [
			'item_name' => 'required|string|max:255',
			'quantity' => 'required|integer|max:4294967295',
			'measurement_unit' => 'required|string|max:50',
			'critical_level' => 'integer|min:0',
		], [
			'item_name.required' => 'Item name is required',
			'item_name.string' => 'Item name should be a string',
			'item_name.max' => 'Item name should not be longer than 255 characters',
			'quantity.required' => 'Quantity is required',
			'quantity.integer' => 'Quantity should be a number',
			'quantity.max' => 'Quantity should not exceed 4,294,967,295',
			'measurement_unit.required' => 'A unit of measurement is required',
			'measurement_unit.string' => 'Unit of measurement should be a string',
			'measurement_unit.max' => 'Unit of measurement should not be longer than 50 characters',
			'critical_level.integer' => 'Critical level should be a number',
			'critical_level.min' => 'Critical level should not be below 0',
		]);

		if ($validator->fails()) {
			return redirect()
				->back()
				->withErrors($validator)
				->withInput();
		}

		$existing = Inventory::withTrashed()->where('item_name', '=', $req->item_name)->first();

		if ($existing)
			return $this->update($req, $existing->id);

		try {
			DB::beginTransaction();

			$item = Inventory::create([
				'item_name' => $req->item_name,
				'quantity' => $req->quantity,
				'measurement_unit' => $req->measurement_unit,
				'critical_level' => $req->critical_level
			]);

			if (!$req->is_active)
				$item->delete();

			DB::commit();
		} catch (Exception $e) {
			DB::rollback();
			Log::error($e);

			return redirect()
				->route('admin.inventory.index')
				->with('flash_error', 'Something went wrong, please try again later');
		}

		ActivityLog::log(
			"Item '{$req->item_name}' created.",
			$item->id,
			"Inventory",
			Auth::user()->id
		);

		return redirect()
			->route('admin.inventory.index')
			->with('flash_success', 'Successfully added ' . $req->item_name);
	}

	protected function edit(Request $req, $id) {
		$measurement_unit = [];
		$item = Inventory::withTrashed()->find($id);

		foreach (Inventory::select('measurement_unit')->withTrashed()->distinct()->get() as $i)
			array_push($measurement_unit, $i->measurement_unit);

		if ($item == null) {
			return redirect()
				->route('admin.inventory.index')
				->with('flash_error', 'The item either does not exists or is already deleted.');
		}

		return view('admin.inventory.edit', [
			'item' => $item,
			'measurement_unit' => $measurement_unit
		]);
	}

	protected function update(Request $req, $id) {
		$item = Inventory::withTrashed()->find($id);

		if ($item == null) {
			return redirect()
				->route('admin.inventory.index')
				->with('flash_error', 'The item either does not exists or is already deleted.');
		}

		$validator = Validator::make($req->all(), [
			'item_name' => 'required|string|max:255',
			'quantity' => 'required|integer|max:4294967295',
			'measurement_unit' => 'required|string|max:50',
			'critical_level' => 'integer|min:0',
		], [
			'item_name.required' => 'Item name is required',
			'item_name.string' => 'Item name should be a string',
			'item_name.max' => 'Item name should not be longer than 255 characters',
			'quantity.required' => 'Quantity is required',
			'quantity.integer' => 'Quantity should be a number',
			'quantity.max' => 'Quantity should not exceed 4,294,967,295',
			'measurement_unit.required' => 'A unit of measurement is required',
			'measurement_unit.string' => 'Unit of measurement should be a string',
			'measurement_unit.max' => 'Unit of measurement should not be longer than 50 characters',
			'critical_level.integer' => 'Critical level should be a number',
			'critical_level.min' => 'Critical level should not be below 0',
		]);

		if ($validator->fails()) {
			Log::debug($validator->messages());
			return redirect()
				->back()
				->withErrors($validator)
				->withInput();
		}

		try {
			DB::beginTransaction();

			$item->item_name = $req->item_name;
			$item->quantity = $req->quantity;
			$item->measurement_unit = $req->measurement_unit;
			$item->critical_level = $req->critical_level;
			
			if ($req->is_active == null || $req->quantity <= 0) {
				$item->delete();
			} else {
				$item->restore();
			}

			$item->save();

			DB::commit();
		} catch (Exception $e) {
			DB::rollback();
			Log::error($e);

			return redirect()
				->route('admin.inventory.index')
				->with('flash_error', 'Something went wrong, please try again later');
		}

		ActivityLog::log(
			"Item '{$item->item_name}' updated.",
			$item->id,
			"Inventory",
			Auth::user()->id
		);

		return redirect()
			->route('admin.inventory.index')
			->with('flash_success', 'Successfully updated item.');
	}

	protected function increase(Request $req, $id) {
		$item = Inventory::withTrashed()->find($id);

		if ($item == null) {
			return redirect()
				->route('admin.inventory.index')
				->with('flash_error', 'The item either does not exists or is already deleted.');
		}

		$validator = Validator::make($req->all(), [
			'quantity' => 'required|integer|max:4294967295',
		], [
			'quantity.required' => 'Quantity is required',
			'quantity.integer' => 'Quantity should be a number',
			'quantity.max' => 'Quantity should not exceed 4,294,967,295',
		]);

		if ($validator->fails()) {
			Log::debug($validator->messages());
			return response()
				->json([
					''
				]);
		}

		$prevCount = 0;
		try {
			DB::beginTransaction();
			$prevCount = $item->quantity;

			$item->quantity = $prevCount + $req->quantity;
			$item->save();

			DB::commit();
		} catch (Exception $e) {
			DB::rollback();
			Log::error($e);

			return redirect()
				->route('admin.inventory.index')
				->with('flash_error', 'Something went wrong, please try again later');
		}

		ActivityLog::log(
			"Item '{$item->item_name}' increased by '{$req->quantity}' from '{$prevCount}' to '{$item->quantity}'.",
			$item->id,
			"Inventory",
			Auth::user()->id
		);

		return response()
			->json([
				''
			]);
	}

	protected function delete(Request $req, $id) {
		$item = Inventory::find($id);

		if ($item == null) {
			return redirect()
				->route('admin.inventory.index')
				->with('flash_error', 'The item either does not exists or is already deleted.');
		}

		try {
			DB::beginTransaction();			
			$item->delete();
			DB::commit();
		} catch (Exception $e) {
			DB::rollback();
			Log::error($e);

			return redirect()
				->route('admin.inventory.index')
				->with('flash_error', 'Something went wrong, please try again later');
		}

		ActivityLog::log(
			"Item '{$item->item_name}' deactivated.",
			$item->id,
			"Inventory",
			Auth::user()->id
		);

		return redirect()
			->back()
			->with('flash_success', 'Successfully deactivated item.');
	}

	protected function restore(Request $req, $id) {
		$item = Inventory::withTrashed()->find($id);

		if ($item == null) {
			return redirect()
				->route('admin.inventory.index')
				->with('flash_error', 'Item either does not exists or is already deleted permanently.');
		}
		else if (!$item->trashed()) {
			return redirect()
				->back()
				->with('flash_error', 'The item is already activated.');
		}

		try {
			DB::beginTransaction();
			$item->restore();
			DB::commit();
		} catch (Exception $e) {
			DB::rollback();
			Log::error($e);

			return redirect()
				->route('admin.inventory.index')
				->with('flash_error', 'Something went wrong, please try again later');
		}

		ActivityLog::log(
			"Item '{$item->item_name}' activated.",
			$item->id,
			"Inventory",
			Auth::user()->id
		);

		return redirect()
			->back()
			->with('flash_success', 'Successfully activated item.');
	}

	protected function permaDelete(Request $req, $id) {
		$item = Inventory::withTrashed()->find($id);

		if ($item == null) {
			return redirect()
				->route('admin.inventory.index')
				->with('flash_error', 'Item either does not exists or is already deleted.');
		}

		try {
			DB::beginTransaction();
			
			$item->forceDelete();
			
			DB::commit();
		} catch (Exception $e) {
			DB::rollback();
			Log::error($e);

			return redirect()
				->route('admin.inventory.index')
				->with('flash_error', 'Something went wrong, please try again later');
		}

		ActivityLog::log(
			"Item '{$item->item_name}' permanently deleted.",
			null,
			"Inventory",
			Auth::user()->id
		);

		ActivityLog::itemDeleted($id);

		return redirect()
			->route('admin.inventory.index')
			->with('flash_success', 'Successfully removed the announcement permanently');
	}
}
