<?php

namespace AndreaPeverelli\PhxCore;

use AndreaPeverelli\PhxCore\typo\TypoRole;
use AndreaPeverelli\PhxCore\typo\TypoSubRole;

final class Typo
{
	final public function __construct(
		public TypoRole $role,
		public TypoSubRole $sub_role,
		public bool $monospace = false,
		public bool $emphasized = false,
	) {}
}
