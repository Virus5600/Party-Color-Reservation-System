@extends('layouts.admin')

@section('title', 'Settings')

@section('content')
<div class="container-fluid d-flex flex-column min-h-100">
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

	<div class="card dark-shadow flex-fill py-2 px-3 mb-3" id="inner-content">
		@if (Auth::user()->hasPermission('settings_tab_edit'))
		<form method="POST" action="{{ route('admin.settings.update') }}" class="form">
		@else
		<form class="form" readonly>
		@endif
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

					<div class="form-group text-counter-parent">
						<label class="form-label">Website Description</label>
						<textarea name="web-desc" class="form-control not-resizable text-counter-input" rows="3" data-max="255">{{ App\Settings::getValue('web-desc') == null ? 'The official website of Taytay Municipal' : App\Settings::getValue('web-desc') }}</textarea>
						<span class="text-counter small">255</span>
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
					<div data-tags-input-name="contacts" class="tag-input form-control">
						{{ App\Settings::getValue('contacts') == null ? '080-3980-4560' : App\Settings::getValue('contacts') }}
					</div>
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

			@if (Auth::user()->hasPermission('settings_tab_edit'))
			<button type="submit" class="btn btn-success" data-action="update">Update</button>
			<button type="button" class="btn btn-secondary" id="revert">Undo</button>
			@endif
		</form>
	</div>
</div>
@endsection

@section('css')
<link rel="stylesheet" type="text/css" href="{{ asset('css/util/image-input.css') }}" />
<link rel="stylesheet" type="text/css" href="{{ asset('css/util/text-counter.css') }}" />
<style type="text/css">
	.tag-input.form-control {
		padding: 0.375rem 0.75rem;
		height: calc(1.5em + 0.75rem + 2px);
	}

	div.tag {
		padding: 0;
		padding-right: 1.75rem;
	}

	div.tag:first-child { margin-left: 0.125rem; }
	div.tag:last-child { margin-right: 0.125rem; }

	div.tag, div.tag > * {
		background-color: var(--primary);
		margin: 0 0.5rem;
	}

	div.tag .tag-i {
		top: 40%;
		right: 0.25rem;
		color: #fff;
		transition: 0.25s;
	}

	div.tag .tag-i:hover {
		color: rgb(255 255 255 / 75%);
		text-decoration: none;
	}

	div.tag span { padding: 0; }
</style>
@endsection

@section('scripts')
<script type="text/javascript" src="{{ asset('js/util/image-input.js') }}"></script>
<script type="text/javascript" src="{{ asset('js/util/disable-on-submit.js') }}"></script>
<script type="text/javascript" src="{{ asset('js/util/text-counter.js') }}"></script>
<script type="text/javascript">
	$(document).ready(() => {
		$('.tag-input').tagging({
			'edit-on-delete': false
		});

		@if (Auth::user()->hasPermission('settings_tab_edit'))
		$('#revert').on('click', (e) => {
			location.reload();
		});
		@else
		$.each($('form').find('input, textarea'), (k, v) => {
			$(v).prop('readonly', true);
		});

		$('div.tag .tag-i').remove();
		@endif
	});
</script>
@endsection