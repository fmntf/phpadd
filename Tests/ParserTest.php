<?php

include('../PHPADD/Parser.php');

class ParserTest extends PHPUnit_Framework_TestCase
{
	public function setUp()
	{
		$this->parser = new PHPADD_Parser('Example');
	}

	public function testAnalyzesAllMethods()
	{
		$noProtectedFilter = new PHPADD_Filter();
		$analysys = $this->parser->analyze($noProtectedFilter);
		
		$this->assertEquals(3, count($analysys));
	}

	public function testAnalyzesOnlyPublicMethods()
	{
		$noProtectedFilter = new PHPADD_Filter(true, true);
		$analysys = $this->parser->analyze($noProtectedFilter);
		
		$this->assertEquals(1, count($analysys));
		$this->assertEquals('publicMethod', $analysys[0]['method']);
	}

	public function testSkipsValidDocBlocks()
	{
		$this->parser = new PHPADD_Parser('ValidExample');
		$filter = new PHPADD_Filter();

		$analysys = $this->parser->analyze($filter);
		$this->assertEquals(0, count($analysys));
	}
}

class Example
{
	public function publicMethod() {}
	protected function protectedMethod() {}
	private function privateMethod() {}
}

class ValidExample
{
	/**
	 * Some description here
	 * 
	 * @param stdClass $my
	 * @param string $name
	 * @return string
	 */
	public function validMethod(stdClass $my, $name)
	{
		return 'public';
	}
}