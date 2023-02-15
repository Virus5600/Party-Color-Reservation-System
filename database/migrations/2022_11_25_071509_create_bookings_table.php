\<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

use App\Enum\ApprovalStatus;

class CreateBookingsTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('bookings', function (Blueprint $table) {
			$table->id();
			$table->string('control_no')->unique();
			$table->enum('booking_type', ['reservation', 'walk-ins'])->default('reservation');
			$table->time('start_at');
			$table->time('end_at');
			$table->date('reserved_at');
			$table->decimal('extension', 4, 2)->unsigned()->default(0);
			$table->decimal('price', 17, 2)->unsigned();
			$table->integer('pax')->unsigned();
			$table->string('phone_numbers');
			$table->tinyInteger('archived')->default(0);
			$table->string('status')->default(ApprovalStatus::Pending);
			$table->tinyInteger('items_returned')->default(1);
			$table->string('reason')->nullable();
			$table->softDeletes();
			$table->timestamps();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::dropIfExists('bookings');
	}
}