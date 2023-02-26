@extends('layouts.admin')

@section('title', 'Types')

@section('content')
<div class="container-fluid">
	<div class="row">
		<div class="col-12 col-md mt-3">
			<div class="row">
				{{-- Header --}}
				<div class="col-6">
					<h1>
						<a href="{{ route('admin.types.index') }}" class="text-dark text-decoration-none font-weight-normal">
							<i class="fas fa-chevron-left mr-2"></i>(Role) Types
						</a>
					</h1>
				</div>
			</div>
		</div>
	</div>

	<hr class="hr-thick">

	<div class="row">
		<div class="col-12 col-md-10 mx-auto">
			<div class="card dark-shadow mb-5" id="inner-content">
				<h3 class="card-header font-weight-bold text-center">{{ $type->name }}</h3>
				
				<div class="card-body">
					<p>Permissions:</p>

					<ul class="list-group">
						@php ($listedPerms = [])
						@forelse ($type->permissions as $p)
							@if (!in_array($p->id, $listedPerms))
							<li class="list-group-item">
								{{-- PARENT PERMISSIONS --}}
								<p class="cursor-pointer my-0" data-toggle="collapse" data-target="#{{ $p->slug }}" aria-expanded="false" aria-controls="{{ $p->slug }}">
									{{ $p->name }}
									
									@if ($p->parent_permission == null)
									<span class="badge badge-primary">Parent Permission</span>
									@else
									<span class="badge badge-secondary">Child of {{ $p->parentPermission()->name }}</span>
									@endif
								</p>
								@php (array_push($listedPerms, $p->id))

								<div class="w-100 collapse" id="{{ $p->slug }}">
									{{-- CHILD PERMISSIONS --}}
									@foreach($p->childPermissions() as $cp)
										@if (count($cp->childPermissions()) > 0)
											@continue
										@endif
										<p class="w-100 ml-2 my-0"><i class="fas fa-caret-right mr-2"></i>{{ $cp->name }}</p>
										@php(array_push($listedPerms, $cp->id))
									@endforeach
								</div>
							</li>
							@endif
						@empty
						<li class="list-group-item text-muted"><i>No permissions attached...</i></li>
						@endforelse
					</ul>
				</div>
			</div>
		</div>
	</div>
</div>
@endsection

@section('scripts')
<script type="text/javascript" src="{{ asset('js/util/confirm-leave.js') }}"></script>
@endsection