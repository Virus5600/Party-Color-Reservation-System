<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

use App\Booking;

use Artisan;
use Log;
use Mail;

class BookingCancellationNotification implements ShouldQueue
{
	use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

	private Booking $booking;
	private $type, $args;

	/**
	 * Create a new job instance.
	 *
	 * @return void
	 */
	public function __construct(Booking $booking, $type, $args) {
		$this->booking = $booking;
		$this->type = $type;
		$this->args = $args;
	}

	/**
	 * Execute the job.
	 *
	 * @return void
	 */
	public function handle() {
		// Set subject
		if (!isset($this->args['subject']))
			$this->args['subject'] = "Reservation Cancellation Update";
		else
			$this->args['subject'] = ($this->args['subject'] == null) ? "Reservation Cancellation Update" : $this->args['subject'];

		$recipient = $this->booking->contactInformation()->first();
		$subject = $this->args['subject'];

		// Send email to every single one of the recipients
		Mail::send(
			"layouts.emails.bookings.cancellation",
			[
				'type' => $this->type,
				'reason' => array_key_exists('reason', $this->args) ? $this->args['reason'] : null
			],
			function ($m) use ($recipient, $subject) {
				$m->to($recipient->email)
					->from('partycolor@booking.com')
					->subject($subject);
			}
		);

		activity('mailer')
			->byAnonymous()
			->on($this->booking)
			->event('mail-sent')
			->withProperties(array_merge(['args' => $this->args], $recipient->toArray()))
			->log("Booking cancellation mail notification sent to \"{$recipient->contact_name}\" ({$recipient->email})");
	}

	public function __destruct() {
		Artisan::call('queue:work --stop-when-empty');
	}
}