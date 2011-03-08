<?php

class Filter_ClassTest extends PHPUnit_Framework_TestCase
{
	/**
	 * @dataProvider getClasses
	 */
	public function testSkipsClasses($class, $result)
	{
		// Foo is the regular expression to discard
		$filter = new PHPADD_Filter_Class(array('Foo'));
		
		$this->assertSame($result, $filter->isFiltered($class));
	}
	
	public function getClasses()
	{
		return array(
			array('Foo',				true),
			array('Zend_Application',	false),
			array('FooBar',				true)
		);
	}
}