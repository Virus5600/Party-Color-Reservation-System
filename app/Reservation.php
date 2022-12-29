<?php

namespace App;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

use Carbon\Carbon;

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
}