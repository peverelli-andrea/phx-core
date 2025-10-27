<?php

namespace Phx\Core;

final class ColorStatesCss
{
	final public function __construct(
		public ?string $default = null,
		public ?string $disabled = null,
		public ?string $hover = null,
		public ?string $focus = null,
		public ?string $pressed = null,
		public ?string $toggled_default = null,
		public ?string $toggled_hover = null,
		public ?string $toggled_focus = null,
		public ?string $toggled_pressed = null,
	) {}
}
