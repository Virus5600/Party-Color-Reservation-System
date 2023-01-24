<div class="card my-3 h-100" wire:init="loadData">
	<h4 class="text-center mt-3 mb-0">
		{{ $name }}
	</h4>

	<div class="card-body overflow-x-auto">
		<table class="table table-striped my-0">
			<thead>
				<tr>
					{{-- IF FN IS FIRST --}}
					@if ($fnFirst)
						@foreach ($columnsFn as $c)
						<th class="text-center align-middle mx-auto">
							{{ ucwords(preg_replace('/(_+)/', " ", $c)) }}
						</th>
						@endforeach
					@endif

					@foreach ($columns as $c)
					<th class="text-center align-middle mx-auto">{{ ucwords(preg_replace('/(_+)/', " ", $c)) }}</th>
					@endforeach

					{{-- IF FN IS LAST --}}
					@if (!$fnFirst)
						@foreach ($columnsFn as $c)
						<th class="text-center align-middle mx-auto">
							{{ ucwords(preg_replace('/(_+)/', " ", $c)) }}
						</th>
						@endforeach
					@endif


					@if ($hasActions)
					<th class="text-center align-middle mx-auto"></th>
					@endif
				</tr>
			</thead>
			
			<tbody>
				@forelse ($data as $d)
				<tr class="enlarge-on-hover">
					{{-- IF FN IS FIRST --}}
					@if ($fnFirst)
						@foreach ($columnsFn as $c)
						<td class="text-center align-middle mx-auto">
							@php ($c = array_key_exists($c, $aliasFn) ? $aliasFn[$c] : $c)
							{{ $d->$c() }}
						</td>
						@endforeach
					@endif

					@foreach ($columns as $c)
					<td class="text-center align-middle mx-auto">
						{{ $d->$c }}
					</td>
					@endforeach

					{{-- IF FN IS LAST --}}
					@if (!$fnFirst)
						@foreach ($columnsFn as $c)
						<td class="text-center align-middle mx-auto">
							@php ($c = array_key_exists($c, $aliasFn) ? $aliasFn[$c] : $c)
							{{ $d->$c() }}
						</td>
						@endforeach
					@endif

					@if ($hasActions)
					<td class="align-middle d-flex">
						<div class="dropdown mx-auto">
							<button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown" id="dropdown{{$d->id}}" aria-haspopup="true" aria-expanded="false">
								Action
							</button>

							<div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdown{{$d->id}}">
								{{-- SHOW --}}
								<a href="{{ route("admin.{$namespace}.show", [$d->id]) }}" class="dropdown-item"><i class="fas fa-eye mr-2"></i>View</a>

								{{-- EDIT --}}
								@if (Auth::user()->hasPermission("{$namespace}_tab_edit"))
								<a href="{{ route("admin.{$namespace}.edit", [$d->id]) }}" class="dropdown-item"><i class="fas fa-pencil-alt mr-2"></i>Edit</a>
								@endif
								
								{{-- DELETE --}}
								@if (Auth::user()->hasPermission("{$namespace}_tab_delete"))
									<a href="javascript:void(0);" onclick="confirmLeave('{{ route("admin.{$namespace}.restore", [$d->id]) }}', undefined, 'Are you sure you want to activate this?');" class="dropdown-item"><i class="fas fa-toggle-on mr-2"></i>Set Active</a>
								@endif
							</div>
						</div>
					</td>
					@endif
				</tr>
				@empty
				<tr>
					<td colspan="{{ count($columns) + count($columnsFn) + ($hasActions ? 1 : 0) }}" class="text-center">Nothing to show~</td>
				</tr>
				@endforelse
			</tbody>

			@if (count($data) > 0)
			<tfoot class="text-center">
				<tr>
					<td colspan="{{ count($columns) + count($columnsFn) + ($hasActions ? 1 : 0) }}">
						<div class="d-flex align-middle justify-content-center">{{ $data->links() }}</div>
					</td>
				</tr>
			</tfoot>
			@endif
		</table>
	</div>
</div>