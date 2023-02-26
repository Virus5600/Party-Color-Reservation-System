<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Settings extends Model
{
	protected $fillable = [
		'name',
		'value',
		'is_file'
	];

	// CUSTOM FUNCTIONS
	public static function getInstance($key = null) {
		if ($key == null)
			return Settings::get();
		return Settings::where('name', '=', $key)->first();
	}

	public static function valueToJson($key) {
		$setting = Settings::where('name', '=', $key)->first();

		if ($setting == null)
			return null;

		$toRet = array();
		foreach(preg_split("/\,\s*/", $setting->value) as $v) {
			array_push($toRet, array("value" => trim($v)));
		}
		return json_encode($toRet);
	}

	public static function getValue($key) {
		$setting = Settings::where('name', '=', $key)->first();

		if ($setting == null)
			return null;
		return $setting->value;
	}

	public static function getFile($key=0) {
		$setting = Settings::where('name', '=', $key)->first();

		if ($setting->is_file)
			return asset('uploads/settings/' . $setting->value);
		return $setting->value;
	}

	public function getImage($useDefault=false, $getFull=true) {
		$settingF = $this->value;
		$settingU = asset('/uploads/settings/'.$this->value);
		$settingD = asset('/uploads/settings/default.png');
		$toRet = null;

		if ($useDefault) {
			if ($getFull)
				return $settingD;
			else
				return 'default.png';
		}
		else {
			if ($getFull) {
				if (!$this->is_file)
					$toRet = $settingF;
				else
					$toRet = $settingU;
			}
			else {
				$toRet = $settingF;
			}
		}

		return $toRet;
	}

	// STATIC FUNCTIONS
	public static function showRoute() {
		return route('admin.settings.index');
	}
}