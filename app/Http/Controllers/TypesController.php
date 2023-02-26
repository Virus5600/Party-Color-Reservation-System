<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Permission;
use App\Type;

use DB;
use Exception;
use Log;
use Validator;

class TypesController extends Controller
{
	protected function index(Request $req) {
		$types = Type::withTrashed()
			->withCount(['users', 'permissions'])
			->paginate(10);

		$totalPerms = Permission::count();

		return view('admin.types.index', [
			'types' => $types,
			'totalPerms' => $totalPerms
		]);
	}

	protected function create() {
		$permissions = Permission::get();

		return view('admin.types.create', [
			'permissions' => $permissions
		]);
	}

	protected function store(Request $req) {
		$validator = Validator::make($req->all(), [
			'name' => 'required|string|max:255|unique:types,name',
			'permissions' => 'sometimes|array',
			'permissions.*' => 'sometimes|numeric|exists:permissions,id'
		], [
			'name.required' => "Type name is required",
			'name.string' => "Type name should be a string",
			'name.max' => "Type name is capped at 255 characters",
			'name.unique' => "\"{$req->name}\" already exists",
			'permissions.array' => "Malformed permissions data",
			'permissions.*.numeric' => "Please refrain from modifying the form",
			'permissions.*.exists' => "Permission either does not exists or is already deleted",
		]);

		if ($validator->fails()) {
			return redirect()
				->route('admin.types.create')
				->withErrors($validator)
				->withInput();
		}

		try {
			DB::beginTransaction();

			$type = Type::create([
				'name' => trim($req->name)
			]);

			$type->permissions()->sync($req->permissions);

			// LOGGER
			activity('types')
				->by(auth()->user())
				->on($type)
				->event('create')
				->withProperties([
					'name' => $type->name,
					'permissions' => $type->permissions->toArray()
				])
				->log("Created \"{$type->name}\" type.");

			DB::commit();
		} catch (Exception $e) {
			DB::rollback();
			Log::error($e);

			return redirect()
				->route('admin.types.index')
				->with('flash_error', 'Something went wrong, please try again later');
		}

		return redirect()
			->route('admin.types.index')
			->with('flash_success', 'Successfully added new user type');
	}

	protected function show($id) {
		$type = Type::withTrashed()
			->with(['permissions'])
			->find($id);

		if ($type == null) {
			return redirect()
				->route('admin.types.index')
				->with('flash_error', "(Role) Type either does not exists or is already deleted");
		}

		return view('admin.types.show', [
			'type' => $type
		]);
	}

