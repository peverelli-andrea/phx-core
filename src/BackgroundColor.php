<?php

namespace Phx\Core;

enum BackgroundColor: string
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

	final public function getCssName(): string
	{
		return str_replace("_", "-", $this->value);
	}
}
