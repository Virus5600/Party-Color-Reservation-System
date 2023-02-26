@extends('layouts.emails.layout')

@section('title')
{{ $subject }}
@endsection

@section('content')
<p style="text-indent: 1rem;">
	@if ($type == 'creation')
	A reservation has been placed!
	@elseif ($type == 'cancellation request')
	You've requested for a reservation cancellation!
	@elseif ($type == 'cancellation revoke')
	You've retracted your cancellation request
	@endif
</p>

<p>If you did not this action, please contact us:</p>

@if ($type == 'creation')
	<p style="font-size: 1.5em;">Control Number: #{{ $booking->control_no }}</p>

	<p>View your reservations <a href="{{ route('home') }}/viewreservation">here</a>.</p>
	<p>Copy this URL if the link does not work: <span style="font-weight: bold; font-family: monospace; font-size: 1.25em;">{{ route('home') }}/viewreservation</span></p>

	<table style="width: 100%; border: 1px solid #6c757d; border-radius: 0.5em;">
		<tbody>
			@foreach ($booking->toArray() as $k => $v)
				@if (in_array($k, explode(",", "id,control_no,archived,status,items_returned,reason,cancel_requested,cancel_request_reason,deleted_at,updated_at")))
					@continue
				@endif
				
				<tr style="width: 100%; border-bottom: 1px solid #6c757d; padding-top: 1em; padding-bottom: 1em;">
					<td style="text-align: center; padding-left: 1em; padding-right: 1em; border-bottom: 1px solid #6c757d;">{{ ucwords(str_replace("_", " ", $k)) }}</div>
					<td style="text-align: center; padding-left: 1em; padding-right: 1em; border-bottom: 1px solid #6c757d;">{{ $v }}</div>
				</tr>
			@endforeach
		</tbody>
	</table>
{{-- CANCELLATION --}}
@elseif ($type == 'cancellation request')
	<p>You've requested for a reservation cancellation for booking number #{{ $booking->control_no }}</p>

	@if ($reason != null)
	<p>Below is the reason why the you've made the cancellation:</p>
	<div style="border: 1px #343A40 solid; border-radius: 0.5rem; margin: 0.5rem; padding: 0.25rem;">
		<p>{{ $reason }}</p>
	</div>
	@endif
@elseif ($type == 'cancellation revoke')
	<p>You've retracted your cancellation request for booking number #{{ $booking->control_no }}</p>

{{-- STATUSES --}}
@elseif ($type == 'accept')
	<p>You're reservation with a control number #{{ $booking->control_no }} has been accepted!</p>
@elseif ($type == 'reject')
	<p>You've retracted your cancellation request for booking number #{{ $booking->control_no }}</p>

	@if ($reason != null)
	<p>Below is the reason why the reservation is cancelled:</p>
	<div style="border: 1px #343A40 solid; border-radius: 0.5rem; margin: 0.5rem; padding: 0.25rem;">
		<p>{{ $reason }}</p>
	</div>
	@endif
@elseif ($type == 'pending')
	<p>You're reservation with a control number #{{ $booking->control_no }} has been moved to pending.</p>
@endif

<p>If you have any concerns, feel free to contact us through our channels:</p>

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