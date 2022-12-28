<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReservationMenusTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('reservation_menus', function (Blueprint $table) {
			$table->bigInteger('reservation_id')->unsigned();
			$table->bigInteger('menu_id')->unsigned();

			$table->foreign('reservation_id')->references('id')->on('reservations')->onDelete('cascade');
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
		Schema::dropIfExists('reservation_menus');
	}
}