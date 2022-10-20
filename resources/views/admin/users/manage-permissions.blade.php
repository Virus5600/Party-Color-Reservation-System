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
							<i class="fas fa-chevron-left mr-2"></i>ユーザーズ
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
					{{ $user->getName() }}は{{ $user->isUsingTypePermissions() ? '部門権限を使用しています【既定】' : 'ユーザ権限を使用しています【独自】' }}
				</h4>

				<div class="card-body">
					<form method="POST" action="{{ route('admin.users.update-permissions', [$user->id]) }}" enctype="multipart/form-data" class="form">
						{{ csrf_field() }}

						@php($listed_perms = array())
						@foreach($permissions as $p)
							@if (!in_array($p->slug, $listed_perms))
							<div class="row my-2">
								<div class="col-12">
									<div class="card">
										<div class="card-body">
											{{-- PERMISSION PARENT --}}
											<div class="form-check col-12">
												<input type="checkbox" name="permissions[]" id="perms_{{ $p->slug }}" {{ $user->hasPermission($p->slug) ? 'checked' : '' }} value="{{ $p->slug }}">
												<label class="form-label font-weight-bold" for="perms_{{ $p->slug }}">{{ $p->name }}</label>
											</div>

											@foreach($p->childPermissions() as $cp)
											<div class="form-check col-12 ml-4">
												<input type="checkbox" name="permissions[]" id="perms_{{ $cp->slug }}" {{ $user->hasPermission($cp->slug) ? 'checked' : '' }} value="{{ $cp->slug }}" data-parent="#perms_{{ $p->slug }}">
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
								<button class="btn btn-success ml-auto" type="submit" data-action="update">提出する</button>
								<a href="javascript:void(0);" onclick="confirmLeave('{{route('admin.users.revert-permissions', [$user->id])}}', '権限を元に戻しますか？', '部門権限を使用するか？');" class="btn btn-primary mx-3 di {{ $user->isUsingTypePermissions() ? 'disabled' : '' }}"><i class="fas fa-undo mr-2"></i>既定使用する</a>
								<a href="javascript:void(0);" onclick="confirmLeave('{{route('admin.users.index')}}');" class="btn btn-danger mr-auto">キャンセル</a>
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
				parent.on('click', (e) => {
					let p = $(e.currentTarget);
					if (!p.prop('checked')) {
						$(`[data-parent="#${p.attr('id')}"]`)
							.prop('checked', false);
					}
				});
			}

			$(v).on('click', (e) => {
				let p = $(e.currentTarget);
				let target = $(v).attr('data-parent');

				let isThereChecked = false;
				$.each($(`[data-parent="${target}"]`), (kk, vv) => {
					isThereChecked = isThereChecked || $(vv).prop('checked');
				});

				if (!isThereChecked)
					$(target).prop('checked', false);
				else
					$(target).prop('checked', true);
			});
		});
	});
</script>
@endsection