<?php

namespace Phx\Core;

final class CommonProps
{
	final public function __construct(
		public ?string $id = null,
		public ?string $class = null,
		public ?string $style = null,
	) {}
}
