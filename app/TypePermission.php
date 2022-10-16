<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TypePermission extends Model
{
	protected $fillable = [
		'type_id',
		'permission_id'
	];

	// Relationship Functions
	public function type() { return $this->belongsTo('App\Type'); }
	public function permission() { return $this->belongsTo('App\Permission'); }
}