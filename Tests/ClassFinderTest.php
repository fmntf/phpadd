<?php

class ClassFinderTest extends PHPUnit_Framework_TestCase
{
	const PATH = 'fixtures/classfinder/';
	
	public function testGetsAListOfClassesInADirectory()
	{
		$scanFilter = new Tests_NullScanFilter;
		$classFinder = new PHPADD_ClassFinder(self::PATH, $scanFilter);
		
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
		
		$files = $classFinder->getList();
		$this->assertOnFiles($expected, $files);
	}
	
	public function testSkipsPathsContainingFoo()
	{
		$scanFilter = new PHPADD_Filter_Directory(array('foo'));
		$classFinder = new PHPADD_ClassFinder(self::PATH, $scanFilter);
		
		$expected = array(
			self::PATH.'bar.php' => array('classfinder_bar'),
			self::PATH.'bar/double.php' => array('classfinder_bar_doubleA', 'classfinder_bar_doubleB'),
		);
		
		$files = $classFinder->getList();
		$this->assertOnFiles($expected, $files);
	}
	
	public function testSkipsOnlyBarDirectory()
	{
		$scanFilter = new PHPADD_Filter_Directory(array('/bar/'));
		$classFinder = new PHPADD_ClassFinder(self::PATH, $scanFilter);
		
		$expected = array(
			self::PATH.'foo.php' => array('classfinder_foo'),
			self::PATH.'bar.php' => array('classfinder_bar'),
			self::PATH.'foobar.php' => array('classfinder_foobar'),
			self::PATH.'foo/foo.php' => array('classfinder_foo_foo'),
			self::PATH.'foo/double.php' => array('classfinder_foo_doubleA', 'classfinder_foo_doubleB'),
			self::PATH.'foobar/foo.php' => array('classfinder_foobar_foo'),
			self::PATH.'foobar/double.php' => array('classfinder_foobar_doubleA', 'classfinder_foobar_doubleB'),
		);
		
		$files = $classFinder->getList();
		$this->assertOnFiles($expected, $files);
	}
	
	private function assertOnFiles(array $expected, array $files)
	{
		$this->assertEquals(count($expected), count($files));
		
		foreach ($files as $foundFile => $foundClasses) {
			$this->assertArrayHasKey($foundFile, $expected);
			$this->assertEquals($expected[$foundFile], $foundClasses);
		}
	}
}