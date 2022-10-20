@extends('layouts.admin')

@section('title', 'ユーザーズ')

@section('content')
<div class="container-fluid d-flex flex-column h-100">
	<div class="row">
		<div class="col-12 col-md my-3">
			<div class="row">
				{{-- Header --}}
				<div class="col-12 col-md-4 text-center text-md-left">
					<h1>ユーザーズ</h1>
				</div>

				{{-- Controls --}}
				<div class="col-12 col-md-8">
					<div class="row">
						{{-- SHOW DELETED --}}
						@if (Auth::user()->hasSomePermission('users_tab_delete', 'users_tab_perma_delete'))
						<div class="col-12 col-md text-center text-md-right ml-md-auto">
							<div class="btn-group-toggle" data-toggle="buttons">
								<label class="btn btn-secondary {{ $show_softdeletes == 1 ? 'active' : '' }}" for="show_softdeletes">
									<input type="checkbox" value="0" id="show_softdeletes" autocomplete="off" {{ $show_softdeletes == 1 ? 'checked' : '' }}> 削除されたを表示する
								</label>
							</div>
						</div>
						@endif

						{{-- ADD --}}
						@if (Auth::user()->hasPermission('users_tab_create'))
						<div class="col-12 col-md text-center text-md-right ml-md-auto">
							<a href="{{ route('admin.users.create') }}" class="btn btn-success m-auto"><i class="fa fa-plus-circle mr-2"></i>ユーザ追加</a>
						</div>
						@endif

						{{-- SEARCH --}}
						@include('components.admin.admin-search', ['type' => 'users', 'etcInput' => array('sd' => $show_softdeletes)])
					</div>
				</div>
				{{-- Controls End --}}
			</div>
		</div>
	</div>

	<div class="card dark-shadow overflow-x-scroll flex-fill mb-3" id="inner-content">
		<table class="table table-striped my-0">
			<thead>
				<tr>
					<th class="text-center">ユーザーズイメージ</th>
					<th class="text-center">名前</th>
					<th class="text-center">ユーザーズタイプ</th>
					<th class="text-center">Eメール</th>
					<th class="text-center"></th>
				</tr>
			</thead>

			<tbody id="table-content">
				@forelse ($users as $u)
				<tr class="enlarge-on-hover" id="tr-{{ $u->id }}">
					<td class="text-center">
						<img src="{{ $u->getAvatar() }}" alt="{{ $u->first_name }}'s Avatar" class="img img-fluid user-icon mx-auto rounded">
					</td>

					<td class="text-center align-middle mx-auto font-weight-bold">
						@if (Auth::user()->hasSomePermission('users_tab_delete', 'users_tab_perma_delete'))
							<span class="{{ $u->deleted_at ? 'text-danger' : 'text-success' }}">
								<i class="fas fa-circle small"></i>
							</span>
						@endif
						{{ $u->getName() }}
					</td>

					<td class="text-center align-middle mx-auto">
						{{ $u->type->name }}
					</td>
					
					<td class="text-center align-middle mx-auto">
						{{ $u->email }}
					</td>

					<td class="align-middle">
						<div class="dropdown ">
							<button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown" id="dropdown{{$u->id}}" aria-haspopup="true" aria-expanded="false">
								アクション
							</button>

							<div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdown{{$u->id}}">
								{{-- EDIT --}}
								@if (Auth::user()->hasPermission('users_tab_edit'))
								<a href="{{ route('admin.users.edit', [$u->id]) }}" class="dropdown-item"><i class="fas fa-pencil-alt mr-2"></i>編集</a>
								@endif

								{{-- PERMISSIONS --}}
								@if (Auth::user()->hasPermission('users_tab_permissions'))
								<a href="{{ route('admin.users.manage-permissions', [$u->id]) }}" class="dropdown-item"><i class="fas fa-user-lock mr-2"></i>権限の管理</a>
								@endif
								
								{{-- CHANGE PASSWORD (EDIT) --}}
								@if (Auth::user()->hasPermission('users_tab_edit') || Auth::user()->id == $u->id)
								<a href="javascript:void(0);" class="dropdown-item change-password" id="scp-{{ $u->id }}">
									<i class="fas fa-lock mr-2"></i>パスワードの変更
									<script type="text/javascript">
										$(document).ready(() => {
											let data = `{
												"preventDefault": true,
												"name": "{{ $u->getName() }}",
												"targetURI": "{{ route('admin.users.change-password', [$u->id]) }}",
												"notify": true,
												"for": "#tr-{{ $u->id }}"
											}`;
											$('#scp-{{ $u->id }}').attr("data-scp", data);
											$('#scp-{{ $u->id }}').find('script').remove();
										});
									</script>
								</a>
								@endif

								{{-- DELETE --}}
								@if (Auth::user()->hasPermission('users_tab_delete'))
									@if ($u->deleted_at == null)
									<a href="@{{ route('admin.users.delete', [$u->id]) }}" class="dropdown-item"><i class="fas fa-trash mr-2"></i>削除</a>
									@else
									<a href="@{{ route('admin.users.restore', [$u->id]) }}" class="dropdown-item"><i class="fas fa-recycle mr-2"></i>戻す</a>
									@endif
								@endif

								{{-- PERMANENT DELETE --}}
								@if (Auth::user()->hasPermission('users_tab_perma_delete'))
								<a href="@{{ route('admin.users.permaDelete', [$u->id]) }}" class="dropdown-item"><i class="fas fa-fire-alt mr-2"></i>完全に削除する</a>
								@endif
							</div>
						</div>
					</td>
				</tr>
				@empty
				<tr>
					<td class="text-center" colspan="5">展示するものが何も～</td>
				</tr>
				@endforelse
			</tbody>
		</table>
	</div>
</div>
@endsection

@section('meta')
<meta name="csrf-token" content="{{ csrf_token() }}">
@endsection

@section('scripts')
{{-- To someone who will handle this... if you can make this part more secure, I will be glad! QwQ --}}
<script type="text/javascript" src="{{ asset('js/util/swal-change-password.js') }}"></script>
<script type="text/javascript" src="{{ asset('js/login.js') }}"></script>
<script type="text/javascript">
	$(document).ready(() => {
		$('#show_softdeletes').on('change', (e) => {
			let obj = $(e.currentTarget);
			window.location = location.protocol + "//" + location.host + location.pathname + "?sd=" + (obj.prop('checked') ? '1' : '0');
		});
	});
</script>
@endsection