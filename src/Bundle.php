<?php

namespace Phx\Core;

final class Bundle
{
	public final function __construct(
		/** @var string[] $classes */
		public array $classes = [],

		/** @var string[] $typos */
		public array $typos = [],

		/** @var string[] $scripts_before */
		public array $scripts_before = [],

		/** @var string[] $scripts_after */
		public array $scripts_after = [],
	) {}
}
