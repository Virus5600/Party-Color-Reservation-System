<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAnnouncementContentImagesTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('announcement_content_images', function (Blueprint $table) {
			$table->id();
			$table->bigInteger('announcement_id')->unsigned();
			$table->string('image_name');
			$table->timestamps();

			$table->foreign('announcement_id')->references('id')->on('announcements')->onDelete('cascade');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::dropIfExists('announcement_content_images');
	}
}