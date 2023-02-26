@extends('layouts.emails.layout')

@section('title')
An update for your cancellation request has arrived!
@endsection

@section('content')
<p style="text-indent: 1rem;">
	The cancellation is {{ $type }}ed!
	
	@if ($reason != null)
	Below is the reason why the cancellation is {{ $type }}ed:
	@endif
</p>

@if ($reason != null)
<div style="border: 1px #343A40 solid; border-radius: 0.5rem; margin: 0.5rem; padding: 0.25rem;">
	<p>{{ $reason }}</p>
</div>
@endif

<p>If you did not request for this cancellation, please contact us through our emails:</p>

<p>Emails</p>
<ul>
	@foreach(explode(",", App\Settings::getValue('emails')) as $m)
	<li><a href="mailto:{{ $m }}">{{ $m }}</a></li>
	@endforeach
</ul>

<p>Contact Numbers:</p>
<ul>
	@foreach(explode(",", App\Settings::getValue('contacts')) as $c)
	<li><a href="tel:{{ $c }}">{{ $c }}</a></li>
	@endforeach
</ul>
@endsection