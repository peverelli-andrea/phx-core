<?php

namespace AndreaPeverelli\PhxCore\palette;

enum ColorScheme: string
{
	case PRIMARY = "primary";

	final public function getColor(string $theme = "light", string $contrast = "default"): Palette
	{
		if($theme === "light") {
			if($contrast === "default") {
				return match($this) {
					self::PRIMARY => Palette::PRIMARY_40,
				};
			}

			if($contrast === "high") {
				return match($this) {
					self::PRIMARY => Palette::PRIMARY_20,
				};
			}
		}

		if($theme === "dark") {
			if($contrast === "default") {
				return match($this) {
					self::PRIMARY => Palette::PRIMARY_80,
				};
			}

			if($contrast === "high") {
				return match($this) {
					self::PRIMARY => Palette::PRIMARY_95,
				};
			}
		}
	}
}
