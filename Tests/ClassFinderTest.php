<?php

class PHPADD_ClassFinderTest extends PHPUnit_Framework_TestCase
{
	const PATH = 'fixtures/classfinder/';
	
	public function testGetsAListOfClassesInADirectory()
	{
		$nullFilter = new Tests_NullScanFilter;
		$classFinder = new PHPADD_ClassFinder(self::PATH, $nullFilter, $nullFilter);
		
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
		$dirFilter = new PHPADD_Filter_Directory(array('foo'));
		$nullFilter = new Tests_NullScanFilter;
		$classFinder = new PHPADD_ClassFinder(self::PATH, $dirFilter, $nullFilter);
		
		$expected = array(
			self::PATH.'bar.php' => array('classfinder_bar'),
			self::PATH.'bar/double.php' => array('classfinder_bar_doubleA', 'classfinder_bar_doubleB'),
		);
		
		$files = $classFinder->getList();
		$this->assertOnFiles($expected, $files);
	}
	
	public function testSkipsOnlyBarDirectory()
	{
		$dirFilter = new PHPADD_Filter_Directory(array('/bar/'));
		$nullFilter = new Tests_NullScanFilter;
		$classFinder = new PHPADD_ClassFinder(self::PATH, $dirFilter, $nullFilter);
		
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
	
	public function testSkipsClassesContainingDouble()
	{
		$nullFilter = new Tests_NullScanFilter;
		$classFilter = new PHPADD_Filter_Class(array('doubleA'));
		$classFinder = new PHPADD_ClassFinder(self::PATH, $nullFilter, $classFilter);
		
		$expected = array(
			self::PATH.'foo.php' => array('classfinder_foo'),
			self::PATH.'bar.php' => array('classfinder_bar'),
			self::PATH.'foobar.php' => array('classfinder_foobar'),
			self::PATH.'foo/foo.php' => array('classfinder_foo_foo'),
			self::PATH.'foo/double.php' => array('classfinder_foo_doubleB'),
			self::PATH.'bar/foo.php' => array('classfinder_bar_foo'),
			self::PATH.'bar/double.php' => array('classfinder_bar_doubleB'),
			self::PATH.'foobar/foo.php' => array('classfinder_foobar_foo'),
			self::PATH.'foobar/double.php' => array('classfinder_foobar_doubleB'),
		);
		
		$files = $classFinder->getList();
		$this->assertOnFiles($expected, $files);
	}

	public function testManagesNamespaces()
	{
		$nullFilter = new Tests_NullScanFilter;
		$classFinder = new PHPADD_ClassFinder('fixtures/namespaces', $nullFilter, $nullFilter);
		
		$expected = array(
			'fixtures/namespaces/Commerce/Product.php' => array('\My\Commerce\Product'),
			'fixtures/namespaces/Blog/Post.php' => array('\My\Blog\Post'),
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