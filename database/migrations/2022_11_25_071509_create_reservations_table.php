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
			$table->double('extension')->unsigned()->default(0);
			$table->double('price')->unsigned();
			$table->integer('pax');
			$table->string('phone_numbers');
			$table->tinyInteger('archived')->default(0);
			$table->tinyInteger('approved')->default(0);
			$table->string('reason')->nullable();
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