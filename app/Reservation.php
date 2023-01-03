<?php

namespace App;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

use Carbon\Carbon;

use Log;

class Reservation extends Model
{
	protected $fillable = [
		'start_at',
		'end_at',
		'reserved_at',
		'extension',
		'price',
		'pax',
		'phone_numbers',
		'archived',
		'approved',
		'cancelled',
		'reason',
	];

	protected $casts = [
		'reserved_at' => 'date'
	];

	// Accessor
	protected function getStartAtAttribute($value) {
		return Carbon::createFromFormat('H:i:s', $value)->format('H:i');
	}

	protected function getEndAtAttribute($value) {
		return Carbon::createFromFormat('H:i:s', $value)->format('H:i');
	}

	protected function getReservedAtAttribute($value) {
		return Carbon::createFromFormat('Y-m-d', $value)->format('Y-m-d');
	}

	// Relationships
	public function menus() { return $this->belongsToMany('App\Menu', 'reservation_menus'); }
	public function contactInformation() { return $this->hasMany('App\ContactInformation'); }

	// Public Function
	public function getStatus() {
		$start = Carbon::parse("{$this->reserved_at} {$this->start_at}");
		$end = Carbon::parse("{$this->reserved_at} {$this->end_at}");
		$now = Carbon::parse(now()->timezone("Asia/Manila")->format("Y-m-d H:i:s"));
		
		$toCome = $now->lt($start);
		$between = $now->between($start, $end);
		$done = $now->gt($end);

		if ($this->cancelled == 1)
			return Status::Cancelled;

		if ($toCome)
			return Status::Coming;
		else if ($between)
			return Status::Happening;
		else if ($done)
			return Status::Done;
		else {
			Log::info("Reservation does not match the three status; returning \"NonExistent\" as value.", ["Reservation" => $this]);
			return Status::NonExistent;
		}
	}

	public function getApprovalStatus() {
		$approved = $this->approved;

		if ($this->cancelled == 1)
			return Status::Cancelled;

		if ($approved == -1)
			return ApprovalStatus::Rejected;
		else if ($approved == 1)
			return ApprovalStatus::Approved;
		else
			return ApprovalStatus::Pending;
	}

	public function getOverallStatus() {
		$approvalStatus = $this->getApprovalStatus();
		$reservationStatus = $this->getStatus();

		if ($approvalStatus == ApprovalStatus::Approved)
			return $reservationStatus;
		else
			return $approvalStatus;
	}

	public function getStatusColorCode($status) {
		switch ($status) {
			case Status::Coming:
				return "#17a2b8";
			
			case Status::Happening:
				return "#007bff";

			case Status::Done:
				return "#6c757d";

			case ApprovalStatus::Pending:
				return "#ffc107";

			case ApprovalStatus::Approved:
				return "#28a745";

			case ApprovalStatus::Rejected:
			case Status::Cancelled:
				return "#dc3545";

			default:
				return "#1e2b37";
		}
	}

	public function getStatusText($status) {
		switch ($status) {
			case Status::Coming:
				return "Coming";
			
			case Status::Happening:
				return "Happening";

			case Status::Done:
				return "Done";

			case Status::Cancelled:
				return "Cancelled";

			case ApprovalStatus::Pending:
				return "Pending";

			case ApprovalStatus::Approved:
				return "Approved";

			case ApprovalStatus::Rejected:
				return "Rejected";

			default:
				return "Unknown";
		}
	}
}

// ENUMS
abstract class Status {
	const Coming = 0;
	const Happening = 1;
	const Done = 2;
	const Cancelled = 3;
	const NonExistent = 4;
}

abstract class ApprovalStatus {
	const Pending = 10;
	const Approved = 11;
	const Rejected = 12;
}