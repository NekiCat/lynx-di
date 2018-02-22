<?php

namespace Lynx\DI;

/**
 * Class Container
 * @package Lynx\DI
 */
class Container
{
	/**
	 * @var ComponentScanner
	 */
	private $componentScanner;

	/**
	 * @var BeanHusk[]
	 */
	private $beans = [];

	/**
	 * Container constructor.
	 */
	public function __construct()
	{
		$this->componentScanner = new ComponentScanner();
		$this->beans[Container::class] = BeanHusk::of(Container::class, $this);
	}

	/**
	 * @param string $path
	 * @throws \Doctrine\Common\Annotations\AnnotationException
	 * @throws \ReflectionException
	 */
	public function register(string $path)
	{
		$this->beans += $this->componentScanner->scan($path);
	}

	/**
	 * @param string $beanName
	 * @return bool
	 */
	public function contains(string $beanName)
	{
		return isset($this->beans[$beanName]);
	}

	/**
	 * @param string $beanName
	 * @return object
	 */
	public function get(string $beanName)
	{
		if (!isset($this->beans[$beanName]))
		{
			throw new \InvalidArgumentException("No bean found with name: {$beanName}");
		}

		$component = $this->beans[$beanName];
		if (!$component->isInitialized())
		{
			$parameters = $component->getParameterInfo();
			$arguments = [];
			foreach ($parameters as $parameter)
			{
				$arguments[] = $this->get($parameter->getType());
			}

			$component->initialize($arguments);
		}

		return $component->get();
	}
}
