<?php

namespace App;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class Inventory extends Model
{
    use SoftDeletes;

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

	// Custom Functions
	public function getInStock() {
		return number_format($this->quantity, 0, ',', ', ') . " {$this->measurement_unit}";
	}
}
