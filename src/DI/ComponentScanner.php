<?php

namespace Lynx\DI;

use Doctrine\Common\Annotations\AnnotationException;
use Doctrine\Common\Annotations\AnnotationReader;
use Lynx\DI\Annotations\Component;

/**
 * Class ComponentScanner
 * @package Lynx\DI
 * @Component
 */
class ComponentScanner
{
	/**
	 * @param string $path
	 * @return BeanHusk[]
	 * @throws AnnotationException
	 * @throws \ReflectionException
	 */
	public function scan(string $path)
	{
		$result = [];
		$files = $this->findPHPFiles($path);

		foreach ($files as $file)
		{
			$r = $this->scanFile($file);
			if ($r['component'])
			{
				require_once $file;

				$cls = $r['namespace'] . '\\' . $r['class'];

				$reader = new AnnotationReader();
				$reflectionClass = new \ReflectionClass($cls);
				$annotations = $reader->getClassAnnotations($reflectionClass);

				$component = false;
				foreach ($annotations as $annotation)
				{
					if ($annotation instanceof Component)
					{
						$component = $annotation;
						break;
					}
				}

				if ($component)
				{
					$parameterInfo = $this->getParameterInfo($reflectionClass->getConstructor());
					$beanName = $component->name ?: $cls;

					if (isset($result[$beanName])) {
						throw new \InvalidArgumentException("Bean name collision: Both " .
							"'{$result[$beanName]->getClass()}' and '{$cls}' are named '{$beanName}'.");
					}

					$result[$beanName] = new BeanHusk($beanName, $cls, $parameterInfo);
				}
			}
		}

		return $result;
	}

	/**
	 * @param string $path
	 * @return string[]
	 */
	private function findPHPFiles(string $path)
	{
		$result = [];

		$files = scandir($path);
		foreach ($files as $file)
		{
			if ($file === '.' || $file === '..')
			{
				continue;
			}

			if (is_dir($file))
			{
				$result += $this->findPHPFiles($this->joinPaths($path, $file));
			}
			else
			{
				if (strpos($file, '.php', -4) !== false)
				{
					$result[] = $this->joinPaths($path, $file);
				}
			}
		}

		return $result;
	}

	private function scanFile(string $file)
	{
		$handle = fopen($file, 'r');
		if (!$handle)
		{
			throw new \InvalidArgumentException("The folder " . $file . " could not be read.");
		}

		$result = [
			'component' => false,
			'namespace' => null,
			'class' => null,
		];

		try
		{
			while (!feof($handle))
			{
				$block = fread($handle, 1024);

				if (preg_match('/namespace\s+(.*?)\s*;/', $block, $match))
				{
					$result['namespace'] = $match[1];
				}

				if (preg_match('/class\s+(.+)\s*{/', $block, $match))
				{
					$result['class'] = $match[1];
				}

				if (strpos($block, '@Component') !== false)
				{
					$result['component'] = true;
				}

				if ($result['namespace'] !== null && $result['class'] !== null && $result['component'])
				{
					break;
				}
			}
		}
		finally
		{
			fclose($handle);
		}

		return $result;
	}

	private function joinPaths(string ...$parts)
	{
		$parts = array_filter($parts, function ($part) {
			return $part !== '';
		});

		return preg_replace('/\/+/', '/', join('/', $parts));
	}

	/**
	 * @param \ReflectionMethod|null $method
	 * @return ParameterInfo[]
	 */
	private function getParameterInfo($method)
	{
		if (!$method)
		{
			return [];
		}

		$result = [];
		$params = $method->getParameters();
		foreach ($params as $param)
		{
			$result[$param->getName()] = new ParameterInfo($param);
		}

		return $result;
	}
}
