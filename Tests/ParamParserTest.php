<?php

require_once '../PHPADD/ParamParser.php';

class ParamParserTest extends PHPUnit_Framework_TestCase
{
	public function getPublishers()
	{
		return array(
			array('html'),
			array('xml'),
			array('delim'),
		);
	}
	
	/**
	 * @dataProvider getPublishers
	 */
	public function testParsesPathAndPublisher($type)
	{
		$params = array('--publish-'.$type, '/tmp/'.$type, '.');
		$parser = new PHPADD_ParamParser($params);

		$this->assertFalse($parser->getSkipPrivate());
		$this->assertFalse($parser->getSkipProtected());
		$this->assertEquals('.', $parser->getPath());
		$this->assertEquals(1, count($parser->getPublishers()));
		$this->assertNull($parser->getBootstrap());
	}
	
	public function testParsesPathAndTwoPublishers()
	{
		$params = array('--publish-html', '/tmpHTML', '--publish-xml', '/tmpXML', '.');
		$parser = new PHPADD_ParamParser($params);

		$this->assertFalse($parser->getSkipPrivate());
		$this->assertFalse($parser->getSkipProtected());
		$this->assertEquals('.', $parser->getPath());
		$this->assertEquals(2, count($parser->getPublishers()));
		$this->assertNull($parser->getBootstrap());
	
		$publishers = $parser->getPublishers();
		$this->assertPublisher($publishers[0], 'Html', '/tmpHTML');
		$this->assertPublisher($publishers[1], 'Xml', '/tmpXML');
	}
	
	public function testSkipsPrivate()
	{
		$params = array('--skip-private', '--publish-html', '/tmp', '.');
		$parser = new PHPADD_ParamParser($params);

		$this->assertTrue($parser->getSkipPrivate());
		$this->assertFalse($parser->getSkipProtected());
	}
	
	public function testSkipsProtected()
	{
		$params = array('--skip-protected', '--publish-html', '/tmp', '.');
		$parser = new PHPADD_ParamParser($params);

		$this->assertFalse($parser->getSkipPrivate());
		$this->assertTrue($parser->getSkipProtected());
	}
	
	public function testScansOnlyPublic()
	{
		$params = array('--skip-protected', '--skip-private', '--publish-html', '/tmp', '.');
		$parser = new PHPADD_ParamParser($params);

		$this->assertTrue($parser->getSkipPrivate());
		$this->assertTrue($parser->getSkipProtected());
	}
	
	public function testGetsBootstrap()
	{
		$params = array('--bootstrap', '../phpadd.php', '--publish-html', '/tmp', '.');
		$parser = new PHPADD_ParamParser($params);

		$this->assertEquals('../phpadd.php', $parser->getBootstrap());
	}
	
	/**
	 * @expectedException PHPADD_Exception_InvalidArgument
	 * @expectedExceptionMessage Invalid bootstrap
	 */
	public function testDetectsMissingBootstrapFile()
	{
		$params = array('--bootstrap', 'WRONGFILENAME', '--publish-html', '/tmp', '.');
		$parser = new PHPADD_ParamParser($params);
	}
	
	public function testDashRedirectsToStdout()
	{
		$params = array('--publish-html', '-', '.');
		$parser = new PHPADD_ParamParser($params);
		$publishers = $parser->getPublishers();
		
		$this->assertPublisher($publishers[0], 'Html', 'php://stdout');
	}
	
	/**
	 * @expectedException PHPADD_Exception_InvalidArgument
	 * @expectedExceptionMessage publisher
	 */
	public function testRequiresAtLeastOnePublisher()
	{
		$params = array('--skip-private', '.');
		$parser = new PHPADD_ParamParser($params);
	}
	
	/**
	 * @expectedException PHPADD_Exception_InvalidArgument
	 * @expectedExceptionMessage source directory
	 */
	public function testRequiresSourceDirectory()
	{
		$params = array('--publish-html', '-');
		$parser = new PHPADD_ParamParser($params);
	}
	
	/**
	 * @expectedException PHPADD_Exception_InvalidArgument
	 * @xxexpectedExceptionMessage Invalid source directory
	 */
	public function testChecksIfSourceDirectoryExists()
	{
		$params = array('--publish-html', '-', 'FAKEDIRECTORY');
		$parser = new PHPADD_ParamParser($params);
	}
	
	
	private function assertPublisher($publisher, $type, $destination)
	{
		$ref = new ReflectionObject($publisher);
		$property = $ref->getProperty('destination');
		
		$property->setAccessible(true);
		$value = $property->getValue($publisher);
		
		$this->assertEquals('PHPADD_Publisher_' . $type, get_class($publisher));
		$this->assertEquals($destination, $value);
	}
}