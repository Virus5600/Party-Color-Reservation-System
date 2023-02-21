<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMenuVariationsTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('menu_variations', function (Blueprint $table) {
			$table->id();
			$table->bigInteger('menu_id')->unsigned();
			$table->string('name')->unique()->default("Default");
			$table->decimal('price', 12, 2)->unsigned()->default(0);
			$table->time('duration')->default('01:00');
			$table->softDeletes();
			$table->timestamps();

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
		Schema::dropIfExists('menu_variations');
	}
}