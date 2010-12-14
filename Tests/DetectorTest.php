<?php

require_once '../PHPADD/Detector.php';

class DetectorTest extends PHPUnit_Framework_TestCase
{
	public function setUp()
	{
		$this->detector = new Unmasked_Detector();
		$this->detector->setFilter(true, true);
	}

	public function testDelegatesanalysisToParser()
	{
		$filter = new PHPADD_Filter();
		$detector = $this->getMockBuilder('PHPADD_Parser')
						->disableOriginalConstructor()
						->getMock();
		$detector->expects($this->once())
				->method('analyze')
				->with($filter)
				->will($this->returnValue('delegated'));

		$this->assertEquals('delegated', $detector->analyze($filter));
	}

	public function testWillSkipCleanClasses()
	{
		$mess = $this->detector->getMess(__DIR__ . '/fixtures/clean');

		$this->assertEquals(0, count($mess));
	}

	public function testWillReportDirtyClasses()
	{
		$mess = $this->detector->getMess(__DIR__ . '/fixtures/dirty');

		$fileMess = $mess[__DIR__ . '/fixtures/dirty/simple.php'];

		$this->assertEquals(2, count($fileMess));
		$this->assertEquals('missing-param', $fileMess['Fixture_InvalidMissingExample'][0]['detail'][0]['type']);
		$this->assertEquals('unexpected-param', $fileMess['Fixture_InvalidRemovedExample'][0]['detail'][0]['type']);
	}
}

class Unmasked_Detector extends PHPADD_Detector
{
	public function analyze($param)
	{
		return parent::analyze($param);
	}
}