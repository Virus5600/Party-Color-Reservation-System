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
		// $permissions = Permission::get();
		$permissions = array('PERM 1', 'PERM 2');

		return view('admin.permissions.index', [
			'permissions' => $permissions
		]);
	}
}