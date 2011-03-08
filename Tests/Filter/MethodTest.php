<?php

class Filter_MethodTest extends PHPUnit_Framework_TestCase
{
	/**
	 * @dataProvider getMethods
	 */
	public function testSkipsMethods($method, $result)
	{
		// Foo is the regular expression to discard
		$filter = new PHPADD_Filter_Method(array('Foo'));
		
		$this->assertSame($result, $filter->isFiltered($method));
	}
	
	public function getMethods()
	{
		return array(
			array('Foo',				true),
			array('__construct',		false),
			array('__constructFoo',		true)
		);
	}
}
