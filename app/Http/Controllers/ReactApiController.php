<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Carbon\Carbon;

use App\Jobs\BookingNotification;

use App\Enum\ApprovalStatus;
use App\Enum\Status;

use App\Announcement;
use App\Booking;
use App\ContactInformation;
use App\MenuVariation;
use App\Settings;

use DB;
use Exception;
use Log;
use Validator;

class ReactApiController extends Controller
{
	// ANNOUNCEMENTS
	protected function fetchSingleAnnouncement(Request $req, $id)
	{
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

	protected function fetchAnnouncements(Request $req)
	{
		$announcements = Announcement::select(
			DB::raw("
				`id`,
				CONCAT('" . asset('uploads/announcements/{id}') . "/', `poster`) as `poster`,
				`slug`,
				`summary`,
				`title`,
				`created_at`,
				`content`
			")
		)
			->where('is_draft', '!=', '1')
			->get();

		return response()->json([
			'announcements' => $announcements
		]);
	}

	// RESERVATIONS
	protected function bookingsCreate(Request $req)
	{
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
			]); foreach ($req->menu as $k => $v)
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

			// CREATE MAILER TO THE CONTACT PERSON
			$args = [
				'subject' => 'Reservation Created',
				'reason' => null
			];
			BookingNotification::dispatch($booking, "creation", $args);

			// Logger
			activity('react-api')
				->byAnonymous()
				->on($booking)
				->event('create')
				->withProperties([
					'control_no' => $booking->control_no,
					'booking_type' => $booking->booking_type,
					'start_at' => $booking->start_at,
					'end_at' => $booking->end_at,
					'reserved_at' => $booking->reserved_at,
					'extension' => $booking->extension,
					'price' => $booking->price,
					'pax' => $booking->pax,
					'phone_numbers' => $booking->phone_numbers,
					'special_request' => $booking->special_request
				])
				->log("Reservation #{$booking->control_no} created via the User Reservation.");

			DB::commit();
		} catch (Exception $e) {
			DB::rollback();
			Log::error($e);

			return response()
				->json([
					'success' => false,
					'type' => 'fatal_error',
					'errors' => "Something went wrong, please try again later"
				]);
		}

