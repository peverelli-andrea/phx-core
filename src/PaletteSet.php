<?php

namespace Phx\Core;

final class PaletteSet
{
	final public function __construct(
		public string $light_normal,
		public string $light_medium,
		public string $light_high,
		public string $dark_normal,
		public string $dark_medium,
		public string $dark_high,
	) {}
}
