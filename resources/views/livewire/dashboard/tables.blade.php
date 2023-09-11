<div class="card my-3 h-100" wire:init="loadData" wire:key="{{ Str::camel($name) }}">
	<h4 class="text-center mt-3 mb-0">
		{{ $name }}
	</h4>

	<div class="card-body overflow-x-auto h-100 d-flex flex-column">
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
					@if (!in_array($c, $hiddenColumns))
					<th class="text-center align-middle mx-auto">{{ ucwords(preg_replace('/(_+)/', " ", $c)) }}</th>
					@endif
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

					@if ($hasShow)
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
					@if (!in_array($c, $hiddenColumns))
					<td class="text-center align-middle mx-auto">
						{{ $d->$c }}
					</td>
					@endif
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

					@if ($hasActions && !$hasShow)
					<td class="align-middle text-center">
						<div class="dropdown text-left">
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
					@elseif ($hasShow)
					<td class="align-middle">
						<a href="{{ $clazz == 'Spatie\Activitylog\Models\Activity' ? route('admin.activity-log.show', [$d->id]) : $clazz::showRoute($d->id) }}" class="btn btn-primary btn-sm m-auto"><i class="fas fa-eye fa-sm"></i></a>
					</td>
					@endif
				</tr>
				@empty
				<tr>
					<td colspan="{{ count($columns) + count($columnsFn) + ($hasActions ? 1 : 0) + ($hasShow ? 1 : 0) }}" class="text-center">Nothing to show~</td>
				</tr>
				@endforelse
			</tbody>
		</table>

		@if ($loadData)
		<div id="table-paginate" class="w-100 d-flex align-middle mt-auto my-3">
			{{ $data->links() }}
		</div>
		@endif
	</div>
</div>