<?php

include('../PHPADD/Parser.php');

class ParserTest extends PHPUnit_Framework_TestCase
{
	public function setUp()
	{
		$this->parser = new PHPADD_Parser('Example');
	}

	public function testAnalyzesByLevel()
	{
		$noProtectedFilter = new PHPADD_Filter(false, true);

		$analysys = $this->parser->analyze($noProtectedFilter);
		$this->assertEquals(2, count($analysys));
		$this->assertEquals('publicMethod', $analysys[0]['method']);
		$this->assertEquals('privateMethod', $analysys[1]['method']);
	}

	public function testAnalyzesOnlyPublicMethods()
	{
		$noProtectedFilter = new PHPADD_Filter(false, false);

		$analysys = $this->parser->analyze($noProtectedFilter);
		$this->assertEquals(1, count($analysys));
		$this->assertEquals('publicMethod', $analysys[0]['method']);
	}
}

class Example
{
	public function publicMethod() {}
	protected function protectedMethod() {}
	private function privateMethod() {}
}