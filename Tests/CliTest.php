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
		
		$this->assertNotNull($output['stats']);
		$this->assertNotNull($output['report']);
		
		$this->assertOnStats($output['stats'], 4, 14, 6, 6, 2);
		
		$this->assertOnMissingBlocks($output['report'], array(
			'fixtures/application/controllers/index.php' => array(
				'Application_IndexController' => array('someAction'),
			),
			'fixtures/application/models/post.php' => array(
				'Application_Model_Post' => array('setTitle', 'setText', 'setAuthor', 'clearVisitStats'),
			),
			'fixtures/application/models/user.php' => array(
				'Application_Model_User' => array('setUsername'),
			),
		));
		
		$this->assertOnOutdatedBlocks($output['report'], array(
			'fixtures/application/controllers/index.php' => array(
				'Application_IndexController' => array('doSomething'),
			),
			'fixtures/application/models/post.php' => array(
				'Application_Model_Post' => array('deleteComments'),
			),
		));
	}
	
	public function testSkippesPrivateMethods()
	{
		$output = $this->getOutput('--skip-private');
		
		$this->assertOnOutdatedBlocks($output['report'], array(
			'fixtures/application/models/post.php' => array(
				'Application_Model_Post' => array('deleteComments'),
			),
		));
	}
	
	public function testSkipsSetters()
	{
		$output = $this->getOutput('--exclude-methods ^set');
		
		$this->assertOnStats($output['stats'], 4, 9, 5, 2, 2);
		
		$this->assertOnMissingBlocks($output['report'], array(
			'fixtures/application/controllers/index.php' => array(
				'Application_IndexController' => array('someAction'),
			),
			'fixtures/application/models/post.php' => array(
				'Application_Model_Post' => array('clearVisitStats'),
			),
		));
	}
	
	private function getOutput($switches = '')
	{
		$bootstrap = '--bootstrap ' . self::BOOTSTRAP_PATH;
		$result = exec("php ../phpadd.php --publish-json - $bootstrap $switches fixtures/application");
		
		$decoded = json_decode($result, true);
		
		if (!$decoded) {
			$this->fail('The command was not successful: ' . $result);
		}
		
		return $decoded;
	}
	
	private function assertOnStats($stats, $noFiles, $noMethods, $regular, $missing, $outdated)
	{
		$this->assertEquals($noFiles, $stats['files-count'], 'Unexpected number of files');
		$this->assertEquals($noMethods, $stats['methods-count'], 'Unexpected number of methods');
		$this->assertEquals($regular, $stats['regular-blocks'], 'Unexpected number of regular docblocks');
		$this->assertEquals($missing, $stats['missing-blocks'], 'Unexpected number of missing docblocks');
		$this->assertEquals($outdated, $stats['outdated-blocks'], 'Unexpected number of outdateddocblocks');
	}
	
	private function assertOnMissingBlocks(array $report, array $expectedFiles)
	{
		foreach ($report as $file => $classes) {
			foreach ($classes as $class => $methods) {
				foreach ($methods as $method => $methodMess) {
					$hasNoDocblock = $this->methodHasNoDocblock($methodMess);
					if (isset($expectedFiles[$file]) && isset($expectedFiles[$file][$class])
						&& in_array($method, $expectedFiles[$file][$class])
					) {
						$this->assertTrue($hasNoDocblock, "We expected to have no docblock on $class::$method");
						$expectedFiles = $this->getExpectedFilesWithoutMethod($expectedFiles, $file, $class, $method);
					} else {
						if ($hasNoDocblock) {
							$this->fail("We did not expect to have no docblock on $class::$method");
						}
					}
				}
			}
		}
		
		$this->assertTrue(count($expectedFiles) == 0,
			'There are some methods with regular docblock that were supposed to have a missing block');
	}
	
	private function assertOnOutdatedBlocks(array $report, array $expectedFiles)
	{
		foreach ($report as $file => $classes) {
			foreach ($classes as $class => $methods) {
				foreach ($methods as $method => $methodMess) {
					$hasOutdatedDocblock = $this->methodHasOutdatedDocblock($methodMess);
					if (isset($expectedFiles[$file]) && isset($expectedFiles[$file][$class])
						&& in_array($method, $expectedFiles[$file][$class])
					) {
						$this->assertTrue($hasOutdatedDocblock, "We expected to have an outdated docblock on $class::$method");
						$expectedFiles = $this->getExpectedFilesWithoutMethod($expectedFiles, $file, $class, $method);
					} else {
						if ($hasOutdatedDocblock) {
							$this->fail("We did not expect to have an outdated docblock on $class::$method");
						}
					}
				}
			}
		}
		
		$this->assertTrue(count($expectedFiles) == 0,
			'There are some methods with regular docblock that were supposed to have an outdated block');
	}
	
	private function methodHasNoDocblock($methodMess)
	{
		return count($methodMess)==1 && $methodMess[0]=='Missing docblock';
	}
	
	private function methodHasOutdatedDocblock($methodMess)
	{
		return $methodMess[0]!='Missing docblock';
	}
	
	private function getExpectedFilesWithoutMethod(array $expectedFiles, $file, $class, $method)
	{
		$key = array_search($method, $expectedFiles[$file][$class]);
		unset($expectedFiles[$file][$class][$key]);
		if (count($expectedFiles[$file][$class])==0) {
			unset($expectedFiles[$file][$class]);
			if (count($expectedFiles[$file])==0) unset($expectedFiles[$file]);
		}
		
		return $expectedFiles;
	}
}
