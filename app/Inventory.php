<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class Inventory extends Model
{
    use HasFactory, SoftDeletes;

	protected $fillable = [
		'item_name',
		'quantity',
		'measurement_unit'
	];

    protected $casts = [
		'created_at' => 'datetime',
		'updated_at' => 'datetime',
		'deleted_at' => 'datetime',
	];

	// Relationships
	public function menus() { return $this->belongsToMany('App\Menu', 'menu_items', 'inventory_id', 'menu_id'); }
	public function menuItem() { return $this->belongsTo('App\MenuItem', 'id', 'inventory_id'); }

	// Custom Functions
	public function getInStock() {
		return number_format($this->quantity, 0, ',', ', ') . " {$this->measurement_unit}";
	}
}
