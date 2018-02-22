<?php

namespace Lynx\DI;

/**
 * Class BeanContainer
 * @package Lynx\DI
 */
class BeanHusk
{
	private $beanName;
	private $className;
	private $parameters;
	private $instance;

	/**
	 * BeanContainer constructor.
	 * @param string $beanName
	 * @param string $className
	 * @param ParameterInfo[] $parameters
	 */
	public function __construct(string $beanName, string $className, array $parameters)
	{
		$this->beanName = $beanName;
		$this->className = $className;
		$this->parameters = $parameters;
	}

	public static function of(string $beanName, $instance)
	{
		$result = new BeanHusk($beanName, get_class($instance), []);
		$result->instance = $instance;

		return $result;
	}

	public function getName()
	{
		return $this->beanName;
	}

	public function getClass()
	{
		return $this->className;
	}

	/**
	 * @return ParameterInfo[]
	 */
	public function getParameterInfo()
	{
		return $this->parameters;
	}

	/**
	 * @return bool
	 */
	public function isInitialized()
	{
		return $this->instance !== null;
	}

	/**
	 * @param array $args
	 */
	public function initialize(array $args)
	{
		if ($this->isInitialized())
		{
			return;
		}

		$ctr = $this->className;
		$this->instance = new $ctr(...$args);
	}

	/**
	 * @return object
	 */
	public function get()
	{
		if (!$this->isInitialized())
		{
			throw new \InvalidArgumentException("The bean with name: {$this->beanName} was not initialized yet.");
		}

		return $this->instance;
	}
}
