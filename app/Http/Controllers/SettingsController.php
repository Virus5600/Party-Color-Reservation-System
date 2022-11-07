<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Settings;

use DB;
use Exception;
use Log;
use Validator;

class SettingsController extends Controller
{
	protected function index() {
		return view('admin.settings.index');
	}

	protected function update(Request $req) {
		$validator = Validator::make($req->all(), [
			'web-name' => 'required|string|max:255',
			'web-desc' => 'required|string|max:255',
			'address' => 'required|string|max:255',
			'contact' => 'string|max:255',
			'email' => 'required|email|string|max:255',
			'web-logo' => 'max:5120|mimes:jpeg,jpg,png,webp|nullable'
		], [
			'web-name.required' => 'Website Name is required',
			'web-name.string' => 'Website Name should be a string of characters',
			'web-name.max' => 'Website Name should not exceed 255 characters',
			'web-desc.required' => 'Website Description is required',
			'web-desc.string' => 'Website Description should be a string of characters',
			'web-desc.max' => 'Website Description should not exceed 255 characters',
			'address.required' => 'Address is required',
			'address.string' => 'Address should be a string of characters',
			'address.max' => 'Address should not exceed 255 characters',
			'contact.string' => 'Contact should be a string of characters',
			'contact.max' => 'Contact should not exceed 255 characters',
			'email.required' => 'The email is required',
			'email.email' => 'Invalid email address',
			'email.string' => 'Inavlid email address',
			'email.max' => 'Emails must not exceed 255 characters',
			'web-logo.max' => 'Image should be below 5MB',
			'web-logo.mimes' => 'Selected file doesn\'t match the allowed image formats',
		]);

		if ($validator->fails())
			return redirect()
				->back()
				->withInput()
				->withErrors($validator);

		try {
			DB::beginTransaction();

			foreach ($req->except('_token') as $k => $v) {
				if ($k == 'contacts') {
					$v = implode(', ', $v);
				}
				else if ($k == 'web-logo') {
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

		return redirect()
			->route('admin.settings.index')
			->with('flash_success', 'Successfully updated settings');
	}
}