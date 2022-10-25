<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Announcement;

use Auth;
use DB;
use Exception;
use File;
use Location;
use Log;
use Mail;
use Validator;

class AnnouncementController extends Controller
{
	protected function index(Request $req) {

		if (($req->has('sd') && $req->sd == 1) && ($req->has('d') && $req->d == 1))
			$announcements = Announcement::withTrashed()->get();
		else if ($req->has('sd') && $req->sd == 1)
			$announcements = Announcement::onlyTrashed()->get();
		else if ($req->has('d') && $req->d == 1)
			$announcements = Announcement::where('is_draft', '=', '1')->get();
		else
			$announcements = Announcement::where('is_draft', '=', '0')->get();

		return view('admin.announcements.index', [
			'announcements' => $announcements,
			'show_softdeletes' => ($req->has('sd') && $req->sd == 1 ? true : false),
			'show_drafts' => ($req->has('d') && $req->d == 1 ? true : false),
		]);
	}

	protected function create(Request $req) {
		return view('admin.announcements.create', [
			'show_softdeletes' => $req->sd,
			'show_drafts' => $req->d
		]);
	}

	protected function store(Request $req) {
		// If the is_draft is not checked, set it to 0 since php returns nothing if a boolean is god damn false...
		if (!$req->has('is_draft'))
			$req->request->set('is_draft', '0');

		$validator = Validator::make($req->all(), [
			'image' => 'required|mimes:jpeg,jpg,png,webp|max:5120',
			'title' => 'required|string|max:255',
			'summary' => 'string|max:255',
			'content' => 'required|string|max:16777215'
		], [
			'image.required' => 'A poster is required',
			'image.mimes' => 'Selected file does not match the allowed formats',
			'image.max' => 'Max allowed file size is 5MB',
			'title.required' => 'A title is required',
			'title.string' => 'A title should only cosist of string characters',
			'title.max' => 'Title should not be longer than 255 characters',
			'summary.string' => 'The summary should only cosist of string characters',
			'summary.max' => 'The summary should not be longer than 255 characters',
			'content.required' => 'The announcement\'s content is required',
			'content.string' => 'Content should only cosist of string characters',
			'content.max' => 'Content length exceeded 64MB...'
		]);

		if ($validator->fails()) {
			Log::debug($validator->messages());
			return redirect()
				->back()
				->withErrors($validator)
				->withInput();
		}

		try {
			DB::beginTransaction();

			$slug = preg_replace('/\s+/', '_', $req->title);

			$image = 'default.png';
			// FILE HANDLING
			if ($req->has('image')) {
				$destination = 'uploads/announcements';
				$fileType = $req->file('image')->getClientOriginalExtension();
				$image = $slug . "-" . uniqid() . "ポスター." . $fileType;
				$req->file('image')->move($destination, $image);
			}

			Announcement::create([
				'poster' => $image,
				'title' => $req->title,
				'slug' => $slug,
				'summary' => $req->summary,
				'content' => $req->content,
				'is_draft' => $req->is_draft ? 1 : 0,
				'user_id' => Auth::user()->id
			]);

			DB::commit();
		} catch (Exception $e) {
			DB::rollback();
			Log::error($e);

			return redirect()
				->route('admin.announcements.index', ['d' => $req->d, 'sd' => $req->sd])
				->with('flash_error', 'Something went wrong, please try again later');
		}

		return redirect()
			->route('admin.announcements.index', ['d' => $req->d, 'sd' => $req->sd])
			->with('flash_success', 'Successfully uploaded announcement');
	}

	protected function show(Request $req, $id) {
		if ($req->has('sd') && $req->sd)
			$announcement = Announcement::withTrashed()->find($id);
		else
			$announcement = Announcement::find($id);

		if ($announcement == null) {
			return redirect()
				->route('admin.announcements.index', ['d' => $req->d, 'sd' => $req->sd])
				->with('flash_error', 'The announcement either does not exists or is already deleted.');
		}

		return view('admin.announcements.show', [
			'announcement' => $announcement,
			'show_softdeletes' => $req->has('sd') ? $req->sd : 0,
			'show_drafts' => $req->has('d') ? $req->d : 0
		]);
	}

