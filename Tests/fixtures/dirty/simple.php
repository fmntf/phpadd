<?php

class Fixture_InvalidMissingExample
{
	/**
	 * Some description here
	 *
	 * @param stdClass $my
	 * @return string
	 */
	public function invalidMethod(stdClass $my, $name) {}
}

class Fixture_InvalidRemovedExample
{
	/**
	 * Some description here
	 *
	 * @param stdClass $my
	 * @param string $name
	 * @return string
	 */
	public function invalidMethod(stdClass $my) {}
}

class Fixture_InvalidMultiExample
{
	/**
	 * Some description here
	 *
	 * @param StdClass $my
	 * @param string $name
	 * @param mixed $nonexisting
	 * @return string
	 */
	public function invalidMethod(stdClass $my) {}
}

class Fixture_NoDocBlock
{
	public function invalidMethod(stdClass $my) {}
}
