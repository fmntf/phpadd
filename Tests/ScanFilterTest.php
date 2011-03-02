<?php

class ScanFilterTest extends PHPUnit_Framework_TestCase
{
	/**
	 * @dataProvider getDirectories
	 */
	public function testSkipsDirectories($directory, $result)
	{
		// Foo is the regular expression to discard
		$filter = new PHPADD_ScanFilter(array('Foo'), array(), array());
		
		$this->assertSame($result, $filter->keepsDirectory($directory));
	}
	
	/**
	 * @dataProvider getClasses
	 */
	public function testSkipsClasses($class, $result)
	{
		// Foo is the regular expression to discard
		$filter = new PHPADD_ScanFilter(array(), array('Foo'), array());
		
		$this->assertSame($result, $filter->keepsClass($class));
	}
	
	/**
	 * @dataProvider getMethods
	 */
	public function testSkipsMethods($method, $result)
	{
		// Foo is the regular expression to discard
		$filter = new PHPADD_ScanFilter(array(), array(), array('Foo'));
		
		$this->assertSame($result, $filter->keepsMethod($method));
	}
	
	public function getDirectories()
	{
		return array(
			array('Foo',		false),
			array('FooBar',		false),
			array('Bar',		true),
			array('Foo/Bar',	false),
			array('Baz/Bar',	true)
		);
	}
	
	public function getClasses()
	{
		return array(
			array('Foo',				false),
			array('Zend_Application',	true),
			array('FooBar',				false)
		);
	}
	
	public function getMethods()
	{
		return array(
			array('Foo',				false),
			array('__construct',		true),
			array('__constructFoo',		false)
		);
	}
}