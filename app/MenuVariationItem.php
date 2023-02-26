<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MenuVariationItem extends Model
{
	use HasFactory;

	protected $primaryKey = null;

	protected $fillable = [
		'menu_variation_id',
		'inventory_id',
		'amount',
		'is_unlimited',
	];

	protected $with = [
		'menuVariation',
		'item'
	];

	public $timestamps = false;
	public $incrementing = false;

	// Relationships
	public function menuVariation() { return $this->belongsTo('App\MenuVariation', 'menu_variation_id', 'id'); }
	public function item() { return $this->belongsTo('App\Inventory', 'inventory_id', 'id'); }
}