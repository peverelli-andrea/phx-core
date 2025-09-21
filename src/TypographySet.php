<?php

namespace Phx\Core;

final class TypographySet
{
	final public function __construct(
		public Typography $normal,
		public Typography $italic,
		public Typography $emphasized,
		public Typography $emphasized_italic,
	) {}
}
