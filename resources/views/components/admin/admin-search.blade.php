@php($etcInput = isset($etcInput) ? $etcInput : null)

<div class="col-12 col-md-6 text-center text-md-right my-2 my-md-auto d-flex">
	<form method="POST" action="{{ route('adminSearch') }}" enctype="multipart/form-data" id="admin-search" class="form m-auto">
		{{ csrf_field() }}
		<input type='hidden' name="type" value='{{ $type }}' />
		
		@if ($etcInput != null)
		@foreach($etcInput as $k => $v)
		<input type="hidden" name="{{ $k }}" value="{{ $v }}">
		@endforeach
		@endif

		<div class="input-group">
			<input type='search' name="search" placeholder="「/」を押して検索します" class="form-control" id="search" aria-label="Search" accesskey="/" />
			<div class="input-group-append">
				<button type="submit" class="btn btn-secondary"><i class="fas fa-search" aria-hidden="true"></i></button>
			</div>
		</div>

		<script type="text/javascript" src="{{ asset('js/util/serialize-form-json.js') }}"></script>
		<script type="text/javascript" src="{{ asset('js/hooks/adminSearchHook.js') }}"></script>
	</form>
</div>