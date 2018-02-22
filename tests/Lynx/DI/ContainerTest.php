<?php

namespace Lynx\DI;

use PHPUnit\Framework\TestCase;

/**
 * Class ContainerTest
 * @package Lynx\DI
 */
class ContainerTest extends TestCase
{
	/**
	 * @test
	 */
	public function itShouldRegisterSelf()
	{
		$sut = new Container();

		self::assertTrue($sut->contains(Container::class));
		self::assertEquals($sut, $sut->get(Container::class));
	}

	/**
	 * @test
	 * @expectedException \InvalidArgumentException
	 * @expectedExceptionMessage No bean found with name: InvalidBeanName
	 */
	public function itShouldThrowIfNotFound()
	{
		$sut = new Container();
		$sut->get("InvalidBeanName");
	}

	/**
	 * @test
	 * @throws \Exception
	 */
	public function itShouldFindComponents()
	{
		$sut = new Container();
		$sut->register(__DIR__ . "/BeanSimple");

		self::assertTrue($sut->contains("Lynx\\DI\\BeanSimple\\A"));
	}

	/**
	 * @test
	 * @throws \Exception
	 */
	public function itShouldIgnoreNormalClasses()
	{
		$sut = new Container();
		$sut->register(__DIR__ . "/NotABean");

		self::assertFalse($sut->contains("Lynx\\DI\\NotABean\\A"));
	}

	/**
	 * @test
	 * @throws \Exception
	 */
	public function itShouldAllowComponentNaming()
	{
		$sut = new Container();
		$sut->register(__DIR__ . "/BeanName");

		self::assertInstanceOf("Lynx\\DI\\BeanName\\A", $sut->get("Bean"));
	}

	/**
	 * @test
	 * @throws \Exception
	 */
	public function itShouldResolveDependency()
	{
		$sut = new Container();
		$sut->register(__DIR__ . "/BeanDependency");

		self::assertTrue($sut->contains("Lynx\\DI\\BeanDependency\\A"));
		self::assertTrue($sut->contains("Lynx\\DI\\BeanDependency\\B"));
		self::assertInstanceOf("Lynx\\DI\\BeanDependency\\B", $sut->get("Lynx\\DI\\BeanDependency\\B"));
	}

	/**
	 * @test
	 * @throws \Exception
	 * @expectedException \InvalidArgumentException
	 * @expectedExceptionMessage circular dependency error message
	 */
	public function itShouldResolveCircularDependency()
	{
		// todo: actually implement this
		$this->markTestSkipped("Circular dependency detection is not implemented yet.");

		$sut = new Container();
		$sut->register(__DIR__ . "/CircularBean");
	}

	/**
	 * @test
	 * @throws \Exception
	 * @expectedException \InvalidArgumentException
	 * @expectedExceptionMessage Bean name collision: Both 'Lynx\DI\NameCollision\A' and 'Lynx\DI\NameCollision\B' are named 'Bean'.
	 */
	public function itShouldThrowOnBeanNameCollision()
	{
		$sut = new Container();
		$sut->register(__DIR__ . "/NameCollision");
	}
}
