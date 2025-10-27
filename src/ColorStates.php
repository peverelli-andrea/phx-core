<?php

namespace Phx\Core;

final class ColorStates
{
	final public function __construct(
		public ForegroundColor|BackgroundColor|null $default = null,
		public ForegroundColor|BackgroundColor|null $disabled = null,
		public ForegroundColor|BackgroundColor|null $hover = null,
		public ForegroundColor|BackgroundColor|null $focus = null,
		public ForegroundColor|BackgroundColor|null $pressed = null,
		public ForegroundColor|BackgroundColor|null $toggled_default = null,
		public ForegroundColor|BackgroundColor|null $toggled_hover = null,
		public ForegroundColor|BackgroundColor|null $toggled_focus = null,
		public ForegroundColor|BackgroundColor|null $toggled_pressed = null,
	) {}
}
