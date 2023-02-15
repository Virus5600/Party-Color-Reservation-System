<div class="d-flex flex-row dark-shadow position-absolute position-lg-relative h-100 w-100 w-lg-auto" style="overflow: hidden;">
	{{-- Navigation Bar (SIDE) --}}
	<div class="sidebar dark-shadow custom-scroll d-flex flex-column py-3 px-0 collapse-horizontal overflow-y-auto h-100 bg-white" id="sidebar" aria-labelledby="sidebar-toggler" aria-expanded="false">
		{{-- DASHBOARD --}}
		@if (\Request::is('admin/dashboard'))
		<span class="bg-secondary text-white"><i class="fas fa-tachometer-alt mr-2"></i>Dashboard</span>
		@elseif (\Request::is('admin/dashboard*'))
		<a class="text-decoration-none bg-secondary text-white aria-link" href="{{ route('admin.dashboard') }}" aria-hidden="false" aria-label="Dashboard"><i class="fas fa-tachometer-alt mr-2"></i>Dashboard</a>
		@else
		<a class="text-decoration-none text-dark aria-link" href="{{ route('admin.dashboard') }}" aria-hidden="false" aria-label="Dashboard"><i class="fas fa-tachometer-alt mr-2"></i>Dashboard</a>
		@endif

		{{-- RESERVATION --}}
		@if (\Request::is('admin/booking'))
		<span class="bg-secondary text-white"><i class="fas fa-calendar-alt mr-2"></i>Bookings</span>
		@elseif (\Request::is('admin/booking*'))
		<a class="text-decoration-none bg-secondary text-white aria-link" href="{{ route('admin.bookings.index') }}" aria-hidden="false" aria-label="Booking"><i class="fas fa-calendar-alt mr-2"></i>Booking</a>
		@else
		<a class="text-decoration-none text-dark aria-link" href="{{ route('admin.bookings.index') }}" aria-hidden="false" aria-label="Booking"><i class="fas fa-calendar-alt mr-2"></i>Booking</a>
		@endif

		{{-- INVENTORY --}}
		@if (\Request::is('admin/inventory'))
		<span class="bg-secondary text-white"><i class="fas fa-boxes mr-2"></i>Inventory</span>
		@elseif (\Request::is('admin/inventory*'))
		<a class="text-decoration-none bg-secondary text-white aria-link" href="{{ route('admin.inventory.index') }}" aria-hidden="false" aria-label="Inventory"><i class="fas fa-boxes mr-2"></i>Inventory</a>
		@else
		<a class="text-decoration-none text-dark aria-link" href="{{ route('admin.inventory.index') }}" aria-hidden="false" aria-label="Inventory"><i class="fas fa-boxes mr-2"></i>Inventory</a>
		@endif

		{{-- MENU --}}
		@if (\Request::is('admin/menu'))
		<span class="bg-secondary text-white"><i class="fas fa-utensils mr-2"></i>Menu</span>
		@elseif (\Request::is('admin/menu*'))
		<a class="text-decoration-none bg-secondary text-white aria-link" href="{{ route('admin.menu.index') }}" aria-hidden="false" aria-label="Inventory"><i class="fas fa-utensils mr-2"></i>Menu</a>
		@else
		<a class="text-decoration-none text-dark aria-link" href="{{ route('admin.menu.index') }}" aria-hidden="false" aria-label="Inventory"><i class="fas fa-utensils mr-2"></i>Menu</a>
		@endif

		{{-- ANNOUNCEMENT --}}
		@if (\Request::is('admin/announcement'))
		<span class="bg-secondary text-white"><i class="fas fa-bullhorn mr-2"></i>Announcements</span>
		@elseif (\Request::is('admin/announcement*'))
		<a class="text-decoration-none bg-secondary text-white aria-link" href="{{ route('admin.announcements.index') }}" aria-hidden="false" aria-label="Inventory"><i class="fas fa-bullhorn mr-2"></i>Announcements</a>
		@else
		<a class="text-decoration-none text-dark aria-link" href="{{ route('admin.announcements.index') }}" aria-hidden="false" aria-label="Inventory"><i class="fas fa-bullhorn mr-2"></i>Announcements</a>
		@endif

		{{-- ADMIN SETTING AREA --}}
		@php
		$userAccess = Auth::user()->hasPermission('users_tab_access');
		$permsAccess = Auth::user()->hasPermission('permissions_tab_access');
		$logsAccess = Auth::user()->hasPermission('activity_logs_tab_access');
		$settingsAccess = Auth::user()->hasPermission('settings_tab_access');
		@endphp

		@if ($userAccess || $permsAccess || $settingsAccess)
			<hr class="w-100 custom-hr">

			{{-- USERS --}}
			@if ($userAccess)
				@if (\Request::is('admin/users'))
				<span class="bg-secondary text-white"><i class="fas fa-user-alt mr-2"></i>Users</span>
				@elseif (\Request::is('admin/users*'))
				<a class="text-decoration-none bg-secondary text-white aria-link" href="{{ route('admin.users.index') }}" aria-hidden="false" aria-label="Users"><i class="fas fa-user-alt mr-2"></i>Users</a>
				@else
				<a class="text-decoration-none text-dark aria-link" href="{{ route('admin.users.index') }}" aria-hidden="false" aria-label="Users"><i class="fas fa-user-alt mr-2"></i>Users</a>
				@endif
			@endif

			{{-- PERMISSIONS --}}
			@if ($permsAccess)
				@if (\Request::is('admin/permissions'))
				<span class="bg-secondary text-white"><i class="fas fa-lock mr-2"></i>Permissions</span>
				@elseif (\Request::is('admin/permissions*'))
				<a class="text-decoration-none bg-secondary text-white aria-link" href="{{ route('admin.permissions.index') }}" aria-hidden="false" aria-label="Permissions"><i class="fas fa-lock mr-2"></i>Permissions</a>
				@else
				<a class="text-decoration-none text-dark aria-link" href="{{ route('admin.permissions.index') }}" aria-hidden="false" aria-label="Permissions"><i class="fas fa-lock mr-2"></i>Permissions</a>
				@endif
			@endif

			{{-- ACTIVITY LOG --}}
			@if ($logsAccess)
				@if (\Request::is('admin/activity-log'))
				<span class="bg-secondary text-white"><i class="fas fa-book mr-2"></i>Activity Log</span>
				@elseif (\Request::is('admin/activity-log*'))
				<a class="text-decoration-none bg-secondary text-white aria-link" href="{{ route('admin.activity-log.index') }}" aria-hidden="false" aria-label="Users"><i class="fas fa-book mr-2"></i>Activity Log</a>
				@else
				<a class="text-decoration-none text-dark aria-link" href="{{ route('admin.activity-log.index') }}" aria-hidden="false" aria-label="Users"><i class="fas fa-book mr-2"></i>Activity Log</a>
				@endif
			@endif

			{{-- SETTINGS --}}
			@if ($settingsAccess)
				@if (\Request::is('admin/settings'))
				<span class="bg-secondary text-white"><i class="fas fa-cog mr-2"></i>Settings</span>
				@elseif (\Request::is('admin/settings*'))
				<a class="text-decoration-none bg-secondary text-white aria-link" href="{{ route('admin.settings.index') }}" aria-hidden="false" aria-label="Settings"><i class="fas fa-cog mr-2"></i>Settings</a>
				@else
				<a class="text-decoration-none text-dark aria-link" href="{{ route('admin.settings.index') }}" aria-hidden="false" aria-label="Settings"><i class="fas fa-cog mr-2"></i>Settings</a>
				@endif
			@endif
		@endif

		{{-- SIGNOUT --}}
		<hr class="w-100 custom-hr">

		<a class="text-decoration-none text-dark aria-link" href="{{ route('logout') }}" aria-hidden="false" aria-label="Logout"><i class="fas fa-sign-out-alt mr-2"></i>Logout</a>
	</div>
</div>