<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Announcement;
use App\Booking;
use App\ContactInformation; # added
use App\ActivityLog; # added

use DB;
use Exception;
use Log;

class ReactApiController extends Controller
{
	// ANNOUNCEMENTS
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
					'errors' => $validator->messages(),
					'newContactIndex' => $newContactIndex
				]);
		}

		try {
			DB::beginTransaction();

			$price = 0;

			foreach ($menu as $v)
				$price += $v->price;
			$price *= $req->pax;
			$price += ($req->extension * 500);

			$booking = Booking::create([
				'start_at' => $start_at,
				'end_at' => $end_at,
				'reserved_at' => $req->booking_date,
				'extension' => $req->extension,
				'price' => $price,
				'pax' => $req->pax,
				'phone_numbers' => implode("|", $req->phone_numbers)
			]);

			$booking->menus()->attach($req->menu);

			$iterations = max(count($req->contact_name), count($req->contact_email));
			for ($i = 0; $i < $iterations; $i++) {
				$ci = ContactInformation::create([
					'contact_name' => $req->contact_name["{$i}"],
					'email' => $req->contact_email["{$i}"],
					'booking_id' => $booking->id
				]);
			}

			// Reduce the inventoy for realtime update
			foreach ($booking->menus as $m) {
				$response = $m->reduceInventory();

				if (!$response->success) {
					throw new Exception($response->message);
				}
			}

			DB::commit();
		} catch (Exception $e) {
			DB::rollback();
			Log::error($e);

			return response()
				->json([
					'success' => false,
					'type' => 'error',
					'flash_error' => 'Something went wrong, please try again later'
				]);
		}

		ActivityLog::log(
			"Booking created through user form.",
			null,
			false
		);

		return response()
			->json([
				'success' => true,
				'flash_success' => 'Successfully added a new booking'
			]);
	}
}