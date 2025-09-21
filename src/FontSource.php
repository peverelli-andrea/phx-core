<?php

namespace Phx\Core;

final class FontSource
{
	final public function __construct(
		public string $source = "",
		public string $format = "",
		public ?string $tech = null,
	) {}
}
