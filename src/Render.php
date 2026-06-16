<?php

namespace AndreaPeverelli\PhxCore;

final class Render
{
	final public function __construct(
		public string $html = "",
		public array $classes = [],
		public array $css = [],
	) {}
}
