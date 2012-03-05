<?php

require_once '../PHPADD/ParamsDiff.php';

class PHPADD_ParamsDiffTest extends PHPUnit_Framework_TestCase
{
	public function testNoDifferences()
	{
		$paramsDiff = new PHPADD_ParamsDiff();
		
		$diff = $paramsDiff->diff(array(), array());
		$this->assertEquals(array(), $diff);
		
		$diff = $paramsDiff->diff(array('string $a'), array('string $a'));
		$this->assertEquals(array(), $diff);
	}
	
	public function testMissingParam()
	{
		$paramsDiff = new PHPADD_ParamsDiff();
		
		$php = array(
			'string $test',
			'int $my',
		);
		
		$docblock = array(
			'string $test',
		);
		
		$diff = $paramsDiff->diff($php, $docblock);
		$this->assertEquals(array($this->missing('$my', 'int')), $diff);
	}
	
	public function testUnexpectedParam()
	{
		$paramsDiff = new PHPADD_ParamsDiff();
		
		$php = array(
			'string $test',
		);
		
		$docblock = array(
			'string $test',
			'int $my',
		);
		
		$diff = $paramsDiff->diff($php, $docblock);
		$this->assertEquals(array($this->unexpected('$my', 'int')), $diff);
	}
	
	public function testOrderIsIrrelevant()
	{
		$paramsDiff = new PHPADD_ParamsDiff();
		
		$php = array(
			'int $my',
			'string $test',
		);
		
		$docblock = array(
			'string $test',
			'int $my',
		);
		
		$diff = $paramsDiff->diff($php, $docblock);
		$this->assertEquals(0, count($diff));
	}
	
	public function testDetectsTypeChanges()
	{
		$paramsDiff = new PHPADD_ParamsDiff();
		
		$php = array(
			'My_Model $my',
			'My_Test $test',
		);
		
		$docblock = array(
			'Vendor_Model $my',
			'My_Test $test',
		);
		
		$diff = $paramsDiff->diff($php, $docblock);
		$this->assertEquals(array($this->typechange('$my', 'My_Model', 'Vendor_Model')), $diff);
	}
	
	public function testWontTriggerTypeChangeOnNamespaces()
	{
		$paramsDiff = new PHPADD_ParamsDiff();
		
		$php = array(
			'Doctrine\ORM\EntityManager $my',
			'Doctrine\ORM\Query\Expr $test',
		);
		
		$docblock = array(
			'\Doctrine\ORM\EntityManager $my',
			'Expr $test',
		);
		
		$diff = $paramsDiff->diff($php, $docblock);
		$this->assertEquals(0, count($diff));
	}
	
	private function missing($name, $type)
	{
		return new PHPADD_Result_Mess_MissingParam($name, $type);
	}
	
	private function unexpected($name, $type)
	{
		return new PHPADD_Result_Mess_UnexpectedParam($name, $type);
	}
	
	private function typechange($name, $type, $oldType)
	{
		return new PHPADD_Result_Mess_OutdatedParam($name, $type, $oldType);
	}
}
