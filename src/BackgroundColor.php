<?php

namespace Phx\Core;

enum BackgroundColor: string
{
	case SURFACE = "surface";
	case PRIMARY = "primary";
	case SECONDARY = "secondary";
	case TERTIARY = "tertiary";

	final public function getCssName(): string
	{
		return str_replace("_", "-", $this->value);
	}
}
