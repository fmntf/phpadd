<?php

class IntegrationTest extends PHPUnit_Framework_TestCase
{
	public function testZeroDifferencesInEquals()
	{
		$php = array(
			'stdObj $first',
			'$fresh',
			'$any',
		);

		$docblock = array(
			'stdObj $first',
			'$fresh',
			'$any',
		);

		$diff = array_diff($php, $docblock);
		$this->assertEquals(0, count($diff));

		$diff = array_diff($docblock, $php);
		$this->assertEquals(0, count($diff));
	}

	public function testMissingFromDocBlock()
	{
		$php = array(
			'stdObj $first',
			'$fresh',
			'$any',
		);

		$docblock = array(
			'stdObj $first',
			'$fresh',
		);

		$diff = array_values(array_diff($php, $docblock));
		$this->assertEquals(1, count($diff));
		$this->assertEquals('$any', $diff[0]);

		$diff = array_diff($docblock, $php);
		$this->assertEquals(0, count($diff));
	}

	public function testMissingFromPhp()
	{
		$php = array(
			'stdObj $first',
			'$fresh',
		);

		$docblock = array(
			'stdObj $first',
			'$fresh',
			'$any',
		);

		$diff = array_diff($php, $docblock);
		$this->assertEquals(0, count($diff));

		$diff = array_values(array_diff($docblock, $php));
		$this->assertEquals(1, count($diff));
		$this->assertEquals('$any', $diff[0]);
	}

	public function testDetectsDisorder()
	{
		$php = array(
			'stdObj $first',
			'stdObj $second',
			'$fresh',
			'$dish',
			'$non',
			'array $sense',
		);

		$docblock = array(
			'stdObj $first',
			'stdObj $second',
			'$fresh',
			'$non',
			'array $sense',
		);

		$diff = array_values(array_diff($php, $docblock));
		$this->assertEquals(1, count($diff));
		$this->assertEquals('$dish', $diff[0]);

		$diff = array_diff($docblock, $php);
		$this->assertEquals(0, count($diff));
	}
}