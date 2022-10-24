<?php

namespace App;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class Announcement extends Model
{

	use SoftDeletes;

	protected $fillable = [
		'poster',
		'title',
		'slug',
		'summary',
		'content',
		'is_draft',
		'user_id',
	];

	protected $casts = [
		'created_at' => 'datetime',
		'updated_at' => 'datetime',
		'deleted_at' => 'datetime',
	];

	// Relationship Function
	protected function user() { return $this->belongsTo('App\User'); }

	// Custom Function
	public function getPoster() {
		return asset('uploads/announcements/'.$this->poster);
	}
}