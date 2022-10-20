<div class="d-flex flex-row dark-shadow position-absolute position-lg-relative h-100 w-100 w-lg-auto" style="overflow: hidden;">
	{{-- Navigation Bar (SIDE) --}}
	<div class="sidebar dark-shadow custom-scroll d-flex flex-column py-3 px-0 collapse-horizontal overflow-y-auto h-100 bg-white" id="sidebar" aria-labelledby="sidebar-toggler" aria-expanded="false">
		{{-- DASHBOARD --}}
		@if (\Request::is('admin/dashboard*'))
		<span class="bg-secondary text-white"><i class="fas fa-tachometer-alt mr-2"></i>ダッシュボード</span>
		@else
		<a class="text-decoration-none text-dark aria-link" href="{{ route('admin.dashboard') }}" aria-hidden="false" aria-label="Dashboard"><i class="fas fa-tachometer-alt mr-2"></i>ダッシュボード</a>
		@endif

		{{-- RESERVATION --}}
		@if (\Request::is('admin/reservation*'))
		<span class="bg-secondary text-white"><i class="fas fa-calendar-alt mr-2"></i>予約</span>
		@else
		<a class="text-decoration-none text-dark aria-link" href="{{ route('admin.dashboard') }}" aria-hidden="false" aria-label="Reservation"><i class="fas fa-calendar-alt mr-2"></i>予約</a>
		@endif

		{{-- INVENTORY --}}
		@if (\Request::is('admin/inventory*'))
		<span class="bg-secondary text-white"><i class="fas fa-boxes mr-2"></i>在庫</span>
		@else
		<a class="text-decoration-none text-dark aria-link" href="{{ route('admin.dashboard') }}" aria-hidden="false" aria-label="Inventory"><i class="fas fa-boxes mr-2"></i>在庫</a>
		@endif

		{{-- ANNOUNCEMENT --}}
		@if (\Request::is('admin/announcement*'))
		<span class="bg-secondary text-white"><i class="fas fa-bullhorn mr-2"></i>発表</span>
		@else
		<a class="text-decoration-none text-dark aria-link" href="{{ route('admin.dashboard') }}" aria-hidden="false" aria-label="Inventory"><i class="fas fa-bullhorn mr-2"></i>発表</a>
		@endif

		{{-- ADMIN SETTING AREA --}}
		<hr class="w-100 custom-hr">

		{{-- USERS --}}
		@if (Auth::user()->hasPermission('users_tab_access'))
			@if (\Request::is('admin/users*'))
			<span class="bg-secondary text-white"><i class="fas fa-user-alt mr-2"></i>ユーザーズ</span>
			@else
			<a class="text-decoration-none text-dark aria-link" href="{{ route('admin.users.index') }}" aria-hidden="false" aria-label="Users"><i class="fas fa-user-alt mr-2"></i>ユーザーズ</a>
			@endif
		@endif

		{{-- PERMISSIONS --}}
		@if (\Request::is('admin/permissions*'))
		<span class="bg-secondary text-white"><i class="fas fa-lock mr-2"></i>権限</span>
		@else
		<a class="text-decoration-none text-dark aria-link" href="{{ route('admin.permissions.index') }}" aria-hidden="false" aria-label="Permissions"><i class="fas fa-lock mr-2"></i>権限</a>
		@endif

		{{-- SETTINGS --}}
		@if (Auth::user()->hasPermission('settings_tab_access'))
			@if (\Request::is('admin/settings*'))
			<span class="bg-secondary text-white"><i class="fas fa-cog mr-2"></i>設定</span>
			@else
			<a class="text-decoration-none text-dark aria-link" href="{{ route('admin.settings.index') }}" aria-hidden="false" aria-label="Settings"><i class="fas fa-cog mr-2"></i>設定</a>
			@endif
		@endif

		{{-- SIGNOUT --}}
		<hr class="w-100 custom-hr">

		<a class="text-decoration-none text-dark aria-link" href="{{ route('logout') }}" aria-hidden="false" aria-label="Logout"><i class="fas fa-sign-out-alt mr-2"></i>サインアウト</a>
	</div>
</div>