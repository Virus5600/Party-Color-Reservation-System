<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Settings;
use App\ActivityLog;

use DB;
use Exception;
use File;
use Log;
use Validator;

class SettingsController extends Controller
{
	protected function index() {
		return view('admin.settings.index');
	}

	protected function update(Request $req) {
		$req->merge([
			"email-single" => $req->emails,
			"contact-single" => $req->contacts,
			"emails" => explode(",", $req->emails),
			"contacts" => explode(",", $req->contacts)
		]);

		// Change TaggingJS with Tagify on reservation and finish implementation of opening, closing, and schedule days
		$validator = Validator::make($req->all(), [
			'web-name' => 'required|string|max:16777215',
			'web-desc' => 'required|string|max:16777215',
			'address' => 'required|string|max:16777215',
			'capacity' => 'required|numeric|between:1,2147483647',
			'day-schedule' => 'required|array|min:1',
			'day-schedule.*' => 'required|numeric|between:0,6',
			'contact-single' => 'max:16777215',
			'contacts' => 'required|array',
			'contacts.*' => ['required', 'regex:/^\+*(?=.{7,14})[\d\s-]{7,15}$/'],
			'email-single' => 'max:16777215',
			'emails' => 'required|array',
			'emails.*' => 'required|email',
			'web-logo' => 'max:5120|mimes:jpeg,jpg,png,webp|nullable',
			'opening' => 'required|date_format:H:i',
			'closing' => 'required|date_format:H:i|after:opening',
		], [
			'web-name.required' => 'Website Name is required',
			'web-name.string' => 'Website Name should be a string of characters',
			'web-name.max' => 'Website Name should not exceed 16,777,215 characters',
			'web-desc.required' => 'Website Description is required',
			'web-desc.string' => 'Website Description should be a string of characters',
			'web-desc.max' => 'Website Description should not exceed 16,777,215 characters',
			'address.required' => 'Address is required',
			'address.string' => 'Address should be a string of characters',
			'address.max' => 'Address should not exceed 16,777,215 characters',
			'capacity.required' => 'Capacity is required',
			'capacity.numeric' => 'Capacity should be a number',
			'capacity.between' => 'Capacity should be between 1 and 2,147,483,647',
			'day-schedule.required' => 'At least 1 open date is required',
			'day-schedule.min' => 'At least 1 open date is required',
			'day-schedule.*.required' => 'At least 1 open date is required',
			'day-schedule.*.numeric' => 'Please refrain from tampering the form',
			'day-schedule.*.between' => 'Please refrain from tampering the form',
			'email-single.max' => 'Contacts must not exceed 16,777,215 characters',
			'contacts.max' => 'Contact should not exceed 16,777,215 characters',
			'contacts.*.max' => 'Contact should not exceed 16,777,215 characters',
			'contacts.*.regex' => '":input" is an invalid contact number',
			'email-single.max' => 'Emails must not exceed 16,777,215 characters',
			'emails.required' => 'The email is required',
			'emails.*.required' => 'An email is required',
			'emails.*.email' => '":input" is an invalid email address',
			'web-logo.max' => 'Image should be below 5MB',
			'web-logo.mimes' => 'Selected file doesn\'t match the allowed image formats',
			'opening.required' => 'Opening time is required',
			'opening.date_format' => 'Please refrain froim tampering the form',
			'closing.required' => 'Closing time is required',
			'closing.date_format' => 'Please refrain from modifying the form',
			'closing.after' => 'Closing time must be after the opening time',
		]);

		if ($validator->fails()) {
			return redirect()
				->back()
				->withInput()
				->withErrors($validator);
		}

		$req->request->remove('contact-single');
		$req->request->remove('email-single');

		try {
			DB::beginTransaction();

			foreach ($req->except('_token') as $k => $v) {
				if ($k == 'contacts' || $k == 'emails' || $k == 'day-schedule') {
					$v = implode(',', $v);
				}
				else if ($k == 'web-logo') {
					if ($req->has($k)) {
						$setting = Settings::where('name', '=', $k)->first();

						if ($setting->value != 'default.png')
							File::delete(public_path() . "/uploads/settings/{$setting->value}");
						
						$destination = "uploads/settings";
						$fileType = $req->file($k)->getClientOriginalExtension();
						$v = "favicon.{$fileType}";
						$req->file($k)->move($destination, $v);
					}
					else
						continue;
				}

				$setting = Settings::where('name', '=', $k)->first();

				if ($setting == null) {
					$setting = Settings::create([
						'name' => $k,
						'value' => ($v == null ? 'null' : $v)
					]);
				}
				else {
					$setting->value = $v;
					$setting->save();
				}
			}

			DB::commit();
		} catch (Exception $e) {
			DB::rollback();
			Log::error($e);

			return redirect()
				->route('admin.settings.index')
				->withInput()
				->with('flash_error', 'Something went wrong, please try again later');
		}

		ActivityLog::log(
			"Settings updated.",
			null,
			"Settings",
			Auth::user()->id
		);

		return redirect()
			->route('admin.settings.index')
			->with('flash_success', 'Successfully updated settings');
	}
}