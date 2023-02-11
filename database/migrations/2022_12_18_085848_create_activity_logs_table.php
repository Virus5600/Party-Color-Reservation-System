<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateActivityLogsTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('activity_logs', function (Blueprint $table) {
			$table->id();
			$table->bigInteger('user_id')->unsigned()->default(0);
			$table->string('email')->nullable();
			$table->string('address')->nullable();
			$table->string('action')->nullable();
			$table->tinyInteger('is_automated')->default(0);
			$table->tinyInteger('is_marked')->default(0);
			$table->mediumText('reason')->nullable();
			$table->bigInteger('model_id')->unsigned()->nullable();
			$table->string('model_type')->nullable();
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
		Schema::dropIfExists('activity_logs');
	}
}
