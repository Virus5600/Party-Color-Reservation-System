@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
<div class="row my-3">
	{{-- TOTALS --}}
	<div class="col-12 col-lg-6 d-flex">
		<div class="row my-auto mx-0 w-100">
			@foreach ($totals as $k => $v)
			<div class="col-12 col-sm-6 my-3">
				@livewire('dashboard.summary-card', ['clazz' => $v, 'icon' => $k])
			</div>
			@endforeach
		</div>
	</div>

	{{-- LIST OF INACTIVE MENUS --}}
	<div class="col-12 col-lg-6">
		<div class="row">
			<div class="col-12">
				@livewire('dashboard.tables', $tables['inactive_menu'])
			</div>
		</div>
	</div>
</div>

<div class="row my-3">
	<div class="col-12 col-lg-8">
		<div class="row h-100">
			{{-- WEEKLY INCOME --}}
			<div class="col-12 my-3">
				<div class="card h-100">
					<div class="card-body position-relative">
						<canvas id="monthlyEarnings" class="rounded m-auto"></canvas>
					</div>
				</div>
			</div>

			{{-- CRITICAL INVENTORIES --}}
			<div class="col-12 col-lg-6">
				@livewire('dashboard.tables', $tables['critical_inventories'])
			</div>

			<div class="col-12 col-lg-6">
				@livewire('dashboard.tables', $tables['pending_bookings'])
			</div>
		</div>
	</div>

	{{-- LATEST ACTIVITIES --}}
	<div class="col-12 col-lg-4">
		@livewire('dashboard.tables', $tables['latest_activities'])
	</div>
</div>

<div class="row my-3">
	<div class="col-12 col-lg-6">
		@livewire('dashboard.tables', $tables['draft_announcements'])
	</div>
	
	<div class="col-12 col-lg-6">
		@livewire('dashboard.tables', $tables['latest_announcements'])
	</div>
</div>
@endsection

@section('css')
<link rel="stylesheet" type="text/css" href="{{ asset('css/util/custom-scrollbar.css') }}">
@endsection

@section('scripts')
<script type="text/javascript" src="{{ asset('js/util/confirm-leave.js') }}"></script>
{{-- Graph Pre-Initialization --}}
<script type="text/javascript" data-for-removal>
	var target = $(`#monthlyEarnings`);
	const dataset = [{
		data: [
			@for ($i = 0; $i < count($months); $i++)
			{
				x: `{{ $months[$i] }}`,
				y: `{{ $monthly_earnings[$i] }}`,
				date: `{{ Carbon\Carbon::parse("$months[$i] 1, " . now()->format('Y'))->format('F') }} {{ now()->format('Y') }}`
			},
			@endfor
		],
		borderColor: '#707070',
		backgroundColor: '#707070',
	}];
	const bookingFetchURL = `{{ route('api.admin.bookings.fetch', ["$1"]) }}`;
</script>
{{-- Graph Initialization --}}
<script type="text/javascript" src="{{ asset('js/views/admin/dashboard.js') }}"></script>
<script type="text/javascript" data-for-removal> $(document).ready(() => { $('[data-for-removal]').remove(); }); </script>
@endsection