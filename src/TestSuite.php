<?php

namespace AndreaPeverelli\PhxCore;

final class TestSuite
{
	final public static function init(string $test_name): void
	{
		echo "\n";
		echo "$test_name:\n";
	}

	final public static function test(
		mixed $got,
		mixed $expect,
		string $test_name = "Default",
	): void
	{
		if($got == $expect) {
			echo "$test_name - \033[92mPassed\033[0m\n";

			return;
		}

		echo "$test_name - \033[91mFailed\033[0m\n";
		echo "\033[91mExpected\033[0m\n";
		var_dump($expect);
		echo "\033[91mGot\033[0m\n";
		var_dump($got);

		return;
	}
}
