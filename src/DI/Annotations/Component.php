<?php

namespace Lynx\DI\Annotations;

/**
 * Class Component
 * @package Lynx\DI\Annotations
 * @Annotation
 * @Target("CLASS")
 */
class Component
{
	/**
	 * @var string
	 */
	public $name;
}
