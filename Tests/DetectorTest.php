<?php

class DetectorTest extends PHPUnit_Framework_TestCase
{
	public function setUp()
	{
		$filter = new PHPADD_Filter(true, true);
		$this->detector = new Unmasked_Detector($filter);
	}

	public function testDelegatesanalysisToParser()
	{
		$detector = $this->getMockBuilder('Unmasked_Detector')
						->disableOriginalConstructor()
						->getMock();
		$detector->expects($this->once())
				->method('analyze')
				->with('myClass')
				->will($this->returnValue('delegated'));

		$this->assertEquals('delegated', $detector->analyze('myClass'));
	}

	public function testWillSkipCleanClasses()
	{
		$mess = $this->detector->getMess(__DIR__ . '/fixtures/clean');

		$files = $mess->getFiles();
		$this->assertEquals(1, $mess->getCount());

		$classes = $files[0]->getClasses();
		$validClasses = array('Fixture_ValidExample', 'Fixture_ValidWithSpacesExample');

		foreach ($validClasses as $validClass) {
			$this->assertEquals(0, count($classes[$validClass]->getMethods()));
			$this->assertEquals(0, count($classes[$validClass]->getMissingBlocks()));
			$this->assertEquals(0, count($classes[$validClass]->getOutdatedBlocks()));
			$this->assertEquals(1, count($classes[$validClass]->getRegularBlocks()));

			$this->assertTrue($classes[$validClass]->isClean());
		}
	}

	public function testReportsDirtyClasses()
	{
		$mess = $this->detector->getMess(__DIR__ . '/fixtures/dirty');

		$this->assertEquals(1, $mess->getCount());
		$files = $mess->getFiles();
		$file = $files[0];

		$this->assertEquals(4, $file->getCount());
		$classes = $file->getClasses();

		$missingParam = $classes['Fixture_InvalidMissingExample'];
		$removedParam = $classes['Fixture_InvalidRemovedExample'];
		$multi = $classes['Fixture_InvalidMultiExample'];
		$noblock = $classes['Fixture_NoDocBlock'];

		// nothing regular
		$this->assertEquals(0, $missingParam->getRegularBlocks());
		$this->assertEquals(0, $removedParam->getRegularBlocks());
		$this->assertEquals(0, $multi->getRegularBlocks());
		$this->assertEquals(0, $noblock->getRegularBlocks());

		// only last class miss docblock
		$this->assertEquals(0, count($missingParam->getMissingBlocks()));
		$this->assertEquals(0, count($removedParam->getMissingBlocks()));
		$this->assertEquals(0, count($multi->getMissingBlocks()));
		$this->assertEquals(1, count($noblock->getMissingBlocks()));

		//just one warning
		$this->assertEquals(1, count($missingParam->getOutdatedBlocks()));
		$this->assertEquals(1, count($removedParam->getOutdatedBlocks()));
		$this->assertEquals(1, count($multi->getOutdatedBlocks()));
		$this->assertEquals(0, count($noblock->getOutdatedBlocks()));
	}

	public function testReportsMissingParams()
	{
		$mess = $this->detector->getMess(__DIR__ . '/fixtures/dirty');

		$files = $mess->getFiles();
		$classes = $files[0]->getClasses();
		$outdates = $classes['Fixture_InvalidMissingExample']->getOutdatedBlocks();

		$this->assertEquals('invalidMethod', $outdates[0]->getName());
		$detail = $outdates[0]->getDetail();
		$expectedDetail = array('type' => 'missing-param', 'name' => '$name');
		$this->assertEquals(1, count($detail));
		$this->assertEquals($expectedDetail, $detail[0]);
	}

	public function testReportsUnexpectedParams()
	{
		$mess = $this->detector->getMess(__DIR__ . '/fixtures/dirty');

		$files = $mess->getFiles();
		$classes = $files[0]->getClasses();
		$outdates = $classes['Fixture_InvalidRemovedExample']->getOutdatedBlocks();

		$this->assertEquals('invalidMethod', $outdates[0]->getName());
		$detail = $outdates[0]->getDetail();
		$expectedDetail = array('type' => 'unexpected-param', 'name' => '$name');
		$this->assertEquals(1, count($detail));
		$this->assertEquals($expectedDetail, $detail[0]);
	}
}

class Unmasked_Detector extends PHPADD_Detector
{
	public function analyze($param)
	{
		return parent::analyze($param);
	}
}