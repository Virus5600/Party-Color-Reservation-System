<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Announcement;
use App\User;

use DB;
use Exception;
use File;
use Log;
use Validator;

class ApiController extends Controller
{
	protected function generalSearch(Request $req) {
		$content = '<h2>' . $req->search . '</h2>';

		return response()->json([
			'content' => $content
		]);
	}

	protected function adminSearch(Request $req, $id) {
		$toSearch = '%'.$req->search.'%';
		$user = User::find($id);

		if ($user == null)
			throw new Exception('Unauthorize Access');

		$hasImage = false;
		$image = null;
		$imgAlt = null;

		$hasHeader = false;
		$hasSoftDel = false;
		$header = null;

		$hasChangePassword = false;
		$changePassURI = null;

		$etc = null;

		// DEPARTMENTS
		if ($req->type == 'types') {
			$content = Department::query();

			if ($req->sd == 1)
				$content = $content->withTrashed();

			$content = $content->where('name', 'LIKE', $toSearch)
				->orWhere('abbreviation', 'LIKE', $toSearch)
				->select('id', 'name', 'abbreviation', 'seal', 'deleted_at')
				->get();
			$content_order = ['name'];
			$data_length = 3;

			$hasImage = true;
			$image = 'seal';
			$imgAlt = 'name';
			
			$hasHeader = true;
			$hasSoftDel = true;
			$header = 'abbreviation';

			$etc = array();

			// Edit
			if ($user->hasPermission('departments_tab_edit')) {
				array_push($etc, array(
					'uri' => url('/admin/departments/{id}/edit'),
					'icon' => 'fas fa-pencil-alt',
					'name' => 'Edit'
				));
			}

			// Manage Permissions
			if ($user->hasPermission('departments_tab_permissions')) {
				array_push($etc, array(
					'uri' => url('/admin/departments/{id}/manage-permissions'),
					'icon' => 'fas fa-user-lock',
					'name' => 'Manage Permissions'
				));
			}

			// Delete
			if ($user->hasPermission('departments_tab_delete')) {
				array_push($etc, array(
					'uri' => url('/admin/departments/{id}/delete'),
					'icon' => 'fas fa-trash',
					'name' => 'Delete'
				));

				array_push($etc, array(
					'uri' => url('/admin/departments/{id}/restore'),
					'icon' => 'fas fa-recycle',
					'name' => 'Restore'
				));
			}

			// Perma Delete
			if ($user->hasPermission('departments_tab_perma_delete')) {
				array_push($etc, array(
					'uri' => url('/admin/departments/{id}/perma-delete'),
					'icon' => 'fas fa-fire-alt',
					'name' => 'Delete Permanently'
				));
			}
		}
		// PERMISSIONS
		else if ($req->type == 'permissions') {
			$content = Permission::where('permissions.name', 'LIKE', $toSearch)
				->leftJoin('department_permissions', 'department_permissions.permission_id', '=', 'permissions.id')
				->leftJoin('departments', 'departments.id', '=', 'department_permissions.department_id')
				->leftJoin('users', 'users.department_id', '=', 'departments.id')
				->select('permissions.name', DB::raw('COUNT(DISTINCT users.id) as count'))
				->groupBy('permissions.name')
				->get();
			$content_order = ['count'];
			$data_length = 2;

			$hasHeader = true;
			$header = 'name';
		}
		// USERS
		else if ($req->type == 'users') {
			$content = User::leftJoin('types', 'users.type_id', '=', 'types.id')
				->where('first_name', 'LIKE', $toSearch)
				->orWhere('middle_name', 'LIKE', $toSearch)
				->orWhere('last_name', 'LIKE', $toSearch)
				->orWhere('email', 'LIKE', $toSearch)
				->orWhere('types.name', 'LIKE', $toSearch)
				->select('users.id', DB::raw("CONCAT(first_name, ' ', last_name) as user_name"), 'types.name', 'email', 'avatar')
				->get();

			$content_order = ['name', 'email'];
			$data_length = 5;

			$hasHeader = true;
			$hasSoftDel = true;
			$header = 'user_name';

			$hasImage = true;
			$image = 'avatar';
			$imgAlt = 'first_name';

			$hasChangePassword = true;
			$changePassURI = url('/admin/users/{id}/change-password');

			$etc = array();

			// Edit
			if ($user->hasPermission('users_tab_edit')) {
				array_push($etc, array(
					'uri' => url('/admin/users/{id}/edit'),
					'icon' => 'fas fa-pencil-alt',
					'name' => 'Edit'
				));
			}

			// Manage Permissions
			if ($user->hasPermission('users_tab_permissions')) {
				array_push($etc, array(
					'uri' => url('/admin/users/{id}/manage-permissions'),
					'icon' => 'fas fa-user-lock',
					'name' => 'Manage Permissions'
				));
			}

			// Change Password
			if ($user->hasPermission('users_tab_edit')) {
				array_push($etc, array(
					'uri' => url('/admin/users/{id}/change-password'),
					'icon' => 'fas fa-lock',
					'name' => 'Change Password'
				));
			}

			// Delete
			if ($user->hasPermission('users_tab_delete')) {
				array_push($etc, array(
					'uri' => url('/admin/users/{id}/delete'),
					'icon' => 'fas fa-trash',
					'name' => 'Delete'
				));

				array_push($etc, array(
					'uri' => url('/admin/users/{id}/restore'),
					'icon' => 'fas fa-recycle',
					'name' => 'Restore'
				));
			}

			// Perma Delete
			if ($user->hasPermission('users_tab_perma_delete')) {
				array_push($etc, array(
					'uri' => url('/admin/users/{id}/perma-delete'),
					'icon' => 'fas fa-fire-alt',
					'name' => 'Delete Permanently'
				));
			}
		}

		return response()->json([
			'type' => $req->type,
			'content' => $content,
			'content_order' => $content_order,
			'asset' => asset('uploads/'.$req->type.'/'),
			'data_length' => $data_length,
			'has_image' => $hasImage,
			'image' => $image,
			'img_alt' => $imgAlt,
			'has_header' => $hasHeader,
			'has_soft_del' => $hasSoftDel,
			'header' => $header,
			'etc' => $etc,
		]);
	}

	protected function removeImage(Request $req) {
		$validator = Validator::make($req->all(), [
			'type' => 'required|string',
			'id' => 'required|numeric'
		]);

		if ($validator->fails()) {
			return response()
				->json([
					'type' => 'validation_error',
					'errors' => $validator->errors()
				]);
		}

		$emptyResponse = true;

		if ($req->type == 'user') {
			try {
				DB::beginTransaction();

				$user = User::find($req->id);

				$oldAvatar = $user->avatar;
				$user->avatar = 'default.png';
				$user->save();

				File::delete(public_path() . '/uploads/users/' . $oldAvatar);

				$fallback = asset('uploads/users/default.png');
				$emptyResponse = false;

				DB::commit();
			} catch (Exception $e) {
				DB::rollback();
				Log::error($e);

				return response()
					->json([
						'type' => 'error',
						'error' => $e
					]);
			}
		}

		if (!$emptyResponse)
			return response()
				->json([
					'type' => 'success',
					'message' => 'Successfully removed image of ' . $req->type,
					'fallback' => $fallback
				]);

		return response()->json([
			'type' => 'empty',
			'message' => 'Unknown category: ' . $req->type
		]);
	}

	protected function fetchAnnouncements(Request $req) {
		$announcements = Announcement::get();

		return response()->json([
			'announcements' => $announcements
		]);
	}
}