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
						<a href="javascript:void(0);" onclick="confirmLeave('{{route('admin.users.index')}}');" class="text-dark text-decoration-none font-weight-normal">
							<i class="fas fa-chevron-left mr-2"></i>Users
						</a>
					</h1>
				</div>
			</div>
		</div>
	</div>

	<hr class="hr-thick">

	<div class="row">
		<div class="col-12 col-md-8 mx-auto">
			<div class="card dark-shadow mb-5" id="inner-content">
				<div class="card-body">
					<form method="POST" action="{{ route('admin.users.store') }}" enctype="multipart/form-data" class="form">
						{{ csrf_field() }}

						<div class="row">
							{{-- AVATAR --}}
							<div class="col-12 col-lg-6 mx-auto">
								<div class="image-input-scope" id="avatar-scope" data-settings="#image-input-settings" data-fallback-img="{{ asset('uploads/users/default.png') }}">
									{{-- FILE IMAGE --}}
									<div class="form-group text-center image-input collapse show avatar_holder" id="avatar-image-input-wrapper">
										<label class="form-label font-weight-bold" for="avatar">User Image</label><br>
										<div class="hover-cam mx-auto avatar circular-border overflow-hidden">
											<img src="{{ asset('uploads/users/default.png') }}" class="hover-zoom img-fluid avatar" id="avatar-file-container" alt="User Avatar" data-default-src="{{ asset('uploads/users/default.png') }}">
											<span class="icon text-center image-input-float" id="avatar" tabindex="0">
												<i class="fas fa-camera text-white hover-icon-2x"></i>
											</span>
										</div>
										<input type="file" name="avatar" class="d-none" accept=".jpg,.jpeg,.png,.webp" data-target-image-container="#avatar-file-container" data-target-name-container="#avatar-name" >
										<h6 id="avatar-name" class="text-truncate w-50 mx-auto" data-default-name="{{ asset('uploads/users/default.png') }}">default.png</h6>
										<small class="text-muted pb-0 mb-0">
											<b>ALLOWED FORMATS:</b>
											<br>JPEG, JPG, PNG, WEBP
										</small><br>
										<small class="text-muted pt-0 mt-0"><b>MAX SIZE:</b> 5MB</small>
									</div>
								</div>

								{{-- LOGO ERROR --}}
								<div class="text-center">
									<span class="text-danger small">{{$errors->first('avatar')}}</span>
								</div>
							</div>

							<div class="col-12 col-lg-6">
								<div class="row">
									{{-- FIRST NAME --}}
									<div class="form-group col-6">
										<label class="form-label" for="first_name">First Name</label>
										<input type="text" id="first_name" name="first_name" class="form-control" placeholder="First Name" value="{{ old('first_name') }}" />
										<span class="text-danger small">{{$errors->first('first_name')}}</span>
									</div>

									{{-- MIDDLE NAME --}}
									<div class="form-group col-6">
										<label class="form-label" for="middle_name">Middle Name</label>
										<input type="text" id="middle_name" name="middle_name" class="form-control" placeholder="Middle Name" value="{{ old('middle_name') }}" />
										<span class="text-danger small">{{$errors->first('middle_name')}}</span>
									</div>

									{{-- LAST NAME --}}
									<div class="form-group col-6">
										<label class="form-label" for="last_name">Last Name</label>
										<input type="text" id="last_name" name="last_name" class="form-control" placeholder="Last Name" value="{{ old('last_name') }}" />
										<span class="text-danger small">{{$errors->first('last_name')}}</span>
									</div>

									{{-- SUFFIX --}}
									<div class="form-group col-6">
										<label class="form-label" for="suffix">Suffix</label>
										<input type="text" id="suffix" name="suffix" class="form-control" placeholder="Suffix" value="{{ old('suffix') }}" />
										<span class="text-danger small">{{$errors->first('suffix')}}</span>
									</div>

									{{-- EMAIL --}}
									<div class="form-group col-12">
										<label class="form-label" for="email">Email</label>
										<input type="email" id="email" name="email" class="form-control" placeholder="Email" value="{{ old('email') }}" />
										<span class="text-danger small">{{$errors->first('email')}}</span>
									</div>

									{{-- PASSWORD --}}
									<div class="form-group col-12">
										<label class="form-label" for="password">Password</label>
										<div class="input-group">
											<input class="form-control border-secondary border-right-0" type="password" name="password" id="password" aria-label="Password" aria-describedby="toggle-show-password" placeholder="Password" readonly value="{{ old('password') ? old('password') : $password }}" />
											<div class="input-group-append">
												<button type="button" class="btn bg-white border-secondary border-left-0" id="toggle-show-password" aria-label="Show Password" data-target="#password">
													<i class="fas fa-eye d-none" id="show"></i>
													<i class="fas fa-eye-slash" id="hide"></i>
												</button>
											</div>
											<br><span class="text-danger small">{{$errors->first('password')}}</span>
										</div>
									</div>

									{{-- USER TYPE --}}
									<div class="form-group col-12">
										<label class="form-label" for="type">User Type</label>
										<br>
										<select class="form-control selectpicker" name="type" id="type" data-live-search="true" title="User's role or user type">
											@foreach($types as $t)
												@if ($t->id >= Auth::user()->type->id)
												<option value="{{ $t->id }}" {{ $t->id == old('type') ? 'selected' : '' }} aria-label="{{ $t->name }}">{{ $t->name }}</option>
												@endif
											@endforeach
										</select>
										<br>
										<span class="text-danger small">{{$errors->first('type')}}</span>
									</div>
								</div>
							</div>
						</div>

						<div class="row">
							<div class="col-6 mx-auto ml-lg-auto d-flex flex-row">
								<button class="btn btn-success ml-auto" type="submit" data-action="submit">Submit</button>
								<a href="javascript:void(0);" onclick="confirmLeave('{{route('admin.users.index')}}');" class="btn btn-danger ml-3 mr-auto">Cancel</a>
							</div>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
</div>
@endsection

@section('css')
<link rel="stylesheet" type="text/css" href="{{ asset('css/util/image-input.css') }}" />
<link rel="stylesheet" type="text/css" href="{{ asset('css/util/custom-switch.css') }}" />
@endsection

@section('scripts')
<script type="text/javascript" src="{{ asset('js/util/confirm-leave.js') }}"></script>
<script type="text/javascript" src="{{ asset('js/util/disable-on-submit.js') }}"></script>
<script type="text/javascript" src="{{ asset('js/util/image-input.js') }}"></script>
<script type="text/javascript" src="{{ asset('js/login.js') }}"></script>
@endsection