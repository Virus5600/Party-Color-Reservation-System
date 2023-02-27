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
		$announcementContentImage = AnnouncementContentImage::find($id);

		if ($announcementContentImage == null)
			return "javascript:SwalFlash.info(`Cannot Find Item`, `Item may already be deleted or an anonymous user.`, true, false, `center`, false);";
		return route('admin.announcements.show', [$announcementContentImage->announcement_id]);
	}
}