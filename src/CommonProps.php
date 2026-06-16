<?php

namespace AndreaPeverelli\PhxCore;

final class CommonProps
{
	final public function __construct(
		public ?string $id = null,
		public array $classes = [],
		public array $attributes = [],
	) {}
}
