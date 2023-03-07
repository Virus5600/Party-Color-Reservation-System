@php($etcInput = isset($etcInput) ? $etcInput : null)

<div class="text-center text-md-right my-2 my-md-auto d-flex">
	<form method="POST" action="{{ route('adminSearch', [auth()->user()->id]) }}" enctype="multipart/form-data" id="admin-search" class="form">
		{{ csrf_field() }}
		<input type='hidden' name="type" value='{{ $type }}' />

		@if ($etcInput != null)
		@foreach($etcInput as $k => $v)
		<input type="hidden" name="etcInput[]" value="{{ $k }} => {{ $v }}">
		@endforeach
		@endif

		<div class="input-group">
			<input type='search' name="search" placeholder="Press / to search" class="form-control" id="search" aria-label="Search" accesskey="/" value="{{ request('search', '') }}" />
			<div class="input-group-append">
				<button type="submit" class="btn btn-secondary"><i class="fas fa-search" aria-hidden="true"></i></button>
			</div>
		</div>

		<script type="text/javascript" src="{{ asset('js/util/serialize-form-json.js') }}"></script>
		<script type="text/javascript" src="{{ asset('js/hooks/adminSearchHook.js') }}"></script>
	</form>
</div>