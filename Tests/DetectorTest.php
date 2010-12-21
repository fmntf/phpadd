<?php

require_once '../PHPADD/Detector.php';

class DetectorTest extends PHPUnit_Framework_TestCase
{
	public function setUp()
	{
		$filter = new PHPADD_Filter(true, true);
		$this->detector = new Unmasked_Detector($filter);
	}

	public function testDelegatesanalysisToParser()
	{
		$this->markTestSkipped('This test needs to be recreated');
		
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
		$files = $mess->getFiles();
		$this->assertEquals(1, $mess->getCount());

		$classes = $files[0]->getClasses();
		$this->assertEquals(1, $mess->getCount());

		$this->assertEquals(0, count($classes[0]->getMethods()));
		$this->assertEquals(0, count($classes[0]->getMissingBlocks()));
		$this->assertEquals(0, count($classes[0]->getOutdatedBlocks()));
		$this->assertEquals(1, count($classes[0]->getRegularBlocks()));

		$this->assertTrue($classes[0]->isClean());
	}

	public function testWillReportDirtyClasses()
	{
		$this->markTestSkipped('This test needs to be recreated');
		
		$mess = $this->detector->getMess(__DIR__ . '/fixtures/dirty');
		$results = $mess->getResults();

		$missingParam = $results['Fixture_InvalidMissingExample'];
		$removedParam = $results['Fixture_InvalidRemovedExample'];

		// nothing regular
		$this->assertEquals(0, $missingParam->getRegularBlocks());
		$this->assertEquals(0, $removedParam->getRegularBlocks());

		// nothing without docblock
		$this->assertEquals(0, count($missingParam->getMissingBlocks()));
		$this->assertEquals(0, count($removedParam->getMissingBlocks()));

		//just one warning
		$this->assertEquals(1, count($missingParam->getOutdatedBlocks()));
		$this->assertEquals(1, count($removedParam->getOutdatedBlocks()));

		$missingParamOutdates = $missingParam->getOutdatedBlocks();
		$removedParamOutdates = $removedParam->getOutdatedBlocks();

		$this->assertEquals(1, count($missingParamOutdates));
		$this->assertEquals(1, count($removedParamOutdates));

		$missingParamDetail = $missingParamOutdates[0]->getDetail();
		$removedParamDetail = $removedParamOutdates[0]->getDetail();

		$this->assertEquals(1, count($missingParamOutdates));
		$this->assertEquals(1, count($removedParamOutdates));

		$this->assertEquals('missing-param', $missingParamDetail[0]['type']);
		$this->assertEquals('unexpected-param', $removedParamDetail[0]['type']);
	}
}

class Unmasked_Detector extends PHPADD_Detector
{
	public function analyze($param)
	{
		return parent::analyze($param);
	}
}