	protected function edit(Request $req, $id) {
		if ($req->has('sd') && $req->sd)
			$announcement = Announcement::withTrashed()->find($id);
		else
			$announcement = Announcement::find($id);

		if ($announcement == null) {
			return redirect()
				->route('admin.announcements.index', ['show_drafts' => $req->d, 'show_softdeletes' => $req->sd])
				->with('flash_error', 'The announcement either does not exists or is already deleted.');
		}

		return view('admin.announcements.edit', [
			'announcement' => $announcement,
			'show_softdeletes' => $req->has('sd') ? $req->sd : 0,
			'show_drafts' => $req->has('d') ? $req->d : 0
		]);
	}

	protected function update(Request $req, $id) {
		if ($req->has('sd') && $req->sd)
			$announcement = Announcement::withTrashed()->find($id);
		else
			$announcement = Announcement::find($id);

		if ($announcement == null) {
			return redirect()
				->route('admin.announcements.index', ['d' => $req->d, 'sd' => $req->sd])
				->with('flash_error', 'The announcement either does not exists or is already deleted.');
		}

		// If the is_draft is not checked, set it to 0 since php returns nothing if a boolean is god damn false...
		if (!$req->has('is_draft'))
			$req->request->set('is_draft', '0');

		$validator = Validator::make($req->all(), [
			'image' => 'nullable|mimes:jpeg,jpg,png,webp|max:5120',
			'title' => 'required|string|max:255',
			'summary' => 'string|max:255',
			'content' => 'required|string|max:16777215'
		], [
			'image.mimes' => 'Selected file does not match the allowed formats',
			'image.max' => 'Max allowed file size is 5MB',
			'title.required' => 'A title is required',
			'title.string' => 'A title should only cosist of string characters',
			'title.max' => 'Title should not be longer than 255 characters',
			'summary.string' => 'The summary should only cosist of string characters',
			'summary.max' => 'The summary should not be longer than 255 characters',
			'content.required' => 'The announcement\'s content is required',
			'content.string' => 'Content should only cosist of string characters',
			'content.max' => 'Content length exceeded 64MB...'
		]);

		if ($validator->fails()) {
			Log::debug($validator->messages());
			return redirect()
				->back()
				->withErrors($validator)
				->withInput();
		}

		try {
			DB::beginTransaction();

			$slug = preg_replace('/\s+/', '_', $req->title);

			$image = 'default.png';
			// FILE HANDLING
			if ($req->has('image')) {
				$destination = 'uploads/announcements';
				$fileType = $req->file('image')->getClientOriginalExtension();
				$image = $slug . "-" . uniqid() . "ポスター." . $fileType;
				$req->file('image')->move($destination, $image);
				$announcement->poster = $image;
			}

			$announcement->title = $req->title;
			$announcement->slug = $slug;
			$announcement->summary = $req->summary;
			$announcement->content = $req->content;
			$announcement->is_draft = $req->is_draft ? 1 : 0;
			$announcement->save();

			DB::commit();
		} catch (Exception $e) {
			DB::rollback();
			Log::error($e);

			return redirect()
				->route('admin.announcements.index', ['d' => $req->d, 'sd' => $req->sd])
				->with('flash_error', 'Something went wrong, please try again later');
		}

		return redirect()
			->route('admin.announcements.index', ['d' => $req->d, 'sd' => $req->sd])
			->with('flash_success', 'Successfully uploaded announcement');
	}

