<?php

namespace Phx\Core;

final class Render
{
	final public function __construct(
		/** @var string[] $classes */
		public array $classes = [],

		/** @var string[] $typos */
		public array $typos = [],

		/** @var ForegroundColor[]|BackgroundColor[] $colors*/
		public array $colors = [],

		/** @var string[] $scripts_before */
		public array $scripts_before = [],

		/** @var string[] $scripts_after */
		public array $scripts_after = [],

		public string $html = "",
	) {}
}
