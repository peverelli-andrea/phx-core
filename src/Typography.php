<?php

namespace Phx\Core;

final class Typography
{
	final public function __construct(
		/** @var FontSource[] $sources */
		public array $sources = [],
		public string $name = "",
		public string $family = "",
		public string $style = "normal",
		public string $size = "",
		public string $weight = "",

		public string $letter_spacing = "",
		// letter_spacing = tracking/font_size[rem]

		public string $line_height = "",
		public string $vf_weight = "",
		public string $grade = "",
		public string $width = "",
		public string $rounding = "",
		public string $optical_size = "",
		public string $cursive = "",
		public string $slant = "",
		public string $fill = "",
		public string $hyper_expansion = "",
	) {}
}
