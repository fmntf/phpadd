<?php

include('../PHPADD/Cli.php');

class CliTest extends PHPUnit_Framework_TestCase
{
	public function setUp()
	{
		$this->cli = new PHPADD_Cli;
	}

	public function testScansEverythingByDefault()
	{
		$this->assertFalse($this->cli->blocksPrivate());
		$this->assertFalse($this->cli->blocksProtected());
	}

	public function testBlockPrivatesByParam()
	{
		$_SERVER['argv'] = array(
			'script', '--anyparam', '--skip-private', '--any-other-param'
		);

		$this->assertTrue($this->cli->blocksPrivate());
		$this->assertFalse($this->cli->blocksProtected());
	}

	public function testBlockProtectedByParam()
	{
		$_SERVER['argv'] = array(
			'script', '--anyparam', '--skip-protected', '--any-other-param'
		);

		$this->assertFalse($this->cli->blocksPrivate());
		$this->assertTrue($this->cli->blocksProtected());
	}
}
