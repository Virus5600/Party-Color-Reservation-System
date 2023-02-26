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
		<div class="col-12 col-md-11 col-lg-10 mx-auto">
			<div class="card dark-shadow mb-5" id="inner-content">
				<h4 class="card-header">
					{{ $user->getName() }} is {{ $user->isUsingTypePermissions() ? 'using account role permissions (Default)' : 'using user permissions (Custom)' }}
				</h4>

				<div class="card-body">
					<form method="POST" action="{{ route('admin.users.update-permissions', [$user->id]) }}" enctype="multipart/form-data" class="form">
						{{ csrf_field() }}
						<input type="hidden" name="from" value="{{ $from }}"/>

						@php($listed_perms = array())
						@foreach($permissions as $p)
							@if (!in_array($p->slug, $listed_perms))
							<div class="row my-2">
								<div class="col-12">
									<div class="card">
										<div class="card-body">
											{{-- PERMISSION PARENT --}}
											<div class="form-check col-12">
												<input type="checkbox" name="permissions[]" id="perms_{{ $p->slug }}" {{ $user->hasPermission($p->slug) ? 'checked' : '' }} value="{{ $p->id }}" {{ ($pp = $p->parentPermission()) != null ? "data-parent=#perms_{$pp->slug}" : "" }}>
												<label class="form-label font-weight-bold" for="perms_{{ $p->slug }}">
													{{ $p->name }}
													
													@if ($pp != null)
														<span class="badge badge-info">Child of {{ $pp->name }}</span>
													@endif
												</label>
											</div>

											{{-- PERMISSION CHILD --}}
											@foreach($p->childPermissions() as $cp)
												@if (count($cp->childPermissions()) > 0)
													@continue
												@endif

												<div class="form-check col-12 ml-4">
													<input type="checkbox" name="permissions[]" id="perms_{{ $cp->slug }}" {{ $user->hasPermission($cp->slug) ? 'checked' : '' }} value="{{ $cp->id }}" data-parent="#perms_{{ $p->slug }}">
													<label class="form-label" for="perms_{{ $cp->slug }}">{{ $cp->name }}</label>
												</div>
												@php(array_push($listed_perms, $cp->slug))
											@endforeach
										</div>
									</div>
								</div>
								@php(array_push($listed_perms, $p->slug))
							</div>
							@endif
						@endforeach

						<div class="row">
							<div class="col-6 mx-auto ml-lg-auto d-flex flex-row">
								<button class="btn btn-success ml-auto" type="submit" data-action="update">Submit</button>
								<a href="javascript:void(0);" onclick="confirmLeave('{{route('admin.users.revert-permissions', [$user->id, 'from' => $from])}}', 'Restore Permissions?', 'Use user type permissions?');" class="btn btn-primary mx-3 di {{ $user->isUsingTypePermissions() ? 'disabled' : '' }}"><i class="fas fa-undo mr-2"></i>Reset Permission</a>
								<a href="javascript:void(0);" onclick="confirmLeave('{{url($from)}}');" class="btn btn-danger mr-auto">Cancel</a>
							</div>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
</div>
@endsection

@section('scripts')
<script type="text/javascript" src="{{ asset('js/util/confirm-leave.js') }}"></script>
<script type="text/javascript" src="{{ asset('js/util/disable-on-submit.js') }}"></script>
<script type="text/javascript">
	$(document).ready(() => {
		let pp = [];
		$.each($(`[data-parent]`), (k, v) => {
			let obj = $(v).attr('data-parent');
			if (!pp.includes(obj)) {
				pp.push(obj);

				let parent = $(obj);
				parent.on('click change', (e) => {
					let p = $(e.currentTarget);
					if (!p.prop('checked')) {
						$(`[data-parent="#${p.attr('id')}"]`)
							.prop('checked', false)
							.trigger('change');
					}
				});
			}

			$(v).on('click change', (e) => {
				let p = $(e.currentTarget);
				let target = $(v).attr('data-parent');

				let isThereChecked = false;
				$.each($(`[data-parent="${target}"]`), (kk, vv) => {
					isThereChecked = isThereChecked || $(vv).prop('checked');
				});

				if (!isThereChecked) {
					target = $(target);
					target.prop('checked', false);
					
					if (typeof target.attr('data-parent') != 'undefined')
						$(target.attr('data-parent')).prop('checked', false);
				}
				else {
					target = $(target);
					target.prop('checked', true);

					if (typeof target.attr('data-parent') != 'undefined')
						$(target.attr('data-parent')).prop('checked', true);
				}
			});
		});
	});
</script>
@endsection