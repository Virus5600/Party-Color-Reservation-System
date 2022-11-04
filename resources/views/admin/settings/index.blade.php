@extends('layouts.admin')

@section('title', 'Settings')

@section('content')
<div class="container-fluid d-flex flex-column h-100">
	<div class="row">
		<div class="col-12 col-md-4 col-lg my-3">
			<div class="row">
				{{-- HEADER --}}
				<div class="col-12 text-center text-md-left">
					<h1>Settings</h1>
				</div>
			</div>
		</div>
	</div>

	<div class="card dark-shadow flex-fill mb-3 py-2 px-3" id="inner-content">
		<form class="form">
			{{ csrf_field() }}

			<h3 class="text-center font-weight-bold mb-5">Website Related</h3>

			<div class="row">
				{{-- WEB LOGO --}}
				<div class="col-12 col-lg-6">
					{{-- IMAGE INPUT --}}
					<div class="image-input-scope" id="web-logo-scope" data-settings="#image-input-settings" data-fallback-img="{{ asset('uploads/settings/default.png') }}">
						{{-- FILE IMAGE --}}
						<div class="form-group text-center image-input collapse show avatar_holder" id="web-logo-image-input-wrapper">
							<div class="row border rounded border-secondary-light py-2 mx-1">
								<div class="col-12 col-md-6 text-md-right">
									<div class="hover-cam mx-auto avatar rounded overflow-hidden">
										<img src="{{ App\Settings::getInstance('web-logo')->getImage(!App\Settings::getInstance('web-logo')->is_file) }}" class="hover-zoom img-fluid avatar" id="web-logo-container" alt="Website Logo" data-default-src="{{ asset('uploads/settings/default.png') }}">
										<span class="icon text-center image-input-float" id="web-logo" tabindex="0">
											<i class="fas fa-camera text-white hover-icon-2x"></i>
										</span>
									</div>
									<input type="file" name="web-logo" class="d-none" accept=".jpg,.jpeg,.png,.webp" data-role="image-input" data-target-image-container="#web-logo-container" data-target-name-container="#web-logo-name" >
									<h6 id="web-logo-name" class="text-truncate w-50 mx-auto text-center" data-default-name="{{ App\Settings::getInstance('web-logo')->getImage(!App\Settings::getInstance('web-logo')->is_file, false) }}">{{ App\Settings::getInstance('web-logo')->getImage(!App\Settings::getInstance('web-logo')->is_file, false) }}</h6>
								</div>

								<div class="col-12 col-md-6 text-md-left">
									<label class="form-label font-weight-bold" for="web-logo">Website Logo</label><br>
									<small class="text-muted pb-0 mb-0">
										<b>FORMATS ALLOWED:</b>
										<br>JPEG, JPG, PNG, WEBP
									</small><br>
									<small class="text-muted pt-0 mt-0"><b>MAX SIZE:</b> 5MB</small>
								</div>
							</div>
						</div>
					</div>

					{{-- LOGO ERROR --}}
					<div class="text-center">
						<span class="text-danger small">{{$errors->first('web-logo')}}</span>
					</div>
				</div>

				{{-- APP NAME --}}
				<div class="col-12 col-lg-6">
					<div class="form-group">
						<label class="form-label">Website Name</label>
						<input type="text" name="web-name" class="form-control" value="{{ App\Settings::getValue('web-name') == null ? 'Municipality of Taytay, Rizal' : App\Settings::getValue('web-name') }}" />
						<span class="text-danger small">{{$errors->first('web-name')}}</span>
					</div>

					<div class="form-group">
						<label class="form-label">Website Description</label>
						<textarea name="web-name" class="form-control not-resizable" rows="3">{{ App\Settings::getValue('web-desc') == null ? 'The official website of Taytay Municipal' : App\Settings::getValue('web-desc') }}</textarea>
						<span class="text-danger small">{{$errors->first('web-desc')}}</span>
					</div>
				</div>
			</div>

			<hr class="hr-thick">
			<h3 class="text-center font-weight-bold mb-5">Reaching Out</h3>

			<div class="row">
				{{-- PUBLIC CONTACT --}}
				<div class="col-12 col-lg-6 form-group">
					<label class="form-label">Telephone Number(s)</label>
					<input type="text" name="contacts[]" class="form-control" value="{{ App\Settings::getValue('contacts') == null ? '8 284-4771 / 8 286-6149 / 8 284-4770' : App\Settings::getValue('contacts') }}" />
				</div>

				{{-- PUBLIC EMAIL --}}
				<div class="col-12 col-lg-6 form-group">
					<label class="form-label">Email Address</label>
					<input type="text" name="email" class="form-control" value="{{ App\Settings::getValue('email') == null ? 'information@taytayrizal.gov.ph' : App\Settings::getValue('email') }}" />
				</div>

				{{-- ADDRESS --}}
				<div class="col-12 form-group">
					<label class="form-label">Address</label>
					<textarea name="address" class="form-control not-resizable" placeholder="Address" rows="5">{{ App\Settings::getValue('address') == null ? 'Don Hilario Avenue, Club Manila East Compound, Barangay San Juan Taytay, Rizal 1920 Philippines' : App\Settings::getValue('address') }}</textarea>
				</div>
			</div>
		</form>
	</div>
</div>
@endsection

@section('css')
<link rel="stylesheet" type="text/css" href="{{ asset('css/util/image-input.css') }}" />
@endsection

@section('scripts')
<script type="text/javascript" src="{{ asset('js/util/image-input.js') }}"></script>
@endsection