<?php

require_once '../PHPADD/Parser.php';
require_once 'fixtures/sample.classes';

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
	}

	public function testDetectsMissingParametersInPhp()
	{
		$parser = new PHPADD_Parser('InvalidRemovedExample');
		$analysys = $parser->analyze($this->filter);

		$this->assertEquals(1, count($analysys));
		$this->assertEquals('unexpected-param', $analysys[0]['detail'][0]['type']);
	}

	/**
	 * @dataProvider validClasses
	 */
	public function testSkipsValidDocBlocks($className)
	{
		$parser = new PHPADD_Parser($className);
		$analysys = $parser->analyze($this->filter);

		$this->assertEquals(0, count($analysys));
	}
	
	public function validClasses()
	{
		return array(
			array('ValidExample'),
			array('ComplexExample'),
		);
	}

	/**
	 * @dataProvider oneChangeClasses
	 */
	public function testFindsInvalidDocBlocks($className, $error)
	{
		$parser = new PHPADD_Parser($className);
		$analysys = $parser->analyze($this->filter);

		$this->assertEquals(1, count($analysys));
		$this->assertEquals($error, $analysys[0]['detail'][0]['type']);
	}

	public function oneChangeClasses()
	{
		return array(
			array('OneChangeExampleTypeChanged', 'type-mismatch'),
			// won't fix in 1.0
//			array('OneChangeExampleRemovedParameter', 'unexpected-param'),
//			array('OneChangeExampleAddedParameter', 'missing-param'),
		);
	}

}
