<?php

namespace AndreaPeverelli\PhxCore\default_settings;

use AndreaPeverelli\PhxCore\typo\TypoRole;
use AndreaPeverelli\PhxCore\typo\TypoSubRole;

final class Typos
{
	private function __construct() {}

	final public static function getSettings(): array
	{
		$fonts = [
			"Google Sans" => [
				500 => [
					"font-family" => "Google Sans",
					"src" => [
						[
							"url" => "/asssets/fonts/google-sans-regular.woff2",
							"format" => "woff2",
						],
						[
							"url" => "/assets/fonts/google-sans-regular.woff",
							"format" => "woff",
						],
					],
					"font-weight" => "500",
				],
			],
		];

		return [
			"proportional" => [
				"emphasized" => [
					TypoRole::DISPLAY->value => [
						TypoSubRole::LARGE->value => [
							...$fonts["Google Sans"][500],
							"font-size" => "57px",
							"line-height" => "64px",
							"letter-spacing" => "0",
						],
					],
				],
			],
		];
	}
}
