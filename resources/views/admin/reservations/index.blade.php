<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.2/font/bootstrap-icons.css">

@extends('layouts.admin')

@section('title', 'Reservations')

@section('content')
<div class="container-fluid d-flex flex-column min-h-100">
	<div class="row">
		<div class="col-12 col-md my-3">
			<div class="row">
				{{-- Header --}}
				<div class="col-12 col-md-6 text-center text-md-left">
					<h1>Reservations</h1>
				</div>

				{{-- Controls --}}
				<div class="col-12 col-md-6">
					<div class="row">
						{{-- ADD --}}
						<div class="col-12 col-md-6 text-center text-md-right">
							<a href="@{{ route('admin.reservations.create') }}" class="btn btn-success m-auto"><i class="fa fa-plus-circle mr-2"></i>Add Reservation</a>
						</div>

						{{-- SEARCH --}}
						@include('components.admin.admin-search', ['type' => 'reservations'])
					</div>
				</div>
				{{-- Controls End --}}
			</div>
		</div>
	</div>

	<div class="card dark-shadow overflow-x-scroll flex-fill mb-3" id="inner-content">
		<div class="container-fluid">
			{{-- Header --}}
			<div class="row">
				<div class="col-2 border d-flex align-items-center justify-content-center bold font-weight-bold">
					Date and Time
				</div>
				<div class="col border">
					<div class="row border row d-flex align-items-center justify-content-center font-weight-bold py-2">
						2022 January
					</div>
					<div class="row">
						<?php
						$days = array("Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday");
						$firstDayWeek = 24;
						?>
						@for ($i = 0; $i < count($days); $i++)
						<div class="col border d-flex align-items-center justify-content-center text-center font-weight-bold py-1">
							<?php echo $i + $firstDayWeek; ?><br>
							(<?php echo $days[$i]; ?>)
						</div>
						@endfor
					</div>
				</div>
			</div>

			{{-- Time Slots Generation --}}
			<?php
			$minutes = 30;
			$start = "09:00";
			$end = "17:01";
			$startDate = DateTime::createFromFormat("H:i", $start);
			$endDate = DateTime::createFromFormat("H:i", $end);
			$interval = new DateInterval("PT".$minutes."M");
			$dateRange = new DatePeriod($startDate, $interval, $endDate);
			?>
			
			<div class="row">

				{{-- Time Slots --}}
				<div class="col-2">
					@foreach ($dateRange as $date)
					<div class="row border d-flex align-items-center justify-content-center">
						<?php echo $date->format("H:i"); ?>
					</div>
					@endforeach
				</div>

				{{-- Reservations --}}
				@for ($i = 0; $i < count($days); $i++)
				<div class="col">
					@foreach ($dateRange as $date)
					{{-- TODO: If reservation exists --}}
					<div class="row border">
						<div type="button" class="btn btn-link btn-block d-flex align-items-center justify-content-center" data-toggle="modal" data-target="#reservationModal" style="height: 1.5rem">
							<i class="bi bi-circle text-danger"></i>
						</div>
					</div>
					{{-- else --}}
					<!-- <div class="row border bg-light">
						<div class="btn-block d-flex align-items-center justify-content-center" style="height: 1.5rem">
							<i class="bi bi-x text-secondary"></i>
						</div>
					</div> -->
					
					@endforeach
				</div>
				@endfor

				{{-- Reservation Modal--}}
				<!-- Modal -->
				<div class="modal fade" id="reservationModal" tabindex="-1" role="dialog" aria-hidden="true">
					<div class="modal-dialog modal-dialog-centered" role="document">
						<div class="modal-content">
							<div class="modal-header">
								<h5 class="modal-title" id="exampleModalCenterTitle">Reservation Information</h5>
								<button type="button" class="close" data-dismiss="modal" aria-label="Close">
								<span aria-hidden="true">&times;</span>
								</button>
							</div>
						<div class="modal-body">
								'start_at': <br>
								'end_at': <br>
								'reserved_at': <br>
								'pax': <br>
								'contact_name':
						</div>
							<div class="modal-footer">
								<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
							</div>
						</div>
					</div>
				</div>

			</div>
		</div>
	</div>
</div>
@endsection