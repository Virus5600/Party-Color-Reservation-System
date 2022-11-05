<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Inventory;

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
		return view('admin.inventory.create');
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
			'quantity' => 'required|integer|max:4294967295'
		], [
			'item_name.required' => 'Item name is required',
			'quantity.required' => 'Quantity is required',

			'item_name.max' => 'Item name should not be longer than 255 characters',
			'quantity.max' => 'Quantity should not exceed 4,294,967,295',
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
			
			if ($req->is_active == null) {
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

		return redirect()
			->route('admin.inventory.index')
			->with('flash_success', 'Successfully updated item.');
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

		return redirect()
			->back()
			->with('flash_success', 'Successfully deactivated item.');
	}

	protected function edit(Request $req, $id) {
		$item = Inventory::withTrashed()->find($id);

		if ($item == null) {
			return redirect()
				->route('admin.inventory.index')
				->with('flash_error', 'The item either does not exists or is already deleted.');
		}

		return view('admin.inventory.edit', [
			'item' => $item
		]);
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

		return redirect()
			->back()
			->with('flash_success', 'Successfully activated item.');
	}

    protected function store(Request $req) {

		$validator = Validator::make($req->all(), [
			'item_name' => 'required|string|max:255',
			'quantity' => 'required|integer|max:4294967295'
		], [
			'item_name.required' => 'Item name is required',
			'quantity.required' => 'Quantity is required',

			'item_name.max' => 'Item name should not be longer than 255 characters',
			'quantity.max' => 'Quantity should not exceed 4,294,967,295',
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

			$item = Inventory::create([
				'item_name' => $req->item_name,
				'quantity' => $req->quantity
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

		return redirect()
			->route('admin.inventory.index')
			->with('flash_success', 'Successfully added item');
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

			$id = $item->id;

			$item->forceDelete();

			DB::commit();
		} catch (Exception $e) {
			DB::rollback();
			Log::error($e);

			return redirect()
				->route('admin.inventory.index')
				->with('flash_error', 'Something went wrong, please try again later');
		}

		return redirect()
			->route('admin.inventory.index')
			->with('flash_success', 'Successfully removed the announcement permanently');
	}
}
