<?php

class CliTest extends PHPUnit_Framework_TestCase
{
	const BOOTSTRAP_PATH = '/tmp/phpadd-clitest-bootstrap.php';
	
	public function setUp()
	{
		$bootstrap = '<?php
			include "fixtures/application/models/user.php";
			include "fixtures/application/models/author.php";
			include "fixtures/application/models/post.php";
		';
		file_put_contents(self::BOOTSTRAP_PATH, $bootstrap);
	}
	
	public function testProducesAValidOutput()
	{
		$output = $this->getOutput();
		
		$this->assertNotNull($output->stats);
		$this->assertNotNull($output->report);
		
		$this->markTestIncomplete();
	}
	
	private function getOutput()
	{
		$bootstrap = '--bootstrap ' . self::BOOTSTRAP_PATH;
		$result = exec("php ../phpadd.php --publish-json - $bootstrap fixtures/application");
		
		return json_decode($result);
	}
}