		return response()
			->json([
				'success' => true,
				'type' => 'success',
				'message' => 'Successfully added a new booking'
			]);
	}

	protected function bookingsShow(Request $req)
	{
		$validator = Validator::make($req->all(), [
			'control_no' => 'required|numeric|between:0,9999999999'
		], [
				'control_no.required' => 'Control number is required',
				'control_no.numeric' => 'Control number is only composed of numbers',
				'control_no.between' => 'Control number is only 10 characters long',
			]);

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

			$booking = Booking::with([
				'primaryContactInformation:booking_id,contact_name,email',
				'menus:duration,menu_id,name,price'
			])
				->where('control_no', '=', $req->control_no)
				->first();

			if ($booking == null) {
				return response()
					->json([
						'success' => false,
						'type' => 'non-existent',
						'errors' => "Reservation either does not exists or is already deleted"
					]);
			}

			extract($this->isValidForFetch($booking));

			if ($doNotReturn) {
				return response()
					->json([
						'success' => false,
						'type' => 'finished',
						'errors' => "Reservation is already {$status}"
					]);
			}

			$booking->makeHidden([
				"id",
				"booking_type",
				"control_no",
				"created_at",
				"updated_at",
				"deleted_at"
			]);

			$status_types = [];
			$sn = array_merge(array_column(Status::cases(), "name"), array_column(ApprovalStatus::cases(), "name"));
			$sv = array_merge(array_column(Status::cases(), "value"), array_column(ApprovalStatus::cases(), "value"));

			for ($i = 0; $i < count($sn); $i++)
				$status_types[$sn[$i]] = $sv[$i];

			DB::commit();
		} catch (Exception $e) {
			DB::rollback();
			Log::error($e);

			return response()
				->json([
					'success' => false,
					'type' => 'fatal_error',
					'errors' => "Something went wrong, please try again later"
				]);
		}

		return response()
			->json([
				'success' => true,
				'type' => 'success',
				'message' => 'Booking fetched Successfully',
				'booking' => $booking,
				'status_types' => $status_types
			]);
	}

	protected function bookingCancellationRequest(Request $req)
	{
		$validator = Validator::make($req->all(), [
			'control_no' => 'required|numeric|between:0,9999999999',
			'reason' => 'required|string|max:255'
		], [
				'control_no.required' => 'Control number is required',
				'control_no.numeric' => 'Control number is only composed of numbers',
				'control_no.between' => 'Control number is only 10 characters long',
				'reason.required' => 'A reason is required',
				'reason.string' => 'Malformed data',
				'reason.max' => 'Character limit reached (255)',
			]);

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

			$booking = Booking::where('control_no', '=', $req->control_no)->first();

			if ($booking == null) {
				return response()
					->json([
						'success' => false,
						'type' => 'non-existent',
						'errors' => "Reservation either does not exists or is already deleted"
					]);
			}

			extract($this->isValidForFetch($booking));

			if ($doNotReturn) {
				return response()
					->json([
						'success' => false,
						'type' => 'finished',
						'errors' => "Reservation is already {$status}"
					]);
			}

			$booking->cancel_requested = 1;
			$booking->cancel_request_reason = $req->reason;
			$booking->save();

			// CREATE MAILER HERE TO NOTIFY CLIENT OF THEIR CANCELLATION
			$args = [
				'subject' => 'Requested Reservation Cancellation',
				'reason' => $booking->cancel_request_reason
			];
			BookingNotification::dispatch($booking, "cancellation request", $args);

			$status_types = [];
			$sn = array_merge(array_column(Status::cases(), "name"), array_column(ApprovalStatus::cases(), "name"));
			$sv = array_merge(array_column(Status::cases(), "value"), array_column(ApprovalStatus::cases(), "value"));

			for ($i = 0; $i < count($sn); $i++)
				$status_types[$sn[$i]] = $sv[$i];

			// LOGGER
			activity('react-api')
				->byAnonymous()
				->on($booking)
				->event('cancel-create')
				->withProperties([
					'control_no' => $booking->control_no,
					'booking_type' => $booking->booking_type,
					'start_at' => $booking->start_at,
					'end_at' => $booking->end_at,
					'reserved_at' => $booking->reserved_at,
					'extension' => $booking->extension,
					'price' => $booking->price,
					'pax' => $booking->pax,
					'phone_numbers' => $booking->phone_numbers,
					'special_request' => $booking->special_request
				])
				->log("Booking #{$req->control_no} received a cancellation request from the customer");

			DB::commit();
		} catch (Exception $e) {
			DB::rollback();
			Log::error($e);

			return response()
				->json([
					'success' => false,
					'type' => 'fatal_error',
					'errors' => "Something went wrong, please try again later"
				]);
		}

		return response()
			->json([
				'success' => true,
				'type' => 'success',
				'message' => 'Booking fetched Successfully',
				'booking' => $booking,
				'status_types' => $status_types
			]);
	}

	protected function bookingRetractCancellationRequest(Request $req)
	{
		$validator = Validator::make($req->all(), [
			'control_no' => 'required|numeric|between:0,9999999999'
		], [
				'control_no.required' => 'Control number is required',
				'control_no.numeric' => 'Control number is only composed of numbers',
				'control_no.between' => 'Control number is only 10 characters long',
			]);

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

			$booking = Booking::where('control_no', '=', $req->control_no)->first();

			if ($booking == null) {
				return response()
					->json([
						'success' => false,
						'type' => 'non-existent',
						'errors' => "Reservation either does not exists or is already deleted"
					]);
			}

			extract($this->isValidForFetch($booking));

			if ($doNotReturn) {
				return response()
					->json([
						'success' => false,
						'type' => 'finished',
						'errors' => "Reservation is already {$status}"
					]);
			}

			$booking->cancel_requested = 0;
			$booking->cancel_request_reason = null;
			$booking->reason = null;
			$booking->save();

			// CREATE MAILER HERE TO NOTIFY CLIENT OF THE RETRACTION OF CANCELLATION
			$args = [
				'subject' => 'Revoked Reservation Cancellation'
			];
			BookingNotification::dispatch($booking, "cancellation revoke", $args);

			$status_types = [];
			$sn = array_merge(array_column(Status::cases(), "name"), array_column(ApprovalStatus::cases(), "name"));
			$sv = array_merge(array_column(Status::cases(), "value"), array_column(ApprovalStatus::cases(), "value"));

			for ($i = 0; $i < count($sn); $i++)
				$status_types[$sn[$i]] = $sv[$i];
			
			// LOGGER
			activity('react-api')
				->byAnonymous()
				->on($booking)
				->event('cancel-revoke')
				->withProperties([
					'control_no' => $booking->control_no,
					'booking_type' => $booking->booking_type,
					'start_at' => $booking->start_at,
					'end_at' => $booking->end_at,
					'reserved_at' => $booking->reserved_at,
					'extension' => $booking->extension,
					'price' => $booking->price,
					'pax' => $booking->pax,
					'phone_numbers' => $booking->phone_numbers,
					'special_request' => $booking->special_request
				])
				->log("Booking #{$req->control_no}'s cancellation request from the customer was retracted");

			DB::commit();
		} catch (Exception $e) {
			DB::rollback();
			Log::error($e);

			return response()
				->json([
					'success' => false,
					'type' => 'fatal_error',
					'errors' => "Something went wrong, please try again later"
				]);
		}

		return response()
			->json([
				'success' => true,
				'type' => 'success',
				'message' => 'Booking fetched Successfully',
				'booking' => $booking,
				'status_types' => $status_types
			]);
	}

	// SETTINGS (ABOUT US)
	protected function fetchSettings(Request $req)
	{
		$aboutUs = [
			'web-logo',
			'web-name',
			'web-desc',
			'address',
			'contacts',
			'emails',
			'opening',
			'closing',
			'day-schedule'
		];

		$days = ["Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"];

		$settings = Settings::select([
			'name',
			'value',
			'is_file'
		])
			->whereIn('name', $aboutUs)
			->get();

		return response()
			->json([
				'settings' => $settings,
				'days' => $days
			]);
	}

	// PRIVATE FUNCTIONS //
	// RESERVATIONS
	private function isValidForFetch(Booking $booking)
	{
		$start = Carbon::parse("{$booking->reserved_at} {$booking->start_at}");
		$end = Carbon::parse("{$booking->reserved_at} {$booking->end_at}");

		$doNotReturn = false;
		$status = "finished";

		if ($booking == null) {
			$doNotReturn = true;
			$status = "No such booking existed";
		}

		if (now()->timezone("Asia/Tokyo")->gt($start) && now()->timezone("Asia/Tokyo")->lt($end)) {
			$doNotReturn = true;
			$status = "ongoing";
		} else if (now()->timezone("Asia/Tokyo")->gt($start) && now()->timezone("Asia/Tokyo")->gt($end)) {
			$doNotReturn = true;
		}

		if (in_array($booking->getOverallStatus(), [Status::Cancelled, Status::Happening, Status::Done, Status::Ghosted, Status::NonExistent, ApprovalStatus::Rejected]))
			$doNotReturn = true;

		return [
			"doNotReturn" => $doNotReturn,
			"status" => strtolower($booking->getStatusText($booking->getOverallStatus()))
		];
	}
}