<?php

require_once '../PHPADD/Detector.php';

class ExcludefilterTest extends PHPUnit_Framework_TestCase
{
	// Simple data provider where it will provide 2 arrays at the time.
	// Array 1 is the exclude array, array 2 is an array with
	// path values that will be matched against the excludes. When it
	// matches, it should return false, otherwise true.
	public static function provider() {
		return array (
			array(
				array(),	// Filter
				array(		// Input / output from filter
					"fixtures/file1.php" => true,
					"fixtures/dirty/file1.php" => true,
					"fixtures/dirty/file2.php" => true,
					"fixtures/clean/file1.php" => true,
					"fixtures/dirty/simple/file1.php" => true,
					"fixtures/dirty/simple/file2.php" => true,
					"fixtures/dirty/basic/file1.php" => true,
					"fixtures/dirty/basic/file2.php" => true,
				)
			),
			array(
				array("*/dirty/*"),	// Filter
				array(		// Input / output from filter
					"fixtures/file1.php" => true,
					"fixtures/dirty/file1.php" => false,
					"fixtures/dirty/file2.php" => false,
					"fixtures/clean/file1.php" => true,
					"fixtures/dirty/simple/file1.php" => false,
					"fixtures/dirty/simple/file2.php" => false,
					"fixtures/dirty/basic/file1.php" => false,
					"fixtures/dirty/basic/file2.php" => false,
				)
			),
			array(
				array("*/simple/*"),	// Filter
				array(					// Input / output from filter
					"fixtures/file1.php" => true,
					"fixtures/dirty/file1.php" => true,
					"fixtures/dirty/file2.php" => true,
					"fixtures/clean/file1.php" => true,
					"fixtures/dirty/simple/file1.php" => false,
					"fixtures/dirty/simple/file2.php" => false,
					"fixtures/dirty/basic/file1.php" => true,
					"fixtures/dirty/basic/file2.php" => true,
				)
			),
			array(
				array("*/simple/*", "*/dirty/*"),	// Filter
				array(								// Input / output from filter
					"fixtures/file1.php" => true,
					"fixtures/dirty/file1.php" => false,
					"fixtures/dirty/file2.php" => false,
					"fixtures/clean/file1.php" => true,
					"fixtures/dirty/simple/file1.php" => false,
					"fixtures/dirty/simple/file2.php" => false,
					"fixtures/dirty/basic/file1.php" => false,
					"fixtures/dirty/basic/file2.php" => false,
				)
			),
			array(
				array("*"),	// Filter
				array(		// Input / output from filter
					"fixtures/file1.php" => false,
					"fixtures/dirty/file1.php" => false,
					"fixtures/dirty/file2.php" => false,
					"fixtures/clean/file1.php" => false,
					"fixtures/dirty/simple/file1.php" => false,
					"fixtures/dirty/simple/file2.php" => false,
					"fixtures/dirty/basic/file1.php" => false,
					"fixtures/dirty/basic/file2.php" => false,
				)
			),
		);
	}

	/**
	 * @dataProvider provider
	 */
	public function testFilter($excludes, $data)
	{
		$filter = new Mock_Excludefilter();
		$filter->setExcludes($excludes);

		foreach ($data as $path => $result) {
			$filter->setCurrent($path);
			$this->assertEquals($filter->accept(), $result);
		}
	}

}

class Mock_Excludefilter extends PHPAdd_Excludefilter {
	function __construct() {
	}

	function setCurrent($current) {
		$this->_current = $current;
	}

	function current() {
		return $this->_current;
	}
}