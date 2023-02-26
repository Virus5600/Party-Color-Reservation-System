<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Orderable extends Model
{
	use HasFactory;

	protected $fillable = [
		'menu_variation_id',
		'count'
	];

	// Relationships
	public function menuVariation() { return $this->belongsTo('App\MenuVariation', 'menu_variation_id', 'id'); }
	public function booking() { return $this->morphTo(); }
	public function additionalOrder() { return $this->morphTo(); }
}