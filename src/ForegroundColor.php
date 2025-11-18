<?php

namespace Phx\Core;

enum ForegroundColor: string
{
	case ON_SURFACE = "on_surface";
	case ON_SURFACE_VARIANT = "on_surface_variant";
	case INVERSE_ON_SURFACE = "inverse_on_surface";
	case ON_SURFACE_CONTAINER = "on_surface_container";
	case ON_PRIMARY = "on_primary";
	case ON_SECONDARY = "on_secondary";
	case ON_SECONDARY_CONTAINER = "on_secondary_container";
	case ON_TERTIARY = "on_tertiary";

	final public function getCssName(): string
	{
		return str_replace("_", "-", $this->value);
	}
}
