<?php

namespace Phx\Core;

enum BackgroundColor: string
{
	case SURFACE = "surface";
	case PRIMARY = "primary";
	case SECONDARY = "secondary";
	case TERTIARY = "tertiary";
	case SURFACE_CONTAINER_LOW = "surface_container_low";
	case KEY_SHADOW = "key_shadow";
	case AMBIENT_SHADOW = "ambient_shadow";

	final public function getCssName(): string
	{
		return str_replace("_", "-", $this->value);
	}
}
