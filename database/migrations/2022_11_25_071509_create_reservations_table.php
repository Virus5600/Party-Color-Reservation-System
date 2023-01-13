\<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReservationsTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('reservations', function (Blueprint $table) {
			$table->id();
			$table->time('start_at');
			$table->time('end_at');
			$table->date('reserved_at');
			$table->decimal('extension', 4, 2)->unsigned()->default(0);
			$table->decimal('price', 17, 2)->unsigned();
			$table->integer('pax');
			$table->string('phone_numbers');
			$table->tinyInteger('archived')->default(0);
			$table->string('status')->default('pending');
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
		Schema::dropIfExists('reservations');
	}
}