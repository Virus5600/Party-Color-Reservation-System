<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

use NumberFormatter;

class Menu extends Model
{
	use HasFactory, SoftDeletes;

	protected $fillable = [
		'name',
		'price',
		'duration'
	];
	
	protected $casts = [
		'created_at' => 'datetime: M d, Y h:i A',
		'updated_at' => 'datetime: M d, Y h:i A',
		'deleted_at' => 'datetime: M d, Y h:i A',
		'duration' => 'datetime: H:i'
	];

	// Relationships
	public function items() { return $this->belongsToMany('App\Inventory', 'menu_items', 'menu_id', 'inventory_id'); }
	public function menuItems() { return $this->hasMany('App\MenuItem', 'menu_id', 'id'); }

	// Custom Functions
	public function getPrice() {
		$locale = app()->currentLocale();
		
		return (new NumberFormatter("{$locale}@currency=JPY", NumberFormatter::CURRENCY))->getSymbol(NumberFormatter::CURRENCY_SYMBOL) . number_format($this->price, 2);
	}
}