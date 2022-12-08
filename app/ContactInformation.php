<?php

namespace App;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class ContactInformation extends Model
{
	protected $fillable = [
		'reservation_id',
		'email',
		'phone_numbers'
	];

	// Accessor
	protected function data(): Attribute {
		return Attribute::make(
			get: fn ($value) => json_decode($value, true),
			set: fn ($value) => json_encode($value),
		);
	}

	// Relationships
	public function reservation() { return $this->belongsTo('App\Reservation', 'reservation_id', 'id'); }
}