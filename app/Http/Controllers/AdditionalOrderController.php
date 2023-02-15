<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\AdditionalOrder;
use App\Booking;
use App\Inventory;
use App\Menu;
use App\Settings;

use Auth;
use DB;
use Exception;
use Log;
use Validator;

class AdditionalOrderController extends Controller
{
	const CONFIRM_PASS_MSG = [
		"delete" => "You are executing a sensitive action (voiding an order). This requires further authentication confirmation."
	];

	protected function index($id) {
		$additionalOrders = Booking::find($id)
			->additionalOrders()
			->withTrashed()
			->get();

		return view('admin.bookings.additional-orders.index', [
			'booking_id' => $id,
			'additional_orders' => $additionalOrders
		]);
	}

	protected function create($id) {
		$booking = Booking::find($id);
		
		if ($booking == null) {
			return redirect()
				->route('admin.bookings.index')
				->with('flash_info', "Booking either does not exists or is already deleted");
		}

		$menu = Menu::get();

		return view('admin.bookings.additional-orders.create', [
			'booking' => $booking,
			'menus' => $menu
		]);
	}

	protected function store(Request $req, $id) {
		$booking = Booking::find($id);
		
		if ($booking == null) {
			return redirect()
				->route('admin.bookings.index')
				->with('flash_info', "Booking either does not exists or is already deleted");
		}

		extract(AdditionalOrder::validate($req, $booking));

		if ($validator->fails()) {
			return redirect()
				->back()
				->withErrors($validator->messages())
				->withInput();
		}

		try {
			DB::beginTransaction();

			// Create the additional order entry
			$additionalOrder = AdditionalOrder::create([
				'booking_id' => $booking->id,
				'extension' => $req->extension,
				'price' => $req->price
			]);

			// Attach menus for the additional order
			$menus = array();
			foreach ($req->menu as $i => $m) {
				if (array_key_exists("{$m}", $menus))
					$menus["{$m}"]["count"] += $req->count[$i];
				else
					$menus["{$m}"] = ["count" => $req->count[$i]];
			}
			$additionalOrder->menus()
				->attach($menus);

			DB::commit();
		} catch (Exception $e) {
			DB::rollback();
			Log::error($e);

			return redirect()
				->route('admin.bookings.additional-orders.index', [$booking->id])
				->with('flash_error', 'Something went wrong, please try again later');
		}

		ActivityLog::log(
			"Created additional order for order #{$booking->control_no}",
			$additionalOrder->id,
			"AdditionalOrder",
			Auth::user()->id,
			false
		);

		return redirect()
			->route('admin.bookings.additional-orders.index', [$booking->id])
			->with('flash_success', 'Successfully added additional order');
	}

	protected function show($booking_id, $order_id) {
		$booking = Booking::withTrashed()
			->find($booking_id);

		if ($booking == null) {
			return redirect()
				->route('admin.bookings.index')
				->with('flash_info', "Booking either does not exists or is already deleted");
		}

		$additionalOrder = AdditionalOrder::withTrashed()
			->with([
				"bookingMenus",
				"bookingMenus.menu"
			])
			->find($order_id);

		if ($additionalOrder == null) {
			return redirect()
				->route('admin.bookings.additional-orders.index', [$booking_id])
				->with('flash_info', "Order either does not exists or is already voided");
		}

		return view('admin.bookings.additional-orders.show', [
			'booking_id' => $booking_id,
			'additionalOrder' => $additionalOrder
		]);
	}

	protected function edit($booking_id, $order_id) {
		$booking = Booking::find($booking_id);
		if ($booking == null) {
			return redirect()
				->route('admin.bookings.index')
				->with('flash_info', "Booking either does not exists or is already deleted");
		}

		$additionalOrder = AdditionalOrder::find($order_id);
		if ($additionalOrder == null) {
			return redirect()
				->route('admin.bookings.additional-orders.index', [$booking_id])
				->with('flash_info', "Order either does not exists or is already voided");
		}

		$menu = Menu::get();


		return view('admin.bookings.additional-orders.edit', [
			'booking' => $booking,
			'additionalOrder' => $additionalOrder,
			'menus' => $menu
		]);
	}

	protected function update(Request $req, $booking_id, $order_id) {
		$booking = Booking::find($booking_id);
		
		if ($booking == null) {
			return redirect()
				->route('admin.bookings.index')
				->with('flash_info', "Booking either does not exists or is already deleted");
		}

		$additionalOrder = AdditionalOrder::with(["bookingMenus", "bookingMenus.menu"])
			->find($order_id);
		if ($additionalOrder == null) {
			return redirect()
				->route('admin.bookings.additional-orders.index', [$booking_id])
				->with('flash_info', "Order either does not exists or is already voided");
		}

		extract(AdditionalOrder::validate($req, $booking));

		if ($validator->fails()) {
			return redirect()
				->back()
				->withErrors($validator->messages())
				->withInput();
		}

		try {
			DB::beginTransaction();

			// Create the additional order entry
			$additionalOrder->extension = $req->extension;
			$additionalOrder->price = $req->price;
			$additionalOrder->save();

			// Attach menus for the additional order
			$menus = array();
			foreach ($req->menu as $i => $m) {
				if (array_key_exists("{$m}", $menus))
					$menus["{$m}"]["count"] += $req->count[$i];
				else
					$menus["{$m}"] = ["count" => $req->count[$i]];
			}
			$additionalOrder->menus()
				->sync($menus);

			DB::commit();
		} catch (Exception $e) {
			DB::rollback();
			Log::error($e);

			return redirect()
				->route('admin.bookings.additional-orders.index', [$booking->id])
				->with('flash_error', 'Something went wrong, please try again later');
		}

		ActivityLog::log(
			"Updated additional order for order #{$booking->control_no}",
			$additionalOrder->id,
			"AdditionalOrder",
			Auth::user()->id,
			false
		);

		return redirect()
			->route('admin.bookings.additional-orders.index', [$booking->id])
			->with('flash_success', 'Successfully added additional order');
	}

	protected function delete($booking_id, $order_id) {
		$booking = Booking::find($booking_id);
		if ($booking == null) {
			return redirect()
				->route('admin.bookings.index')
				->with('flash_info', "Booking either does not exists, already deleted, or already voided");
		}

		$additionalOrder = AdditionalOrder::find($order_id);
		if ($additionalOrder == null) {
			return redirect()
				->route('admin.bookings.additional-orders.index', [$booking_id])
				->with('flash_info', "Order either does not exists or is already voided");
		}

		try {
			DB::beginTransaction();

			$additionalOrder->delete();
			$additionalOrder->save();

			DB::commit();
		} catch (Exception $e) {
			DB::rollback();
			Log::error($e);

			return redirect()
				->route('admin.bookings.additional-orders.index', [$booking_id])
				->with("flash_error", "Something went wrong, please try again later");
		}

		return redirect()
			->route('admin.bookings.additional-orders.index', [$booking_id])
			->with("flash_success", "Successfully voided the order");
	}
}