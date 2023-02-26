<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderablesTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('orderables', function (Blueprint $table) {
			$table->morphs('orderable');
			$table->bigInteger('menu_variation_id')->unsigned();
			$table->integer('count')->unsigned();

			$table->foreign('menu_variation_id')->references('id')->on('menu_variations')->onDelete('cascade');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::dropIfExists('orderables');
	}
}