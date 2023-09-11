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

class BookingNotification implements ShouldQueue
{
	use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

	private Booking $booking;
	private $type, $args;

	/**
	 * Create a new job instance.
	 *
	 * @return void
	 */
	public function __construct(Booking $booking, $type, $args)
	{
		$this->booking = $booking;
		$this->type = $type;
		$this->args = $args;
	}

	/**
	 * Execute the job.
	 *
	 * @return void
	 */
	public function handle()
	{
		// Set subject
		if (!isset($this->args['subject']))
			$this->args['subject'] = "Reservation Notification";
		else
			$this->args['subject'] = $this->args['subject'] ?? "Reservation Notification";

		$recipient = $this->booking->contactInformation()->first();
		$subject = $this->args['subject'];
		// Send email to every single one of the recipients
		Mail::send(
			"layouts.emails.bookings.notification",
			[
				'subject' => $this->args['subject'],
				'reason' => $this->args['reason'] ?? null,
				'type' => $this->type,
				'booking' => $this->booking
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
			->log("Creation of booking mail notification sent to \"{$recipient->contact_name}\" ({$recipient->email})");
	}

	public function __destruct() {
		Log::info("[BookingNotification] Running Queue");

		Artisan::call('queue:work', [
			'--stop-when-empty' => true,
			'--tries' => 3
		]);
	}
}