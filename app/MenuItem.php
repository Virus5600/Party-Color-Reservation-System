<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MenuItem extends Model
{
	use HasFactory;

	protected $fillable = [
		'menu_id',
		'inventory_id',
		'amount'
	];

	protected $with = [
		'menu',
		'item'
	];

	public $timestamps = false;

	// Relationships
	public function menu() { return $this->belongsTo('App\Menu', 'menu_id', 'id'); }
	public function item() { return $this->belongsTo('App\Inventory', 'inventory_id', 'id'); }
}