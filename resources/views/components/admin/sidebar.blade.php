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
		@if (\Request::is('admin/reservation'))
		<span class="bg-secondary text-white"><i class="fas fa-calendar-alt mr-2"></i>Reservations</span>
		@elseif (\Request::is('admin/reservation*'))
		<a class="text-decoration-none bg-secondary text-white aria-link" href="{{ route('admin.dashboard') }}" aria-hidden="false" aria-label="Reservation"><i class="fas fa-calendar-alt mr-2"></i>Reservation</a>
		@else
		<a class="text-decoration-none text-dark aria-link" href="{{ route('admin.dashboard') }}" aria-hidden="false" aria-label="Reservation"><i class="fas fa-calendar-alt mr-2"></i>Reservation</a>
		@endif

		{{-- INVENTORY --}}
		@if (\Request::is('admin/inventory'))
		<span class="bg-secondary text-white"><i class="fas fa-boxes mr-2"></i>Inventory</span>
		@elseif (\Request::is('admin/inventory*'))
		<a class="text-decoration-none bg-secondary text-white aria-link" href="{{ route('admin.inventory.index') }}" aria-hidden="false" aria-label="Inventory"><i class="fas fa-boxes mr-2"></i>Inventory</a>
		@else
		<a class="text-decoration-none text-dark aria-link" href="{{ route('admin.inventory.index') }}" aria-hidden="false" aria-label="Inventory"><i class="fas fa-boxes mr-2"></i>Inventory</a>
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
		@if (Auth::user()->hasSomePermission('users_tab_access', 'permissions_tab_access', 'settings_tab_access'))
			<hr class="w-100 custom-hr">

			{{-- USERS --}}
			@if (Auth::user()->hasPermission('users_tab_access'))
				@if (\Request::is('admin/users'))
				<span class="bg-secondary text-white"><i class="fas fa-user-alt mr-2"></i>Users</span>
				@elseif (\Request::is('admin/users*'))
				<a class="text-decoration-none bg-secondary text-white aria-link" href="{{ route('admin.users.index') }}" aria-hidden="false" aria-label="Users"><i class="fas fa-user-alt mr-2"></i>Users</a>
				@else
				<a class="text-decoration-none text-dark aria-link" href="{{ route('admin.users.index') }}" aria-hidden="false" aria-label="Users"><i class="fas fa-user-alt mr-2"></i>Users</a>
				@endif
			@endif

			{{-- PERMISSIONS --}}
			@if (Auth::user()->hasPermission('permissions_tab_access'))
				@if (\Request::is('admin/permissions'))
				<span class="bg-secondary text-white"><i class="fas fa-lock mr-2"></i>Permissions</span>
				@elseif (\Request::is('admin/permissions*'))
				<a class="text-decoration-none bg-secondary text-white aria-link" href="{{ route('admin.permissions.index') }}" aria-hidden="false" aria-label="Permissions"><i class="fas fa-lock mr-2"></i>Permissions</a>
				@else
				<a class="text-decoration-none text-dark aria-link" href="{{ route('admin.permissions.index') }}" aria-hidden="false" aria-label="Permissions"><i class="fas fa-lock mr-2"></i>Permissions</a>
				@endif
			@endif

			{{-- SETTINGS --}}
			@if (Auth::user()->hasPermission('settings_tab_access'))
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