<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMenuVariationItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('menu_variation_items', function (Blueprint $table) {
            $table->bigInteger('menu_variation_id')->unsigned();
            $table->bigInteger('inventory_id')->unsigned();
            $table->float('amount')->unsigned()->default(1);
            $table->tinyInteger('is_unlimited')->default(0);

            $table->foreign('menu_variation_id')->references('id')->on('menu_variations')->onDelete('cascade');
            $table->foreign('inventory_id')->references('id')->on('inventories')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('menu_variation_items');
    }
}
