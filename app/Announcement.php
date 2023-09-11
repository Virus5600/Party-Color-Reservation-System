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
	public function user() { return $this->belongsTo('App\User', 'user_id', 'id'); }
	public function announcementContentImages() { return $this->hasMany('App\AnnouncementContentImage', 'announcement_id', 'id'); }

	// Custom Function
	public function getPoster() {
		return asset('uploads/announcements/'.$this->id.'/'.$this->poster);
	}

	public function author() {
		return $this->user->getName();
	}

	// STATIC FUNCTIONS
	public static function showRoute($id) {
		$announcement = Announcement::withTrashed()->find($id);
		
		if ($announcement == null)
			return "javascript:SwalFlash.info(`Cannot Find Item`, `Item may already be deleted or an anonymous user.`, true, false, `center`, false);";
		return route('admin.announcements.show', [$id]);
	}
}