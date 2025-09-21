<?php

namespace Phx\Core;

final class TypographyCss
{
	final public function __construct(
		/** @var string[] $fonts */
		public array $fonts,
		
		/** @var string[] $classes */
		public array $classes,
	) {}
}
