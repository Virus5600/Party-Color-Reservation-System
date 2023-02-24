<?php

namespace App\Http\Controllers;

use Illuminate\Database\Eloquent\Relations\Relation;

use Spatie\Activitylog\Models\Activity;

use Illuminate\Http\Request;

use Carbon\Carbon;

use DB;
use Exception;
use Log;
use ReflectionClass;
use Validator;

class ActivityLogController extends Controller
{
	protected function index(Request $req) {
		$activity = Activity::latest()
			->paginate(25);

		return view('admin.activity-log.index', [
			'activity' => $activity
		]);
	}

	protected function show($id) {
		$log = Activity::find($id);

		if ($log == null) {
			return redirect()
				->route('admin.activity-log.index')
				->with('flash_error', 'Activity either does not exists or is already deleted');
		}

		$subject_type = Relation::getMorphedModel($log->subject_type);
		if ($subject_type == null && $log->log_name == 'middleware') {
			$showRoute = "javascript:SwalFlash.info(`Not Applicable`, `Automated action by the system.`, true, false, `center`, false);";
			$shorthand_subject_type = "System Process";
		}
		else {
			$shorthand_subject_type = (new ReflectionClass($subject_type))->getShortName();
			if ($shorthand_subject_type == 'Activity')
				$showRoute = route('admin.activity-log.show', [$log->subject_id]);
			else
				$showRoute = $log->subject_type == null ? "javascript:SwalFlash.info(`Cannot Find Item`, `Item may already be deleted or an anonymous user.`, true, false, `center`, false);" : $subject_type::showRoute($log->subject_id);
		}

		return view('admin.activity-log.show', [
			'log' => $log,
			'subject_type' => $shorthand_subject_type,
			'showRoute' => $showRoute
		]);
	}

	protected function update(Request $req, $id) {
		$log = Activity::find($id);

		if ($log == null) {
			return response()
				->json([
					'success' => false,
					'message' => 'Activity either does not exists or is already deleted'
				]);

		}

		$validator = Validator::make($req->all(), [
			'reason' => "required|string|max:1000"
		], [
			'reason.required' => "A reason is required",
			'reason.string' => "Reason should be a string",
			'reason.max' => "Reason is capped at 1000 characters"
		]);

		if ($validator->fails()) {
			return response()
			->json([
				'success' => false,
				'message' => $validator->messages()->first()
			]);
		}

		try {
			DB::beginTransaction();

			$log->reason = $req->reason;
			$log->save();

			activity('activity-log')
				->by(auth()->user())
				->on($log)
				->event('update')
				->withProperties([
					'log_name' => $log->log_name,
					'description' => $log->description,
					'subject_type' => $log->subject_type,
					'subject_id' => $log->subject_id,
					'event' => $log->event,
					'causer_type' => $log->causer_type,
					'causer_id' => $log->causer_id,
					'ip_address' => $log->ip_address,
					'is_marked' => $log->is_marked,
					'reason' => $log->reason,
					'properties' => $log->properties,
					'batch_uuid' => $log->batch_uuis,
					'created_at' =>  Carbon::parse($log->created_at)->format("M d, y h:i:s A")
				])
				->log("Updated activity's reason.");

			DB::commit();
		} catch (Exception $e) {
			DB::rollback();
			Log::error($e);

			return response()
				->json([
					'success' => false,
					'message' => "Something went wrong, please try again later"
				]);
		}

		return response()
			->json([
				'success' => true,
				'message' => "Successfully marked activity as suspicious"
			]);
	}

	protected function mark(Request $req, $id) {
		$log = Activity::find($id);

		if ($log == null) {
			return response()
				->json([
					'success' => false,
					'message' => 'Activity either does not exists or is already deleted'
				]);

		}

		$validator = Validator::make($req->all(), [
			'reason' => "required|string|max:1000"
		], [
			'reason.required' => "A reason is required",
			'reason.string' => "Reason should be a string",
			'reason.max' => "Reason is capped at 1000 characters"
		]);

		if ($validator->fails()) {
			return response()
			->json([
				'success' => false,
				'message' => $validator->messages()->first()
			]);
		}

		try {
			DB::beginTransaction();

			$log->is_marked = 1;
			$log->reason = $req->reason;
			$log->save();

			activity('activity-log')
				->by(auth()->user())
				->on($log)
				->event('mark')
				->withProperties([
					'log_name' => $log->log_name,
					'description' => $log->description,
					'subject_type' => $log->subject_type,
					'subject_id' => $log->subject_id,
					'event' => $log->event,
					'causer_type' => $log->causer_type,
					'causer_id' => $log->causer_id,
					'ip_address' => $log->ip_address,
					'is_marked' => $log->is_marked,
					'reason' => $log->reason,
					'properties' => $log->properties,
					'batch_uuid' => $log->batch_uuis,
					'created_at' =>  Carbon::parse($log->created_at)->format("M d, y h:i:s A")
				])
				->log("Marked activity as suspicious");

			DB::commit();
		} catch (Exception $e) {
			DB::rollback();
			Log::error($e);

			return response()
				->json([
					'success' => false,
					'message' => "Something went wrong, please try again later"
				]);
		}

		return response()
			->json([
				'success' => true,
				'message' => "Successfully marked activity as suspicious"
			]);
	}

	protected function unmark(Request $req, $id) {
		$log = Activity::find($id);

		if ($log == null) {
			return response()
				->json([
					'success' => false,
					'message' => 'Activity either does not exists or is already deleted'
				]);

		}

		$validator = Validator::make($req->all(), [
			'reason' => "required|string|max:1000"
		], [
			'reason.required' => "A reason is required",
			'reason.string' => "Reason should be a string",
			'reason.max' => "Reason is capped at 1000 characters"
		]);

		if ($validator->fails()) {
			return response()
			->json([
				'success' => false,
				'message' => $validator->messages()->first()
			]);
		}

		try {
			DB::beginTransaction();

			$log->is_marked = 0;
			$log->reason = $req->reason;
			$log->save();

			activity('activity-log')
				->by(auth()->user())
				->on($log)
				->event('unmark')
				->withProperties([
					'log_name' => $log->log_name,
					'description' => $log->description,
					'subject_type' => $log->subject_type,
					'subject_id' => $log->subject_id,
					'event' => $log->event,
					'causer_type' => $log->causer_type,
					'causer_id' => $log->causer_id,
					'ip_address' => $log->ip_address,
					'is_marked' => $log->is_marked,
					'reason' => $log->reason,
					'properties' => $log->properties,
					'batch_uuid' => $log->batch_uuis,
					'created_at' =>  Carbon::parse($log->created_at)->format("M d, y h:i:s A")
				])
				->log("Unmarked activity as suspicious");

			DB::commit();
		} catch (Exception $e) {
			DB::rollback();
			Log::error($e);

			return response()
				->json([
					'success' => false,
					'message' => "Something went wrong, please try again later"
				]);
		}

		return response()
			->json([
				'success' => true,
				'message' => "Successfully unmarked activity as suspicious"
			]);
	}
}