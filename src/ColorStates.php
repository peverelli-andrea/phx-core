<?php

namespace Phx\Core;

final class ColorStates
{
	final public function __construct(
		public ForegroundColor|BackgroundColor|null $default = null,
		public ForegroundColor|BackgroundColor|null $disabled = null,
		public ForegroundColor|BackgroundColor|bool|null $hover = true,
		public ForegroundColor|BackgroundColor|bool|null $focus = true,
		public ForegroundColor|BackgroundColor|bool|null $pressed = true,
		public ForegroundColor|BackgroundColor|null $toggled_default = null,
		public ForegroundColor|BackgroundColor|bool|null $toggled_hover = true,
		public ForegroundColor|BackgroundColor|bool|null $toggled_focus = true,
		public ForegroundColor|BackgroundColor|bool|null $toggled_pressed = true,
	) {}
}
