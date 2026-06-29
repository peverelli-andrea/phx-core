<?php

namespace AndreaPeverelli\PhxCore;

use AndreaPeverelli\PhxCore\default_settings\Typos;
use AndreaPeverelli\PhxCore\default_settings\Colors;

final class Settings
{
	private function __construct() {}

	final public static function getSettings(): array
	{
		return [
			"typos" => Typos::getSettings(),
			"colors" => Colors::getSettings(),
		];
	}
}
