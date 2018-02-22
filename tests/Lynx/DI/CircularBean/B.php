<?php

namespace Lynx\DI\CircularBean;

use Lynx\DI\Annotations\Component;

/**
 * Class B
 * @package Lynx\DI\BeanDependency
 * @Component
 */
class B
{
	public function __construct(A $a)
	{
	}
}
