<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBookingMenusTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('booking_menus', function (Blueprint $table) {
			$table->morphs('orderable');
			$table->bigInteger('menu_id')->unsigned();
			$table->integer('count')->unsigned();

			$table->foreign('menu_id')->references('id')->on('menus')->onDelete('cascade');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::dropIfExists('booking_menus');
	}
}