	protected function publish(Request $req, $id) {
		$announcement = Announcement::find($id);

		if ($announcement == null) {
			return redirect()
				->route('admin.announcements.index', ['d' => $req->d, 'sd' => $req->sd])
				->with('flash_error', 'The announcement either does not exists or is already deleted.');
		}

		try {
			DB::beginTransaction();
			
			$announcement->is_draft = 0;
			$announcement->save();

			DB::commit();
		} catch (Exception $e) {
			DB::rollback();
			Log::error($e);

			return redirect()
				->route('admin.announcements.index', ['d' => $req->d, 'sd' => $req->sd])
				->with('flash_error', 'Something went wrong, please try again later');
		}

		return redirect()
			->back()
			->with('flash_success', 'Successfully published announcement');
	}

	protected function unpublish(Request $req, $id) {
		$announcement = Announcement::find($id);

		if ($announcement == null) {
			return redirect()
				->route('admin.announcements.index', ['d' => $req->d, 'sd' => $req->sd])
				->with('flash_error', 'The announcement either does not exists or is already deleted.');
		}

		try {
			DB::beginTransaction();
			
			$announcement->is_draft = 1;
			$announcement->save();

			DB::commit();
		} catch (Exception $e) {
			DB::rollback();
			Log::error($e);

			return redirect()
				->route('admin.announcements.index', ['d' => $req->d, 'sd' => $req->sd])
				->with('flash_error', 'Something went wrong, please try again later');
		}

		return redirect()
			->back()
			->with('flash_success', 'Successfully unpublished announcement');
	}

	protected function delete(Request $req, $id) {
		$announcement = Announcement::find($id);

		if ($announcement == null) {
			return redirect()
				->route('admin.announcements.index', ['d' => $req->d, 'sd' => $req->sd])
				->with('flash_error', 'The announcement either does not exists or is already deleted.');
		}

		try {
			DB::beginTransaction();			
			$announcement->delete();
			DB::commit();
		} catch (Exception $e) {
			DB::rollback();
			Log::error($e);

			return redirect()
				->route('admin.announcements.index', ['d' => $req->d, 'sd' => $req->sd])
				->with('flash_error', 'Something went wrong, please try again later');
		}

		return redirect()
			->back()
			->with('flash_success', 'Successfully moved announcement to trash');
	}

	protected function restore(Request $req, $id) {
		$announcement = Announcement::withTrashed()->find($id);

		if ($announcement == null) {
			return redirect()
				->route('admin.announcements.index', ['d' => $req->d, 'sd' => $req->sd])
				->with('flash_error', 'Announcement either does not exists or is already deleted permanently.');
		}
		else if (!$announcement->trashed()) {
			return redirect()
				->back()
				->with('flash_error', 'The announcement is already restored.');
		}

		try {
			DB::beginTransaction();
			$announcement->restore();
			DB::commit();
		} catch (Exception $e) {
			DB::rollback();
			Log::error($e);

			return redirect()
				->route('admin.announcements.index', ['d' => $req->d, 'sd' => $req->sd])
				->with('flash_error', 'Something went wrong, please try again later');
		}

		return redirect()
			->back()
			->with('flash_success', 'Successfully restored the announcement');
	}

	protected function permaDelete(Request $req, $id) {
		$announcement = Announcement::withTrashed()->find($id);

		if ($announcement == null) {
			return redirect()
				->route('admin.announcements.index', ['d' => $req->d, 'sd' => $req->sd])
				->with('flash_error', 'Announcement either does not exists or is already deleted.');
		}

		try {
			DB::beginTransaction();

			$poster = $announcement->poster == 'default.png' ? null : $announcement->poster;
			$announcement->forceDelete();
			if ($poster != null)
				File::delete(public_path() . '/uploads/announcements/' . $poster);

			DB::commit();
		} catch (Exception $e) {
			DB::rollback();
			Log::error($e);

			return redirect()
				->route('admin.announcements.index', ['d' => $req->d, 'sd' => $req->sd])
				->with('flash_error', 'Something went wrong, please try again later');
		}

		return redirect()
			->route('admin.announcements.index', ['d' => $req->d, 'sd' => $req->sd])
			->with('flash_success', 'Successfully removed the announcement permanently');
	}
}