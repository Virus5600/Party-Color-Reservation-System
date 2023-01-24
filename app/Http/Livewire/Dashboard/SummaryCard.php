<?php

namespace App\Http\Livewire\Dashboard;

use Livewire\Component;

use Log;
use ReflectionClass;

class SummaryCard extends Component
{   
	// UTIL VAR
	public $loadData = false;
	public $usesSoftDeletes = false;

	// VAR
	public $clazz;
	public $icon = 'tachometer-alt';
	public $name;
	public $backgroundClass;
	public $backgroundStyle;
	public $textClass;
	public $textStyle;

	public function mount($clazz, $icon, $name = null, $backgroundColorClass = null, $backgroundColorStyle = null, $textColorClass = null, $textColorStyle = null) {
		$this->clazz = $clazz;
		$this->name = $name ? $name : $this->getShortName($clazz);
		$this->icon = $icon;
		$this->backgroundClass = $backgroundColorClass;
		$this->backgroundStyle = $backgroundColorStyle;
		$this->textClass = $textColorClass;
		$this->textStyle = $textColorStyle;

		// Identifies whether it uses soft deletes or not
		if (in_array('Illuminate\Database\Eloquent\SoftDeletes', class_uses($this->clazz)))
			$this->usesSoftDeletes = true;
	}

	public function render() {
		$data = null;

		if ($this->loadData) {
			$clazzz = $this->clazz;
			// Identify if it uses soft deletes or not
			if ($this->usesSoftDeletes)
				$data = $clazzz::withTrashed()->count();
			else
				$data = $clazzz::count();
		}

		// Returns the data
		return view('livewire.dashboard.summary-card', [
			'data' => $data,
			'icon' => $this->icon,
			'name' => $this->name,
			'backgroundClass' => $this->backgroundClass,
			'backgroundStyle' => $this->backgroundStyle,
			'textClass' => $this->textClass,
			'textStyle' => $this->textStyle,
		]);
	}

	// UTIL FUNCTIONS
	public function loadData() {
		$this->loadData = true;
	}

	private function getShortName($clazz) {
		return (new ReflectionClass($clazz))->getShortName();
	}
}
