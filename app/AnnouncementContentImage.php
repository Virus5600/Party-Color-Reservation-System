<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AnnouncementContentImage extends Model
{
	protected $fillable = [
		'announcement_id',
		'image_name'
	];

	// Relation
	protected function announcement() { return $this->belongsTo('App\Announcement', 'announcement_id', 'id'); }

	// Custom Function
	public function getImage() {
		return asset('uploads/announcements/'.$this->announcement_id.'/'.$this->image_name);
	}

	// STATIC FUNCTIONS
	public static function showRoute($id) {
		return route('admin.announcements.show', [AnnouncementContentImage::find($id)->announcement_id]);
	}
}