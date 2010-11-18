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
		$analysys = $parser->analyze($this->filter);

		$this->assertEquals(3, count($analysys));
	}

	public function testIgnoresBlankSpaces()
	{
		$parser = new PHPADD_Parser('ValidWithSpacesExample');
		$analysys = $parser->analyze($this->filter);

		$this->assertEquals(0, count($analysys));
	}

	public function testAnalyzesOnlyPublicMethods()
	{
		$parser = new PHPADD_Parser('Example');
		$noProtectedFilter = new PHPADD_Filter(true, true);
		$analysys = $parser->analyze($noProtectedFilter);

		$this->assertEquals(1, count($analysys));
		$this->assertEquals('publicMethod', $analysys[0]['method']);
	}

	public function testDetectsMissingParametersInDocBlocks()
	{
		$parser = new PHPADD_Parser('InvalidMissingExample');
		$analysys = $parser->analyze($this->filter);

		$this->assertEquals(1, count($analysys));
		$this->assertEquals('missing-param', $analysys[0]['detail'][0]['type']);
		$this->assertEquals('$name', $analysys[0]['detail'][0]['name']);
	}

	public function testDetectsMissingParametersInPhp()
	{
		$parser = new PHPADD_Parser('InvalidRemovedExample');
		$analysys = $parser->analyze($this->filter);

		$this->assertEquals(1, count($analysys));
		$this->assertEquals('unexpected-param', $analysys[0]['detail'][0]['type']);
		$this->assertEquals('$name', $analysys[0]['detail'][0]['name']);
	}

	/**
	 * @dataProvider validClasses
	 */
	public function testSkipsValidDocBlocks($className)
	{
		$parser = new PHPADD_Parser($className);
		$analysys = $parser->analyze(new PHPADD_Filter(true, true));

		$this->assertEquals(0, count($analysys));
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
		$analysys = $parser->analyze($this->filter);

		$count = array(
			'changed' => 2,
			'removed' => 1,
			'added' => 1,
		);

		$this->assertEquals($count[$error], count($analysys[0]['detail']));
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
		$analysys = $parser->analyze($this->filter);

		$this->assertEquals(1, count($analysys));
		$this->assertEquals('b', $analysys[0]['method']);
	}

}