	protected function update(Request $req, $id) {
		$type = Type::withTrashed()->find($id);

		if ($type == null) {
			return response()
				->json([
					'success' => false,
					'message' => "Type either does not exists or is already deleted"
				]);
		}

		$validator = Validator::make($req->all(), [
			'name' => 'required|string|max:255|unique:types,name',
		], [
			'name.required' => "Type name is required",
			'name.string' => "Type name should be a string",
			'name.max' => "Type name is capped at 255 characters",
			'name.unique' => "\"{$req->name}\" already exists",
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

			$oldname = $type->name;
			$type->name = trim($req->name);
			$type->save();

			// LOGGER
			activity('types')
				->by(auth()->user())
				->on($type)
				->event('create')
				->withProperties([
					'name' => $type->name,
					'permissions' => $type->permissions->toArray()
				])
				->log("Updated \"{$type->name}\" type.");

			DB::commit();
		} catch (Exception $e) {
			DB::rollback();
			Log::error($e);

			return redirect()
				->json([
					'success' => false,
					'message' => "Something went wrong, please try again later"
				]);
		}

		return response()
			->json([
				'success' => true,
				'message' => "Successfully updated type's name"
			]);
	}

	protected function managePermissions(Request $req, $id) {
		$type = Type::withTrashed()
			->with(['permissions'])
			->find($id);

		if ($type == null) {
			return response()
				->json([
					'success' => false,
					'message' => "Type either does not exists or is already deleted"
				]);
		}

		$permissions = Permission::get();
		$permIds = $type->permissions->pluck('id')->toArray();

		return view('admin.types.manage-permissions', [
			'type' => $type,
			'permissions' => $permissions,
			'permIds' => $permIds
		]);
	}
	
	protected function updatePermissions(Request $req, $id) {
		$type = Type::withTrashed()->find($id);

		if ($type == null) {
			return response()
				->json([
					'success' => false,
					'message' => "Type either does not exists or is already deleted"
				]);
		}

		$validator = Validator::make($req->all(), [
			'permissions' => 'sometimes|array',
			'permissions.*' => 'sometimes|numeric|exists:permissions,id'
		], [
			'permissions.array' => "Malformed permissions data",
			'permissions.*.numeric' => "Please refrain from modifying the form",
			'permissions.*.exists' => "Permission either does not exists or is already deleted",
		]);

		if ($validator->fails()) {
			return redirect()
				->route('admin.types.manage-permissions', [$id])
				->withErrors($validator)
				->withInput();
		}

		try {
			DB::beginTransaction();

			$type->permissions()->sync($req->permissions);
			// LOGGER
			activity('types')
				->by(auth()->user())
				->on($type)
				->event('update')
				->withProperties([
					'name' => $type->name,
					'permissions' => $type->permissions->toArray()
				])
				->log("Updated permissions of \"{$type->name}\" type.");

			DB::commit();
		} catch (Exception $e) {
			DB::rollback();
			Log::error($e);

			return redirect()
				->route('admin.types.index')
				->with('flash_error', 'Something went wrong, please try again later');
		}

		return redirect()
			->route('admin.types.index')
			->with('flash_success', "Successfully updated permissions of \"{$type->name}\" type.");
	}

	protected function delete(Request $req, $id) {
		$type = Type::find($id);

		if ($type == null) {
			return redirect()
				->route('admin.types.index')
				->with('flash_error', 'Type either does not exists or is already deleted.');
		}

		try {
			DB::beginTransaction();

			$type->delete();

			// LOGGER
			activity('types')
				->by(auth()->user())
				->on($type)
				->event('deactivate')
				->withProperties([
					'name' => $type->name,
					'permissions' => $type->permissions->toArray()
				])
				->log("Deactivated \"{$type->name}\" type.");

			DB::commit();
		} catch (Exception $e) {
			DB::rollback();
			Log::error($e);

			return redirect()
				->route('admin.types.index')
				->with('flash_error', 'Something went wrong, please try again later');
		}

		return redirect()
			->route('admin.types.index')
			->with('flash_success', 'Successfully deactivated type.');
	}

	protected function restore(Request $req, $id) {
		$type = Type::withTrashed()->find($id);

		if ($type == null) {
			return redirect()
				->route('admin.types.index')
				->with('flash_error', 'Type either does not exists or is already deleted permanently.');
		}
		else if (!$type->trashed()) {
			return redirect()
				->back()
				->with('flash_error', 'Type is already activated.');
		}

		try {
			DB::beginTransaction();

			$type->restore();

			// LOGGER
			activity('types')
				->by(auth()->user())
				->on($type)
				->event('activate')
				->withProperties([
					'name' => $type->name,
					'permissions' => $type->permissions->toArray()
				])
				->log("Activated \"{$type->name}\" type.");

			DB::commit();
		} catch (Exception $e) {
			DB::rollback();
			Log::error($e);

			return redirect()
				->route('admin.type.index')
				->with('flash_error', 'Something went wrong, please try again later');
		}

		return redirect()
			->back()
			->with('flash_success', 'Successfully re-activated type.');
	}

	protected function permaDelete(Request $req, $id) {
		$type = Type::withTrashed()->find($id);

		if ($type == null) {
			return redirect()
				->route('admin.types.index')
				->with('flash_error', 'Type either does not exists or is already deleted.');
		}

		try {
			DB::beginTransaction();

			$name = $type->name;
			$permissions = $type->permissions->toArray();

			$type->forceDelete();

			// LOGGER
			activity('types')
				->by(auth()->user())
				->on($type)
				->event('delete')
				->withProperties([
					'name' => $name,
					'permissions' => $permissions
				])
				->log("Permanently deleted \"{$name}\" type.");

			DB::commit();
		} catch (Exception $e) {
			DB::rollback();
			Log::error($e);

			return redirect()
				->route('admin.types.index')
				->with('flash_error', 'Something went wrong, please try again later');
		}

		return redirect()
			->route('admin.types.index')
			->with('flash_success', 'Successfully removed the type permanently');
	}
}