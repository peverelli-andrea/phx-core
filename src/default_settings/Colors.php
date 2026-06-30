<?php

namespace AndreaPeverelli\PhxCore\default_settings;

use AndreaPeverelli\PhxCore\palette\Palette;

final class Colors
{
	private function __construct() {}

	final public static function getSettings(): array
	{
		return [
			Palette::PRIMARY_20->value => "#381E72",
			Palette::PRIMARY_40->value => "#6750A4",
			Palette::PRIMARY_80->value => "#D0BCFF",
			Palette::PRIMARY_95->value => "#F6EDFF",
		];
	}
}
