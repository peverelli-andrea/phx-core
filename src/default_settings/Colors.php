<?php

namespace AndreaPeverelli\PhxCore\default_settings;

use AndreaPeverelli\PhxCore\palette\Palette;

final class Colors
{
	private function __construct() {}

	final public static function getSettings(): array
	{
		return [
			"dark" => [
				"default" => [
					Palette::PRIMARY_100->value => "#FFFFFF",
				],
			],
		];
	}
}
