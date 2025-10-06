<?php

namespace Phx\Core;

enum ForegroundColor: string
{
	case ON_SURFACE = "on_surface";
	case ON_PRIMARY = "on_primary";
	case ON_SECONDARY = "on_secondary";
	case ON_TERTIARY = "on_tertiary";

	final public function getCssName(): string
	{
		return str_replace("_", "-", $this->value);
	}
}
