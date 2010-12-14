<?php

require_once '../PHPADD/Parser.php';
require_once 'fixtures/sample.classes';
require_once 'fixtures/extension.classes';

class ParserTest extends PHPUnit_Framework_TestCase
{
	public function setUp()
	{
		$this->filter = new PHPADD_Filter();
	}

	public function testAnalyzesAllMethods()
	{
		$parser = new PHPADD_Parser('Example');
		$analysis = $parser->analyze($this->filter);

		$this->assertEquals(3, count($analysis));
	}

	public function testIgnoresBlankSpaces()
	{
		$parser = new PHPADD_Parser('ValidWithSpacesExample');
		$analysis = $parser->analyze($this->filter);

		$this->assertEquals(0, count($analysis));
	}

	public function testAnalyzesOnlyPublicMethods()
	{
		$parser = new PHPADD_Parser('Example');
		$noProtectedFilter = new PHPADD_Filter(true, true);
		$analysis = $parser->analyze($noProtectedFilter);

		$this->assertEquals(1, count($analysis));
		$this->assertEquals('publicMethod', $analysis[0]['method']);
	}

	public function testDetectsMissingParametersInDocBlocks()
	{
		$parser = new PHPADD_Parser('InvalidMissingExample');
		$analysis = $parser->analyze($this->filter);

		$this->assertEquals(1, count($analysis));
		$this->assertEquals('missing-param', $analysis[0]['detail'][0]['type']);
		$this->assertEquals('$name', $analysis[0]['detail'][0]['name']);
	}

	public function testDetectsMissingParametersInPhp()
	{
		$parser = new PHPADD_Parser('InvalidRemovedExample');
		$analysis = $parser->analyze($this->filter);

		$this->assertEquals(1, count($analysis));
		$this->assertEquals('unexpected-param', $analysis[0]['detail'][0]['type']);
		$this->assertEquals('$name', $analysis[0]['detail'][0]['name']);
	}

	/**
	 * @dataProvider validClasses
	 */
	public function testSkipsValidDocBlocks($className)
	{
		$parser = new PHPADD_Parser($className);
		$analysis = $parser->analyze(new PHPADD_Filter(true, true));

		$this->assertEquals(0, count($analysis));
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
		$parser = new PHPADD_Parser($className);
		$analysis = $parser->analyze($this->filter);

		$count = array(
			'changed' => 2,
			'removed' => 1,
			'added' => 1,
		);

		$this->assertEquals($count[$error], count($analysis[0]['detail']));
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
		$parser = new PHPADD_Parser('Extension_Extended');
		$analysis = $parser->analyze($this->filter);

		$this->assertEquals(1, count($analysis));
		$this->assertEquals('b', $analysis[0]['method']);
	}

}
