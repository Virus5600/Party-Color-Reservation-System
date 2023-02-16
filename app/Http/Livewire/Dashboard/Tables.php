<?php

namespace App\Http\Livewire\Dashboard;

use Livewire\Component;
use Livewire\WithPagination;

use Log;
use ReflectionClass;

class Tables extends Component
{
	use WithPagination;

	// UTIL VAR
	protected $paginationTheme = 'bootstrap';
	public $loadData = false;
	public $usesSoftDeletes = false;
	public $hasActions = true;

	// VAR
	public $clazz;
	public $name;
	public $conditions;
	public $hiddenColumns;
	public $columns;
	public $alias;
	public $existingColumns;
	public $urlNamespace;
	public $paginate;
	public $fnFirst;

	public function mount(
			$clazz,
			string $name = null,
			array $conditions,
			array $hiddenColumns = [],
			array $columns = ["*"],
			array $alias = [],
			array $columnsFn = [],
			array $aliasFn = [],
			string $urlNamespace = null,
			bool $hasActions = true,
			int $paginate = 10,
			bool $fnFirst = false) {
		
		$this->clazz = $clazz;
		$this->name = $name ? $name : $this->getShortName($clazz);
		$this->conditions = $conditions;
		$this->hiddenColumns = $hiddenColumns;
		$this->columns = $columns;
		$this->alias = $alias;
		$this->columnsFn = $columnsFn;
		$this->aliasFn = $aliasFn;
		$this->urlNamespace = $urlNamespace ? $urlNamespace : strtolower($this->getShortName($clazz));
		$this->paginate = $paginate;
		$this->fnFirst = $fnFirst;

		$this->hasActions = $hasActions;

		if ($columns) {
			if ($this->columns == ["*"]) {
				$this->columns = $clazz::query()
					->getConnection()
					->getSchemaBuilder()
					->getColumnListing((new $clazz)->getTable());
			}
			else {
				$this->columns = $columns;
				if (gettype($columns) == 'string')
					$this->columns = ["*"];
			}
		}

		$this->existingColumns = $clazz::query()
			->getConnection()
			->getSchemaBuilder()
			->getColumnListing((new $clazz)->getTable());

		// Identifies whether it uses soft deletes or not
		if (in_array('Illuminate\Database\Eloquent\SoftDeletes', class_uses($this->clazz)))
			$this->usesSoftDeletes = true;
	}

	public function render()
	{
		if ($this->loadData) {
			$clazz = $this->clazz;
			$clazz = $clazz::select("id");

			// Adds all SELECT queries
			foreach ($this->columns as $c) {
				if (in_array($c, $this->existingColumns)) {
					$clazz = $clazz->addSelect("{$c}");
				}
				else if (array_key_exists($c, $this->alias)) {
					$clazz->addSelect("{$this->alias[$c]} AS $c");
				}
			}

			foreach ($this->hiddenColumns as $c) {
				if (in_array($c, $this->existingColumns))
					$clazz = $clazz->addSelect("{$c}");
			}

			// Iterate through the conditions
			foreach ($this->conditions as $c) {
				// If the condition is * (all), query then break the loop immediately.
				if ($c == "*") {
					$clazz = $this->clazz;
					$clazz = $clazz::select("id");

					// Re-adds all SELECT queries
					foreach ($this->columns as $c) {
						if (in_array($c, $this->existingColumns)) {
							$clazz = $clazz->addSelect("{$c}");
						}
						else if (array_key_exists($c, $this->alias)) {
							$clazz->addSelect("{$this->alias[$c]} AS $c");
						}
					}
					break;
				}
				// If withTrashed, include all soft deleted items... only if this is using soft deletes traits.
				else if ($c == 'withTrashed') {
					if ($this->usesSoftDeletes)
						$clazz = $clazz->withTrashed();
				}
				// If trashed, get all deleted at when using soft deletes only.
				else if ($c == 'trashed') {
					if ($this->usesSoftDeletes)
						$clazz = $clazz->onlyTrashed();
				}
				else if ($c == 'latest') {
					$clazz = $clazz->latest();
				}
				// Other condition that is not empty falls here...
				else if (strlen($c) > 0) {
					// Splice the condition.
					$splice = explode(" ", preg_replace("/(\s+)/", " ", $c));
					$splice[3] = count($splice) >= 4 ? $splice[3] : false;
					
					// Identifies if the comparison is between columns or not.
					if ($splice[3])
						$clazz = $clazz->whereColumn($splice[0], $splice[1], $splice[2]);
					else
						$clazz = $clazz->where($splice[0], $splice[1], $splice[2]);
				}
			}
			$data = $clazz->paginate(10);
		}
		else {
			$data = collect([]);
		}

		return view('livewire.dashboard.tables', [
			'data' => $data,
			'columns' => $this->columns,
			'namespace' => $this->urlNamespace,
			'name' => $this->name,
			'hasActions' => $this->hasActions,
			'columnsFn' => $this->columnsFn,
			'aliasFn' => $this->aliasFn,
			'fnFirst' => $this->fnFirst
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