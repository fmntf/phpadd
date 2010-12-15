<?php

require_once '../PHPADD/Detector.php';

class DetectorTest extends PHPUnit_Framework_TestCase
{
	public function setUp()
	{
		$this->detector = new Unmasked_Detector();
		$this->detector->setFilter(true, true);
	}

	public function testDelegatesAnalysysToParser()
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
		$results = $mess->getResults();

		foreach ($results as $class => $result) {
			$this->assertTrue($result->isClean());
		}
	}

	public function testWillReportDirtyClasses()
	{
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