<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Orderable extends Model
{
	use HasFactory;

	protected $fillable = [
		'menu_id',
		'count'
	];

	// Relationships
	public function menu() { return $this->belongsTo('App\Menu', 'menu_id', 'id'); }
	public function booking() { return $this->morphTo(); }
	public function additionalOrder() { return $this->morphTo(); }
}