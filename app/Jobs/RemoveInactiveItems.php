<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

use Illuminate\Support\Str;

use App\Inventory;
use App\Menu;

use Log;
use Mail;

class RemoveInactiveItems implements ShouldQueue
{
	use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

	private $inventory, $menu;

	/**
	 * Create a new job instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		$this->inventory = Inventory::getForDeletion();
		$this->menu = Menu::getForDeletion();
	}

	/**
	 * Execute the job.
	 *
	 * @return void
	 */
	public function handle()
	{
		Log::info("Beginning check for inactive items...");
		// INVENTORY
		if ($this->inventory)
			$this->deleteInventory();
		else
			Log::info("No inactive inventories found.");

		// MENU
		if ($this->menu)
			$this->deleteMenu();
		else
			Log::info("No inactive menus found.");

		Log::info("Checking for inactive items done...");
	}

	public function __destruct() {
		Log::info("[RemoveInactiveItems] Running Queue");

		Artisan::call('queue:work', [
			'--stop-when-empty' => true,
			'--tries' => 3
		]);
	}

	// PRIVATE METHODS
	private function deleteInventory() {
		Log::info("Deleting inactive inventories...");

		$count = 0;
		$items = [];
		
		foreach ($this->inventory as $i) {
			activity('jobs')
				->byAnonymous()
				->on($i)
				->event('deleted')
				->withProperties([
					'item_name' => $i->item_name,
					'quantity' => $i->quantity,
					'measurement_unit' => $i->measurement_unit,
					'critical_level' => $i->critical_level
				])
				->log("Item {$i->item_name} is now deleted permanently after being inactive for 5 or more years");

			array_push($items, $i->item_name);
			$i->deletePermanently();
			$count++;
		}

		Log::info("Finished deletion of {$count} " . Str::plural("inventory", $count) . ".", $items);
	}

	private function deleteMenu() {
		Log::info("Deleting inactive menus...");

		$count = 0;
		$items = [];
		
		foreach ($this->menu as $i) {
			activity('jobs')
				->byAnonymous()
				->on($i)
				->event('deleted')
				->withProperties([
					'name' => $i->item_name
				])
				->log("Menu {$i->name} is now deleted permanently after being inactive for 5 or more years");

			array_push($items, $i->item_name);
			$i->deletePermanently();
			$count++;
		}

		Log::info("Finished deletion of {$count} " . Str::plural("menu", $count) . ".", $items);
	}
}