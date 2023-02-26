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
		return route('admin.bookings.index', ['cn' => ContactInformation::find($id)->booking->control_no]);
	}
}