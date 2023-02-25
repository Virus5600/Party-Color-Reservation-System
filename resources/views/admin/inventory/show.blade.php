@extends('layouts.admin')

@section('title', 'Inventory')

@section('content')
<div class="container-fluid">
	<div class="row">
		<div class="col-12 col-md mt-3">
			<div class="row">
				{{-- Header --}}
				<div class="col-12">
					<h1>
						<a href="{{ route('admin.inventory.index') }}" class="text-dark text-decoration-none font-weight-normal">
							<i class="fas fa-chevron-left mr-2"></i>Inventory
						</a>
					</h1>
				</div>
			</div>
		</div>
	</div>

	<hr class="hr-thick">

	<div class="row">
		<div class="mx-auto" style="max-width: 25rem !important; flex: 0 0 25rem;">
			<div class="card floating-title dark-shadow" id="inner-content">
				<h3 class="card-title">{{ $item->item_name }}</h3>

				<div class="card-body">
					<ul class="list-group">
						@foreach($format as $f)
						@if ($f == 'measurement_unit')
								@continue
						@else
							<li class="list-group-item d-flex flex-row justify-content-between">
								<span class="font-weight-bold">
									{{ str_replace("_", " ", ucfirst($f)) }}
								</span>
								
								<span>
									@if (in_array($f, ['quantity', 'critical_level']))
									{{ "{$item->$f} {$item->measurement_unit}" }}
									@elseif (Str::endsWith($f, '_at'))
									{{ Carbon\Carbon::parse($item->$f)->format("M d, Y h:i:s A") }}
									@else
									{{ $item->$f }}
									@endif
								</span>
							</li>
						@endif
						@endforeach

						<li class="list-group-item d-flex flex-row justify-content-between">
							<span class="font-weight-bold">
								Status
							</span>

							<span>
								@if ($item->trashed())
								<i class="fas fa-circle text-danger mr-2"></i> Inactive
								@else
									@if ($item->quantity <= $item->critical_level)
										<i class="fas fa-circle text-warning mr-2"></i> Critical
									@else
										<i class="fas fa-circle text-success mr-2"></i> Active
									@endif
								@endif
							</span>
						</li>
					</ul>
				</div>

				<div class="card-footer text-right">
					<a href="{{ route('admin.inventory.edit', [$item->id]) }}" class="btn btn-primary">Edit</a>
					<a href="{{ route('admin.inventory.index') }}" class="btn btn-secondary">Go Back</a>
				</div>
			</div>
		</div>
	</div>
</div>
@endsection