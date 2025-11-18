<?php

namespace Phx\Core;

enum Palette: string
{
	case SURFACE = "surface";
	case SURFACE_VARIANT = "surface_variant";
	case INVERSE_SURFACE = "inverse_surface";
	case SURFACE_CONTAINER = "surface_container";
	case SURFACE_CONTAINER_LOW = "surface_container_low";
	case PRIMARY = "primary";
	case SECONDARY = "secondary";
	case SECONDARY_CONTAINER = "secondary_container";
	case TERTIARY = "tertiary";
	case KEY_SHADOW = "key_shadow";
	case AMBIENT_SHADOW = "ambient_shadow";

	final public function getForeground(): ForegroundColor
	{
		return match($this) {
			self::SURFACE => ForegroundColor::ON_SURFACE,
			self::SURFACE_VARIANT => ForegroundColor::ON_SURFACE_VARIANT,
			self::INVERSE_SURFACE => ForegroundColor::INVERSE_ON_SURFACE,
			self::SURFACE_CONTAINER => ForegroundColor::ON_SURFACE_CONTAINER,
			self::SURFACE_CONTAINER_LOW => ForegroundColor::ON_SURFACE,
			self::PRIMARY => ForegroundColor::ON_PRIMARY,
			self::SECONDARY => ForegroundColor::ON_SECONDARY,
			self::SECONDARY_CONTAINER => ForegroundColor::ON_SECONDARY_CONTAINER,
			self::TERTIARY => ForegroundColor::ON_TERTIARY,
			self::KEY_SHADOW => BackgroundColor::KEY_SHADOW,
			self::AMBIENT_SHADOW => BackgroundColor::AMBIENT_SHADOW,
		};
	}

	final public function getBackground(): BackgroundColor
	{
		return match($this) {
			self::SURFACE => BackgroundColor::SURFACE,
			self::SURFACE_VARIANT => BackgroundColor::SURFACE_VARIANT,
			self::INVERSE_SURFACE => BackgroundColor::INVERSE_SURFACE,
			self::SURFACE_CONTAINER => BackgroundColor::SURFACE_CONTAINER,
			self::SURFACE_CONTAINER_LOW => BackgroundColor::SURFACE_CONTAINER_LOW,
			self::PRIMARY => BackgroundColor::PRIMARY,
			self::SECONDARY => BackgroundColor::SECONDARY,
			self::SECONDARY_CONTAINER => BackgroundColor::SECONDARY_CONTAINER,
			self::TERTIARY => BackgroundColor::TERTIARY,
			self::KEY_SHADOW => BackgroundColor::KEY_SHADOW,
			self::AMBIENT_SHADOW => BackgroundColor::AMBIENT_SHADOW,
		};
	}
}
