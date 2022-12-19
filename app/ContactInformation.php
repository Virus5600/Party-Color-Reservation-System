<?php

namespace App;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class ContactInformation extends Model
{
	protected $fillable = [
		'contact_name'
		'reservation_id',
		'email',
		'phone_numbers'
	];

	// Relationships
	public function reservation() { return $this->belongsTo('App\Reservation', 'reservation_id', 'id'); }
}