<?php

namespace App\Jobs;

use Illuminate\Http\Request;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

use App\Announcement;
use App\AnnouncementContentImage;

use DB;
use DOMDocument;
use Exception;
use File;
use Log;
use Storage;
use Validator;

class CreateAnnouncement implements ShouldQueue
{
	use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

	private $req

	/**
	 * Create a new job instance.
	 *
	 * @return void
	 */
	public function __construct(Request $req) {
		$this->req = $req;
	}

	/**
	 * Execute the job.
	 *
	 * @return void
	 */
	public function handle() {
		//
	}
}