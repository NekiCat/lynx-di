<?php

namespace Lynx\DI\CircularBean;

use Lynx\DI\Annotations\Component;

/**
 * Class A
 * @package Lynx\DI\BeanDependency
 * @Component
 */
class A
{
	public function __construct(B $b)
	{
	}
}
