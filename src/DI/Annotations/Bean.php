<?php

namespace Lynx\DI\Annotations;

use Doctrine\Common\Annotations\Annotation\Target;

/**
 * Class Bean
 * @package Lynx\DI\Annotations
 * @Annotation
 * @Target("CLASS")
 */
class Bean
{
	/**
	 * @var string
	 */
	public $name;
}
