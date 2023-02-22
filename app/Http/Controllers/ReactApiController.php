<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Announcement;
use App\Booking;
use App\ContactInformation;
use App\ActivityLog;

use DB;
use Exception;
use Log;

class ReactApiController extends Controller
{
	// ANNOUNCEMENTS
	protected function fetchSingleAnnouncement(Request $req, $id) {
		$announcement = Announcement::find($id);

		if ($announcement == null) {
			return response()
				->json([
					"success" => false,
					"message_type" => "error",
					"message" => "The announcement either does not exists or is already deleted.",
					"announcement" => null
				]);
		}

		return response()
			->json([
				"success" => true,
				"message_type" => "success",
				"message" => "Announcement exists",
				"announcement" => $announcement
			]);
	}

	protected function fetchAnnouncements(Request $req) {
		$announcements = Announcement::select(
			DB::raw("
				`id`,
				CONCAT('" . asset('uploads/announcements/{id}') . "/', `poster`) as `poster`,
				`slug`,
				`summary`,
				`title`,
				`created_at`,
				`content`
			"))
			->where('is_draft', '!=', '1')
			->get();

		return response()->json([
			'announcements' => $announcements
		]);
	}

	// RESERVATIONS
	protected function bookingsCreate(Request $req) {
		extract(Booking::validate($req));

		if ($validator->fails()) {
			return response()
				->json([
					'success' => false,
					'type' => 'validation',
					'errors' => $validator->messages()
				]);
		}

		try {
			DB::beginTransaction();

			$price = 0;

			foreach ($req->menu as $k => $v)
				$price += (MenuVariation::find($v)->price * $req->amount[$k]);
			$price += ($req->extension * Settings::getValue("extension_fee"));

			$booking = Booking::create([
				'control_no' => Booking::createControlNumber(),
				'booking_type' => 'reservation',
				'start_at' => $start_at,
				'end_at' => $end_at,
				'reserved_at' => $req->booking_date,
				'extension' => $req->extension,
				'price' => $price,
				'pax' => $req->pax,
				'phone_numbers' => implode("|", $req->phone_numbers),
				'special_request' => $req->special_request
			]);

			foreach ($req->menu as $k => $v)
				$booking->menus()
					->attach([
						$v => [
							'count' => $req->amount[$k]
						]
					]);

			$iterations = max(count($req->contact_name), count($req->contact_email));
			for ($i = 0; $i < $iterations; $i++) {
				ContactInformation::create([
					'contact_name' => $req->contact_name["{$i}"],
					'email' => $req->contact_email["{$i}"],
					'booking_id' => $booking->id
				]);
			}

			// Logger
			ActivityLog::log(
				"Reservation #{$booking->control_no} created via the User Reservation.",
				$booking->id,
				"Booking",
				null,
				false,
				false
			);

			DB::commit();
		} catch (Exception $e) {
			DB::rollback();
			Log::error($e);

			return redirect()
				->route('admin.bookings.index')
				->with('flash_error', 'Something went wrong, please try again later');
		}

		return redirect()
			->route('admin.bookings.index')
			->with('flash_success', 'Successfully added a new booking');
	}
}