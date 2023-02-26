@extends('layouts.admin')

@section('title', 'Users')

@section('content')
<div class="container-fluid">
	<div class="row">
		<div class="col-12 col-md mt-3">
			<div class="row">
				{{-- Header --}}
				<div class="col-12">
					<h1>
						<a href="{{ route('admin.users.index') }}" class="text-dark text-decoration-none font-weight-normal">
							<i class="fas fa-chevron-left mr-2"></i>Users
						</a>
					</h1>
				</div>
			</div>
		</div>
	</div>

	<hr class="hr-thick">

	<div class="row">
		<div class="col-12 col-md-10 col-lg-8 mx-auto">
			<div class="card floating-title mt-4 border-secondary">
				<h3 class="card-title text-left m-0 p-2">{{ $user->getName()  }}</h3>
				
				<div class="card-body">
					<img src="{{ asset($user->getAvatar()) }}" alt="{{ $user->getName() }}'s avatar" class="img img-thumbnail w-50 w-md-25 mx-auto d-block mb-3">

					<ul class="list-group">
						@foreach($format as $f)
							@if (in_array($f, ['avatar', 'last_auth']))
								@continue
							@else
							<li class="list-group-item d-flex flex-row justify-content-between">
								<span class="font-weight-bold">
									@if ($f == 'type_id')
									User Type
									@else
									{{ str_replace("_", " ", ucfirst($f)) }}
									@endif
								</span>
								
								<span>
									@if ($f == 'type_id')
									{{ $user->type->name }}
									@elseif ($f == 'locked')
									{{ $user->$f == 1 ? "True" : "False" }}
									@else
									{{ $user->$f }}
									@endif
								</span>
							</li>
							@endif
						@endforeach
					</ul>
				</div>

				<div class="card-footer text-right">
					<a href="{{ route('admin.users.edit', [$user->id]) }}" class="btn btn-primary">Edit</a>
					<a href="{{ route('admin.users.index') }}" class="btn btn-secondary">Go Back</a>
				</div>
			</div>
		</div>
	</div>
</div>
@endsection