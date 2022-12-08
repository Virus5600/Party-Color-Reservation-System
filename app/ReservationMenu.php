<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReservationMenu extends Model
{
	use HasFactory;

	protected $fillable = [
		'reservation_id',
		'menu_id',
	];

	// Relationships
	public function reservation() { return $this->belongsTo('App\Reservation', 'reservation_id', 'id'); }
	public function menu() { return $this->belongsTo('App\Menu', 'menu_id', 'id'); }
}