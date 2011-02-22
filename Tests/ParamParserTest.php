<?php

require_once '../PHPADD/ParamParser.php';

class ParamParserTest extends PHPUnit_Framework_TestCase
{
	public function testParsesPathAndPublisher()
	{
		$params = array('--publish-html', '/tmp', '.');
		$parser = new PHPADD_ParamParser($params);

		$this->assertFalse($parser->getSkipPrivate());
		$this->assertFalse($parser->getSkipProtected());
		$this->assertEquals('.', $parser->getPath());
		$this->assertEquals(1, count($parser->getPublishers()));
		$this->assertNull($parser->getBootstrap());
	}
}
