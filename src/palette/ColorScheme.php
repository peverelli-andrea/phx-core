<?php

namespace AndreaPeverelli\PhxCore\palette;

enum ColorScheme: string
{
	case PRIMARY = "primary";

	final public function getPaletteColor(string $theme = "light", string $contrast = "default"): Palette
	{
		if($theme === "light") {
			if($contrast === "default") {
				return match($this) {
					self::PRIMARY => Palette::PRIMARY_100,
				};
			}
		}
	}
}
