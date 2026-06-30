<?php

namespace AndreaPeverelli\PhxCore;

enum ColorMode: string
{
	case COLOR = "color";
	case BACKGROUND_COLOR = "background_color";
	case FILL = "fill";

	final public function getCssPropertyName(): string
	{
		return match($this) {
			self::COLOR => "color",
			self::BACKGROUND_COLOR => "background-color",
			self::FILL => "fill",
		};
	}
}
