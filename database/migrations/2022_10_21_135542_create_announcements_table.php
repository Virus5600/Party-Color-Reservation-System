<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAnnouncementsTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('announcements', function (Blueprint $table) {
			$table->id();
			$table->string('poster')->default('default.png');
			$table->string('title');
			$table->string('slug');
			$table->string('summary')->nullable();
			$table->mediumText('content')->nullable();
			$table->tinyInteger('is_draft')->default(1);
			$table->bigInteger('user_id')->unsigned();
			$table->softDeletes();
			$table->timestamps();

			$table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::dropIfExists('announcements');
	}
}
