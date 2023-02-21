@extends('layouts.admin')

@section('title', "Menu Variation - {$variation->name} ({$menu->name})")

@section('content')
<div class="container-fluid">
	<div class="row">
		<div class="col-12 col-md mt-3">
			<div class="row">
				{{-- Header --}}
				<div class="col-12">
					<h1>
						<a href="{{ route('admin.menu.variation.index', [$menu->id]) }}" class="text-dark text-decoration-none font-weight-normal">
							<i class="fas fa-chevron-left mr-2"></i>{{ $menu->name }}
						</a>
					</h1>
				</div>
			</div>
		</div>
	</div>

	<hr class="hr-thick">

	<div class="row">
		<div class="mx-auto col-10 col-sm-8 col-md-6 col-lg-4">
			<div class="card dark-shadow mb-5" id="inner-content">
				{{-- MENU NAME --}}
				<h3 class="card-header text-center">
					<span class="float-left">{{ $variation->name }}</span>
					<span class="float-right">{{ $variation->getPrice() }}</span>
				</h3>
				
				<div class="card-body">
					<p class="h5">Includes: </p>

					<ul class="list-group">
						@forelse($variation->items as $i)
						<li class="list-group-item">
							<span class="float-left">{{ $i->item_name }}</span>
							<span class="float-right">
							@if ($i->pivot->is_unlimited == 1)
								Unlimited
							@else
								{{ $i->pivot->amount . $i->measurement_unit }}
							@endif
							</span>
						</li>
						@empty
						<li class="list-group-item text-center">
							<p>No ingredients yet!</p>
							<a href="{{ route('admin.menu.variation.edit', [$menu->id, $variation->id]) }}" class="btn btn-primary btn-sm">Add Ingredients</a>
						</li>
						@endforelse
					</ul>
				</div>
				
				<div class="card-footer text-center">
					<a href="{{ route('admin.menu.variation.edit', [$menu->id, $variation->id]) }}" class="btn btn-primary ml-3">Edit</a>

					<a href="{{ route('admin.menu.index') }}" class="btn btn-secondary ml-3">Go Back</a>
				</div>
			</div>
		</div>
	</div>
</div>
@endsection