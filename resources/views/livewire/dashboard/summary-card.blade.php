<div class="card dark-shadow h-100 rounded {{ $backgroundClass }} {{ $textClass }}" style="{{ $backgroundStyle == null ? '' : 'background-color: ' . $backgroundStyle . ';' }} {{ $textStyle == null ? '' : 'color: ' . $textStyle . ';' }}" wire:init="loadData">
	<div class="card-body d-flex flex-row">
		<i class="fas fa-{{ $icon }} fa-4x my-auto"></i>

		<div class="d-flex flex-column text-center ml-auto my-auto">
			<h4 class="text-wrap"><small>Total {{ Str::plural($name, $data) }}</small></h4>
			<h3>
				<small>
					@if ($data === false || $data === null)
						<div class="spinner-border {{ $textClass }}" style="{{ ($backgroundStyle == null ? '' : ('background-color: ' . $backgroundStyle . ';')) }}">
							<span class="sr-only">Loading...</span>
						</div>
					@elseif ($data)
						{{ $data }}
					@else
						0
					@endif
				</small>
			</h3>
		</div>
	</div>
</div>