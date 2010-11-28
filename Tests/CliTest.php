<?php

require_once '../PHPADD/Cli.php';

class CliTest extends PHPUnit_Framework_TestCase
{
	public function setUp()
	{
		$this->cli = new Unmasked_PHPADD_Cli;
	}

	public function testScansEverythingByDefault()
	{
		$this->assertFalse($this->cli->blocksPrivate());
		$this->assertFalse($this->cli->blocksProtected());
	}
}

class Unmasked_PHPADD_Cli extends PHPADD_Cli
{
	public function blocksPrivate()
	{
		return parent::blocksPrivate();
	}
	public function blocksProtected()
	{
		return parent::blocksProtected();
	}
}
