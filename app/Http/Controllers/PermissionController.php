<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Permission;

use DB;
use Exception;
use Log;
use Validator;

class PermissionController extends Controller
{
	protected function index(Request $req) {
		$permissions = Permission::paginate(10);

		return view('admin.permissions.index', [
			'permissions' => $permissions
		]);
	}

	protected function create() {
		// Set to 404 to prevent adding of permissions
		return abort(404);

		$permissions = Permission::where('parent_permission', '=', NULL)->get();

		return view('admin.permissions.create', [
			'permissions' => $permissions
		]);
	}

	protected function store(Request $req) {
		// Set to 404 to prevent adding of permissions
		return abort(404);

		$validator = Validator::make($req->all(), [
			'name' => 'required|unique:permissions,name|max:50',
			'parent_permission' => 'exists:slug|max:50'
		], [
			'name.required' => 'Permission name is required',
			'name.unique' => 'Permission already exists',
			'name.max' => 'Permission name must not exceed 50 characters',
			'parent_permission.exists' => 'Unknown permission...',
			'parent_permission.max' => 'Please refrain from modifying the form'
		]);

		if ($validator->fails()) {
			return redirect()
				->back()
				->withErrors($validator)
				->withInput();
		}

		try {
			DB::beginTransaction();

			Permission::create([
				'name' => $req->name,
				'slug' => str_replace(' ', '_', strtolower($req->name)),
				'parent_permission' => $req->parent_permission
			]);

			DB::commit();
		} catch (Exception $e) {
			DB::rollback();
			Log::error($e);

			return redirect()
				->route('admin.permissions.index')
				->with('flash_error', 'Something went wrong, please try again later');
		}

		return redirect()
			->route('admin.permissions.index')
			->with('flash_success', 'Successfully added "' . trim($req->name) . '"');
	}

	protected function show($slug) {
		$permission = Permission::where('slug', '=', $slug)->first();

		if ($permission == null) {
			return redirect()
				->route('admin.permissions.index')
				->with('flash_error', 'The permission does not exists.');
		}

		return view('admin.permissions.show', [
			'permission' => $permission
		]);
	}

	protected function edit($id) {
		// Set to 404 to prevent editing of permissions
		return abort(404);

		$permission = Permission::find($id);

		if ($permission == null) {
			return redirect()
				->route('admin.permissions.index')
				->with('flash_error', 'The permission either does not exists or is already deleted.');
		}

		return view('admin.permissions.edit', [
			'permission' => $permission
		]);
	}

	protected function update(Request $req, $id) {
		// Set to 404 to prevent editing of permissions
		return abort(404);
		
		$permission = Permission::find($id);

		if ($permission == null) {
			return redirect()
				->route('admin.permissions.index')
				->with('flash_error', 'The permission either does not exists or is already deleted.');
		}

		$validator = Validator::make($req->all(), [
			'name' => 'required|unique:permissions,name,'.$permission->id.'|max:50'
		], [
			'name.required' => 'Permission name is required',
			'name.unique' => 'Permission already exists',
			'name.max' => 'Permission name must not exceed 50 characters'
		]);

		if ($validator->fails()) {
			return redirect()
				->back()
				->withErrors($validator)
				->withInput();
		}

		try {
			DB::beginTransaction();

			$permission->name = $req->name;
			$permission->slug = str_replace(' ', '_', strtolower($req->name));
			$permission->save();

			DB::commit();
		} catch (Exception $e) {
			DB::rollback();
			Log::error($e);

			return redirect()
				->route('admin.permissions.index')
				->with('flash_error', 'Something went wrong, please try again later');
		}

		return redirect()
			->route('admin.permissions.index')
			->with('flash_success', 'Successfully updated "' . trim($req->name) . '"');
	}
}