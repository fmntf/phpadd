<?php

class InvalidMissingExample
{
	/**
	 * Some description here
	 *
	 * @param stdClass $my
	 * @return string
	 */
	public function invalidMethod(stdClass $my, $name) {}
}

class InvalidRemovedExample
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
