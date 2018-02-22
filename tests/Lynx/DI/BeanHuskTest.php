<?php

namespace Lynx\DI;

use PHPUnit\Framework\TestCase;

/**
 * Class BeanContainerTest
 * @package Lynx\DI
 */
class BeanHuskTest extends TestCase
{
	/**
	 * @test
	 */
	public function itShouldInitializeWithInstance()
	{
		$instance = new \stdClass();
		$sut = BeanHusk::of("bean", $instance);

		self::assertTrue($sut->isInitialized());
		self::assertEquals([], $sut->getParameterInfo());
		self::assertEquals("bean", $sut->getName());
		self::assertEquals($instance, $sut->get());
	}

	/**
	 * @test
	 */
	public function itShouldNotInitializeAutomatically()
	{
		$sut = new BeanHusk("bean", "class", []);

		self::assertFalse($sut->isInitialized());
		self::assertEquals([], $sut->getParameterInfo());
		self::assertEquals("bean", $sut->getName());
		self::assertEquals("class", $sut->getClass());
	}

	/**
	 * @test
	 * @expectedException \InvalidArgumentException
	 * @expectedExceptionMessage The bean with name: bean was not initialized yet.
	 */
	public function itShouldThrowIfNotInitialized()
	{
		$sut = new BeanHusk("bean", "class", []);
		$sut->get();
	}

	/**
	 * @test
	 */
	public function itShouldInitialize()
	{
		$sut = new BeanHusk("bean", \stdClass::class, []);
		$sut->initialize([]);

		self::assertTrue($sut->isInitialized());
		self::assertInstanceOf(\stdClass::class, $sut->get());
	}

	/**
	 * @test
	 * @throws \ReflectionException
	 */
	public function itShouldInitializeWithParameters()
	{
		$reflectionClass = new \ReflectionClass(\Exception::class);
		$reflectionConstructor = $reflectionClass->getConstructor();
		$reflectionParameter = $reflectionConstructor->getParameters()[0];

		$info = new ParameterInfo($reflectionParameter);
		$sut = new BeanHusk("bean", \Exception::class, [$info]);
		$sut->initialize(["message"]);

		self::assertTrue($sut->isInitialized());
		self::assertInstanceOf(\Exception::class, $sut->get());
		self::assertEquals("message", $sut->get()->getMessage());
	}
}
