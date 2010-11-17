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
