<?php

namespace Phx\Core;

enum Palette: string
{
	case SURFACE = "surface";
	case PRIMARY = "primary";
	case SECONDARY = "secondary";
	case TERTIARY = "tertiary";
	case SURFACE_CONTAINER_LOW = "surface_container_low";

	final public function getForeground(): ForegroundColor
	{
		return match($this) {
			self::SURFACE => ForegroundColor::ON_SURFACE,
			self::PRIMARY => ForegroundColor::ON_PRIMARY,
			self::SECONDARY => ForegroundColor::ON_SECONDARY,
			self::TERTIARY => ForegroundColor::ON_TERTIARY,
			self::SURFACE_CONTAINER_LOW => ForegroundColor::ON_SURFACE,
		};
	}

	final public function getBackground(): BackgroundColor
	{
		return match($this) {
			self::SURFACE => BackgroundColor::SURFACE,
			self::PRIMARY => BackgroundColor::PRIMARY,
			self::SECONDARY => BackgroundColor::SECONDARY,
			self::TERTIARY => BackgroundColor::TERTIARY,
			self::SURFACE_CONTAINER_LOW => BackgroundColor::SURFACE_CONTAINER_LOW,
		};
	}
}
