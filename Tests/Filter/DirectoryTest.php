<?php

class Filter_DirectoryTest extends PHPUnit_Framework_TestCase
{
	/**
	 * @dataProvider getDirectories
	 */
	public function testSkipsDirectories($directory, $result)
	{
		// Foo is the regular expression to discard
		$filter = new PHPADD_Filter_Directory(array('Foo'));
		
		$this->assertSame($result, $filter->isFiltered($directory));
	}
	
	public function getDirectories()
	{
		return array(
			array('Foo',		true),
			array('FooBar',		true),
			array('Bar',		false),
			array('Foo/Bar',	true),
			array('Baz/Bar',	false)
		);
	}
	
	public function testSkipsPathsWithSlashes()
	{
		// Foo is the regular expression to discard
		$filter = new PHPADD_Filter_Directory(array('/etc/php5/'));
		
		$this->assertTrue($filter->isFiltered('/etc/php5/info.php'));
	}
}