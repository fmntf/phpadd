<?php

class Application_IndexController
{
	/**
	 * This is valid!
	 */
	public function indexAction()
	{
	}
	
	// missing block here
	public function someAction(array $request)
	{
	}
	
	/**
	 * @param string $x
	 * @param string $y
	 */
	private function doSomething($x, $z)
	{
	}
}