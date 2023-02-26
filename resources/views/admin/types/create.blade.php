@extends('layouts.admin')

@section('title', 'Types')

@section('content')
<div class="container-fluid">
	<div class="row">
		<div class="col-12 col-md mt-3">
			<div class="row">
				{{-- Header --}}
				<div class="col-12">
					<h1>
						<a href="javascript:void(0);" onclick="confirmLeave('{{route('admin.types.index')}}');" class="text-dark text-decoration-none font-weight-normal">
							<i class="fas fa-chevron-left mr-2"></i>(Role) Types
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
					Create New User Type
				</h4>

				<div class="card-body">
					<form method="POST" action="{{ route('admin.types.store') }}" enctype="multipart/form-data" class="form needs-validation" novalidate>
						{{ csrf_field() }}

						<div class="row">
							<div class="col-12 col-md-6 mx-auto">
								<div class="form-group">
									<label for="name" class="form-label">Type Name</label>
									<input type="text" id="name" class="form-control" required="required" name="name" value="{{ old('name') }}">
									<span class="text-danger small">{{ $errors->first('name') }}</span>
								</div>
							</div>
						</div>

						@php($listed_perms = array())
						@foreach($permissions as $p)
							@if (!in_array($p->slug, $listed_perms))
							<div class="row my-2">
								<div class="col-12">
									<div class="card">
										<div class="card-body">
											{{-- PERMISSION PARENT --}}
											<div class="form-check col-12">
												<input type="checkbox" name="permissions[]" id="perms_{{ $p->slug }}" {{ in_array($p->id, (old('permissions') == null ? [] : old('permissions'))) ? 'checked' : '' }} value="{{ $p->id }}" {{ ($pp = $p->parentPermission()) != null ? "data-parent=#perms_{$pp->slug}" : "" }}>
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
													<input type="checkbox" name="permissions[]" id="perms_{{ $cp->slug }}" {{ in_array($cp->id, (old('permissions') == null ? [] : old('permissions'))) ? 'checked' : '' }} value="{{ $cp->id }}" data-parent="#perms_{{ $p->slug }}">
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
								<button class="btn btn-primary mx-2" type="button" id="select-all">Select All</button>
								<a href="javascript:void(0);" onclick="confirmLeave('{{ route('admin.types.index') }}');" class="btn btn-danger mr-auto">Cancel</a>
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

		const updateCheckAllBtn = (e, all = false) => {
			let btn = $(`#select-all`);
			let checkPresent = $(`[name="permissions[]"]:checkbox:checked`).length > 0;

			if (checkPresent)
				btn.text('Unselect All');
			else
				btn.text('Select All');

			if (all && checkPresent) {
				$(`[name="permissions[]"]`).prop('checked', false);
				btn.text('Select All');
			}
			else if (all && !checkPresent) {
				$(`[name="permissions[]"]`).prop('checked', true);
				btn.text('Unselect All');
			}
		};

		$('#select-all').on('click', e => updateCheckAllBtn(e, true));
		$(`[name="permissions[]"]`).on('change', e => updateCheckAllBtn(e)).trigger('change');
	});
</script>
@endsection