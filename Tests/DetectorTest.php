<?php

require_once '../PHPADD/Detector.php';

class DetectorTest extends PHPUnit_Framework_TestCase
{
	public function setUp()
	{
		$this->markTestIncomplete();
		$this->detector = new Unmasked_Detector;
	}

	public function testScansEverythingByDefault()
	{
		$level = $this->detector->getScanLevel();

		$this->assertTrue($level['access']['protected']);
		$this->assertTrue($level['access']['private']);

		$expected = ReflectionMethod::IS_PUBLIC | ReflectionMethod::IS_PROTECTED | ReflectionMethod::IS_PRIVATE;
		$this->assertEquals($expected, $level['scalar']);
	}

	public function testMayPreventProtectedScanning()
	{
		$this->detector->preventProtectedScanning();
		$level = $this->detector->getScanLevel();

		$this->assertFalse($level['access']['protected']);
		$this->assertTrue($level['access']['private']);

		$expected = ReflectionMethod::IS_PUBLIC | ReflectionMethod::IS_PRIVATE;
		$this->assertEquals($expected, $level['scalar']);
	}

	public function testMayPreventPrivateScanning()
	{
		$this->detector->preventPrivateScanning();
		$level = $this->detector->getScanLevel();

		$this->assertTrue($level['access']['protected']);
		$this->assertFalse($level['access']['private']);

		$expected = ReflectionMethod::IS_PUBLIC | ReflectionMethod::IS_PROTECTED;
		$this->assertEquals($expected, $level['scalar']);
	}

	public function testDelegatesAnalysysToParser()
	{
		$detector = $this->getMockBuilder('PHPADD_Parser')
						->disableOriginalConstructor()
						->getMock();
		$detector->expects($this->once())
				->method('analyze')
				->with(23)
				->will($this->returnValue('delegated'));

		$this->assertEquals('delegated', $detector->analyze(23));
	}
}

class Unmasked_Detector extends PHPADD_Detector
{
//	public function getScanLevel()
//	{
//		return parent::getScanLevel();
//	}
	public function analyze($param)
	{
		return parent::analyze($param);
	}
}