<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAdditionalOrdersTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('additional_orders', function (Blueprint $table) {
			$table->id();
			$table->bigInteger('booking_id')->unsigned();
			$table->decimal('extension', 4, 2)->unsigned()->default(0);
			$table->decimal('price', 17, 2)->unsigned();
			$table->softDeletes();
			$table->timestamps();

			$table->foreign('booking_id')->references('id')->on('bookings')->onDelete('cascade');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::dropIfExists('additional_orders');
	}
}