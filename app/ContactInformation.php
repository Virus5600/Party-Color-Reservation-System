<?php

namespace App;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class ContactInformation extends Model
{
	protected $fillable = [
		'contact_name',
		'booking_id',
		'email',
	];

	// Relationships
	public function booking() { return $this->belongsTo('App\Booking', 'booking_id', 'id'); }

	// STATIC FUNCTIONS
	public static function showRoute($id) {
		$contactInformation = ContactInformation::find($id);

		if ($contactInformation == null)
			return "javascript:SwalFlash.info(`Cannot Find Item`, `Item may already be deleted or an anonymous user.`, true, false, `center`, false);";
		return route('admin.bookings.index', ['cn' => $contactInformation->booking->control_no]);
	}
}