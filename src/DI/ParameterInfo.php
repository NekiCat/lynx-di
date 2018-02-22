<?php

namespace Lynx\DI;

/**
 * Class ParameterInfo
 * @package Lynx\DI
 */
class ParameterInfo
{
	private $parameter;

	public function __construct(\ReflectionParameter $parameter)
	{
		$this->parameter = $parameter;
	}

	public function getName()
	{
		return $this->parameter->getName();
	}

	public function getType()
	{
		return $this->parameter->getType();
	}
}
