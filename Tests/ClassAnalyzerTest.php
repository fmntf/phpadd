<?php

require_once 'fixtures/sample.classes';
require_once 'fixtures/extension.classes';
require_once 'fixtures/malformed.classes';

class ParserTest extends PHPUnit_Framework_TestCase
{
	public function setUp()
	{
		$this->filter = new PHPADD_Filter();
	}

	public function testAnalyzesAllMethods()
	{
		$parser = new PHPADD_ClassAnalyzer('Example');
		$analysis = $parser->analyze($this->filter);

		$this->assertInstanceOf('PHPADD_Result_Class', $analysis);

		$missing = $analysis->getMissingBlocks();
		$outdated = $analysis->getOutdatedBlocks();

		$this->assertEquals(3, count($missing));
		$this->assertEquals(0, count($outdated));
	}

	public function testIgnoresBlankSpaces()
	{
		$parser = new PHPADD_ClassAnalyzer('ValidWithSpacesExample');
		$analysis = $parser->analyze($this->filter);

		$missing = $analysis->getMissingBlocks();
		$outdated = $analysis->getOutdatedBlocks();

		$this->assertEquals(0, count($missing));
		$this->assertEquals(0, count($outdated));
	}

	public function testAnalyzesOnlyPublicMethods()
	{
		$parser = new PHPADD_ClassAnalyzer('Example');
		$noProtectedFilter = new PHPADD_Filter(false, false);
		$analysis = $parser->analyze($noProtectedFilter);

		$missing = $analysis->getMissingBlocks();
		$outdated = $analysis->getOutdatedBlocks();

		$this->assertEquals(1, count($missing));
		$this->assertEquals(0, count($outdated));
		$this->assertEquals('publicMethod', $missing[0]->getName());
	}

	public function testDetectsMissingParametersInDocBlocks()
	{
		$parser = new PHPADD_ClassAnalyzer('InvalidMissingExample');
		$analysis = $parser->analyze($this->filter);

		$missing = $analysis->getMissingBlocks();
		$outdated = $analysis->getOutdatedBlocks();

		$this->assertEquals(0, count($missing));
		$this->assertEquals(1, count($outdated));
		$detail = $outdated[0]->getDetail();

		$this->assertEquals(1, count($detail));
		$this->assertEquals('missing-param', $detail[0]['type']);
		$this->assertEquals('$name', $detail[0]['name']);
	}

	public function testDetectsMissingParametersInPhp()
	{
		$parser = new PHPADD_ClassAnalyzer('InvalidRemovedExample');
		$analysis = $parser->analyze($this->filter);

		$missing = $analysis->getMissingBlocks();
		$outdated = $analysis->getOutdatedBlocks();

		$this->assertEquals(0, count($missing));
		$this->assertEquals(1, count($outdated));
		$detail = $outdated[0]->getDetail();

		$this->assertEquals(1, count($detail));
		$this->assertEquals('unexpected-param', $detail[0]['type']);
		$this->assertEquals('$name', $detail[0]['name']);
	}

	/**
	 * @dataProvider validClasses
	 */
	public function testSkipsValidDocBlocks($className)
	{
		$parser = new PHPADD_ClassAnalyzer($className);
		$analysis = $parser->analyze(new PHPADD_Filter(false, false));

		$missing = $analysis->getMissingBlocks();
		$outdated = $analysis->getOutdatedBlocks();

		$this->assertEquals(0, count($missing));
		$this->assertEquals(0, count($outdated));
	}

	public function validClasses()
	{
		return array(
			array('ValidExample'),
			array('ValidComplexExample'),
			array('ValidOnlyPublicExample'),
		);
	}

	/**
	 * @dataProvider oneChangeClasses
	 */
	public function testFindsInvalidDocBlocks($className, $error)
	{
		$parser = new PHPADD_ClassAnalyzer($className);
		$analysis = $parser->analyze($this->filter);

		$missing = $analysis->getMissingBlocks();
		$outdated = $analysis->getOutdatedBlocks();

		$this->assertEquals(0, count($missing));

		switch ($error) {
			case 'changed':
				$this->assertEquals(1, count($outdated));
				$this->assertEquals(2, count($outdated[0]->getDetail()));
				break;
			case 'removed':
				$this->assertEquals(1, count($outdated));
				$this->assertEquals(1, count($outdated[0]->getDetail()));
				break;
			case 'added':
				$this->assertEquals(1, count($outdated));
				$this->assertEquals(1, count($outdated[0]->getDetail()));
				break;
		}
	}

	public function oneChangeClasses()
	{
		return array(
			array('OneChangeExampleTypeChanged', 'changed'),
			array('OneChangeExampleNameChanged', 'changed'),
			array('OneChangeExampleRemovedParameter', 'removed'),
			array('OneChangeExampleAddedParameter', 'added'),
		);
	}

	public function testIgnoresMethodsOfParentClasses()
	{
		$parser = new PHPADD_ClassAnalyzer('Extension_Extended');
		$analysis = $parser->analyze($this->filter);

		$missing = $analysis->getMissingBlocks();
		$outdated = $analysis->getOutdatedBlocks();

		$this->assertEquals(1, count($missing));
		$this->assertEquals(0, count($outdated));
		$this->assertEquals('b', $missing[0]->getName());
	}


	public function testDoesNotExplodeOnMalformedDocblocks()
	{
		$parser = new PHPADD_ClassAnalyzer('MalformedBlocks');
		$analysis = $parser->analyze($this->filter);

		// if no notices are generated, this test passes
	}

	public function testReportsMalformedDocblocks()
	{
		$this->markTestIncomplete('we have to decide how to report them');
	}

}