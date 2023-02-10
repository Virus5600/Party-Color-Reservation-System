<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Carbon\Carbon;

use App\ActivityLog;
use App\Announcement;
use App\Inventory;
use App\Menu;
use App\Reservation;

use App\Enum\ApprovalStatus;

use Log;

class PageController extends Controller
{
	// FALLBACK
	protected function fallback() {
		return redirect()
			->route('home');
	}

	// USER PAGES
	protected function index() {
		return view('index');
	}

	protected function redirectToDashboard() {
		return redirect()
			->route('admin.dashboard');
	}

	// ADMIN PAGES
	protected function dashboard() {
		$totals = [
			'calendar-alt' => Reservation::class,
			'boxes' => Inventory::class,
		];

		$tables = [
			'inactive_menu' => [
				'clazz' => Menu::class,
				'name' => 'Inactive Menus',
				'conditions' => ['trashed'],
				'columns' => ['name'],
				'hasActions' => false
			],
			'latest_activities' => [
				'clazz' => ActivityLog::class,
				'name' => 'Latest Activities',
				'conditions' => ['*'],
				'columns' => ['address', 'action'],
				'hasActions' => false
			],
			'critical_inventories' => [
				'clazz' => Inventory::class,
				'name' => 'Critical Stocks',
				'conditions' => ['withTrashed', 'quantity <= 10'],
				'columns' => ['item_name', 'quantity'],
				'hasActions' => false,
				'paginate' => 5
			],
			'pending_reservations' => [
				'clazz' => Reservation::class,
				'name' => 'Pending Reservations',
				'conditions' => ['status = ' . ApprovalStatus::Pending],
				'hiddenColumns' => ['price'],
				'columns' => ['pax'],
				'columnsFn' => ['reservation_for', 'price'],
				'aliasFn' => ['reservation_for' => 'reservationFor', 'price' => 'fetchPrice'],
				'fnFirst' => true,
				'hasActions' => false,
				'paginate' => 5
			],
			'draft_announcements' => [
				'clazz' => Announcement::class,
				'name' => 'Drafted Announcements',
				'conditions' => ['is_draft = 1'],
				'columns' => ['title', 'summary'],
				'columnsFn' => ['author'],
				'hasActions' => false,
				'paginate' => 5
			],
			'latest_announcements' => [
				'clazz' => Announcement::class,
				'name' => 'Latest Announcements',
				'conditions' => ['is_draft = 0', 'latest'],
				'hiddenColumns' => ['user_id'],
				'columns' => ['title', 'summary'],
				'columnsFn' => ['author'],
				'hasActions' => false,
				'paginate' => 5
			]
		];

		$months = [];
		$monthly_earnings = [];

		for ($i = 1; $i <= now()->format('m'); $i++) {
			array_push($months, Carbon::parse(now()->format('Y') . '-' . $i . '-' . now()->format('d'))->format('M'));

			array_push($monthly_earnings,
				Reservation::where('created_at', '>=', Carbon::parse(now()->format('Y') . '-' . $i . '-01'))
					->where('created_at', '<=', Carbon::parse(Carbon::now()->format('Y') . '-' . $i)->endOfMonth())
					->where('status', '=', ApprovalStatus::Approved)
					->get()
					->sum('price')
			);
		}

		return view('admin.dashboard', [
			'totals' => $totals,
			'tables' => $tables,
			'months' => $months,
			'monthly_earnings' => $monthly_earnings
		]);
	}
}