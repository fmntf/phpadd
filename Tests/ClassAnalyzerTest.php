<?php

require_once 'fixtures/sample.classes';
require_once 'fixtures/extension.classes';
require_once 'fixtures/malformed.classes';
require_once 'fixtures/inheritdoc.classes';
require_once 'fixtures/many.classes';

class PHPADD_ClassAnalyzerTest extends PHPUnit_Framework_TestCase
{
	public function setUp()
	{
		$this->nullMethodFilter = new Tests_NullScanFilter;
		$this->filter = new PHPADD_Filter_Visibility();
	}

	public function testAnalyzesAllMethods()
	{
		$parser = new PHPADD_ClassAnalyzer('Example', $this->nullMethodFilter);
		$analysis = $parser->analyze($this->filter);

		$this->assertInstanceOf('PHPADD_Result_Class', $analysis);

		$missing = $analysis->getMissingBlocks();
		$outdated = $analysis->getOutdatedBlocks();

		$this->assertEquals(3, count($missing));
		$this->assertEquals(0, count($outdated));
	}

	public function testIgnoresBlankSpaces()
	{
		$parser = new PHPADD_ClassAnalyzer('ValidWithSpacesExample', $this->nullMethodFilter);
		$analysis = $parser->analyze($this->filter);

		$missing = $analysis->getMissingBlocks();
		$outdated = $analysis->getOutdatedBlocks();

		$this->assertEquals(0, count($missing));
		$this->assertEquals(0, count($outdated));
	}

	public function testAnalyzesOnlyPublicMethods()
	{
		$parser = new PHPADD_ClassAnalyzer('Example', $this->nullMethodFilter);
		$noProtectedFilter = new PHPADD_Filter_Visibility(false, false);
		$analysis = $parser->analyze($noProtectedFilter);

		$missing = $analysis->getMissingBlocks();
		$outdated = $analysis->getOutdatedBlocks();

		$this->assertEquals(1, count($missing));
		$this->assertEquals(0, count($outdated));
		$this->assertEquals('publicMethod', $missing[0]->getName());
	}

	public function testDetectsMissingParametersInDocBlocks()
	{
		$parser = new PHPADD_ClassAnalyzer('InvalidMissingExample', $this->nullMethodFilter);
		$analysis = $parser->analyze($this->filter);

		$missing = $analysis->getMissingBlocks();
		$outdated = $analysis->getOutdatedBlocks();

		$this->assertEquals(0, count($missing));
		$this->assertEquals(1, count($outdated));
		$detail = $outdated[0]->getDetail();

		$this->assertEquals(1, count($detail));
		$this->assertInstanceOf('PHPADD_Result_Mess_MissingParam', $detail[0]);
		$this->assertEquals('$name', $detail[0]->name);
	}

	public function testDetectsMissingParametersInPhp()
	{
		$parser = new PHPADD_ClassAnalyzer('InvalidRemovedExample', $this->nullMethodFilter);
		$analysis = $parser->analyze($this->filter);

		$missing = $analysis->getMissingBlocks();
		$outdated = $analysis->getOutdatedBlocks();

		$this->assertEquals(0, count($missing));
		$this->assertEquals(1, count($outdated));
		$detail = $outdated[0]->getDetail();

		$this->assertEquals(1, count($detail));
		$this->assertInstanceOf('PHPADD_Result_Mess_UnexpectedParam', $detail[0]);
		$this->assertEquals('$name', $detail[0]->name);
	}

	/**
	 * @dataProvider validClasses
	 */
	public function testSkipsValidDocBlocks($className)
	{
		$parser = new PHPADD_ClassAnalyzer($className, $this->nullMethodFilter);
		$analysis = $parser->analyze(new PHPADD_Filter_Visibility(false, false));

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
	public function testFindsInvalidDocBlocks($className)
	{
		$parser = new PHPADD_ClassAnalyzer($className, $this->nullMethodFilter);
		$analysis = $parser->analyze($this->filter);

		$missing = $analysis->getMissingBlocks();
		$outdated = $analysis->getOutdatedBlocks();

		$this->assertEquals(0, count($missing));

		$this->assertEquals(1, count($outdated));
		$this->assertEquals(1, count($outdated[0]->getDetail()));
	}

	public function oneChangeClasses()
	{
		return array(
			array('OneChangeExampleTypeChanged'),
			array('OneChangeExampleRemovedParameter'),
			array('OneChangeExampleAddedParameter'),
		);
	}
	
	public function testFindsInvalidDocBlocks2()
	{
		$parser = new PHPADD_ClassAnalyzer('OneChangeExampleNameChanged', $this->nullMethodFilter);
		$analysis = $parser->analyze($this->filter);

		$missing = $analysis->getMissingBlocks();
		$outdated = $analysis->getOutdatedBlocks();

		$this->assertEquals(0, count($missing));

		$this->assertEquals(1, count($outdated));
		$this->assertEquals(2, count($outdated[0]->getDetail()));
	}

	public function testIgnoresMethodsOfParentClasses()
	{
		$parser = new PHPADD_ClassAnalyzer('Extension_Extended', $this->nullMethodFilter);
		$analysis = $parser->analyze($this->filter);

		$missing = $analysis->getMissingBlocks();
		$outdated = $analysis->getOutdatedBlocks();

		$this->assertEquals(1, count($missing));
		$this->assertEquals(0, count($outdated));
		$this->assertEquals('b', $missing[0]->getName());
	}

	public function testDoesNotExplodeOnMalformedDocblocks()
	{
		$parser = new PHPADD_ClassAnalyzer('MalformedBlocks', $this->nullMethodFilter);
		$analysis = $parser->analyze($this->filter);

		// if no notices are generated, this test passes
	}

	public function testReportsMalformedDocblocks()
	{
		$this->markTestIncomplete('we have to decide how to report them');
	}
	
	public function testSkipsMethodsByName()
	{
		$methodFilter = new PHPADD_Filter_Method(array('Invalid$'));
		$parser = new PHPADD_ClassAnalyzer('ClassWithManyMethods', $methodFilter);
		$analysis = $parser->analyze($this->filter);
		
		$this->assertTrue($analysis->getRegularBlocks() > 0);
		
		foreach ($analysis->getMissingBlocks() as $method) {
			$this->assertMethodNotContains($method, 'Invalid');
		}
		foreach ($analysis->getOutdatedBlocks() as $method) {
			$this->assertMethodNotContains($method, 'Invalid');
		}
	}
	
	public function testAnalyzesInheritdoc()
	{
		$parser = new PHPADD_ClassAnalyzer('Field', $this->nullMethodFilter);
		$analysis = $parser->analyze($this->filter);
		
		$missing = $analysis->getMissingBlocks();
		$outdated = $analysis->getOutdatedBlocks();
		
		$this->assertEquals(0, count($missing));
		$this->assertEquals(0, count($outdated));
	}
	
	public function testAnalyzesInheritdoc2()
	{
		$parser = new PHPADD_ClassAnalyzer('Button', $this->nullMethodFilter);
		$analysis = $parser->analyze($this->filter);
		
		$missing = $analysis->getMissingBlocks();
		$outdated = $analysis->getOutdatedBlocks();
		
		$this->assertEquals(0, count($missing));
		$this->assertEquals(0, count($outdated));
	}
	
	private function assertMethodNotContains($method, $name)
	{
		if (strstr($method->getName(), $name)) {
			$this->fail('We found the method '.$method->getName().' that was not supposed to be scanned.');
		}
	}
	
}
