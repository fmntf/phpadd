<?php

class FilterFactoryTest extends PHPUnit_Framework_TestCase
{
	public function setUp()
	{
		$this->filter = new PHPADD_FilterFactory(
			array('funnyLlama'), array('fuzzyLLama'), array('llamaLlamaDuck')
		);
	}
	
	public function testBuildsDirectoryFilter()
	{
		$filter = $this->filter->getDirectoryFilter();
		
		$this->assertInstanceOf('PHPADD_Filterable', $filter);
		$this->assertTrue($filter->isFiltered('funnyLlama'));
	}
	
	public function testBuildsClassFilter()
	{
		$filter = $this->filter->getClassFilter();
		
		$this->assertInstanceOf('PHPADD_Filterable', $filter);
		$this->assertTrue($filter->isFiltered('fuzzyLLama'));
	}
	
	public function testBuildsMethodFilter()
	{
		$filter = $this->filter->getMethodFilter();
		
		$this->assertInstanceOf('PHPADD_Filterable', $filter);
		$this->assertTrue($filter->isFiltered('llamaLlamaDuck'));
	}
}