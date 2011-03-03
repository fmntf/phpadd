<?php

class ClassFinderTest extends PHPUnit_Framework_TestCase
{
	const PATH = 'fixtures/classfinder/';
	
	public function testGetsAListOfClassesInADirectory()
	{
		$scanFilter = new Tests_NullScanFilter;
		$classFinder = new PHPADD_ClassFinder(self::PATH, $scanFilter);
		
		$files = $classFinder->getList();
		$this->assertEquals(9, count($files));
		
		$expected = array(
			self::PATH.'foo.php' => array('classfinder_foo'),
			self::PATH.'bar.php' => array('classfinder_bar'),
			self::PATH.'foobar.php' => array('classfinder_foobar'),
			self::PATH.'foo/foo.php' => array('classfinder_foo_foo'),
			self::PATH.'foo/double.php' => array('classfinder_foo_doubleA', 'classfinder_foo_doubleB'),
			self::PATH.'bar/foo.php' => array('classfinder_bar_foo'),
			self::PATH.'bar/double.php' => array('classfinder_bar_doubleA', 'classfinder_bar_doubleB'),
			self::PATH.'foobar/foo.php' => array('classfinder_foobar_foo'),
			self::PATH.'foobar/double.php' => array('classfinder_foobar_doubleA', 'classfinder_foobar_doubleB'),
		);
		
		foreach ($files as $foundFile => $foundClasses) {
			$this->assertArrayHasKey($foundFile, $expected);
			$this->assertEquals($expected[$foundFile], $foundClasses);
		}
	}
}