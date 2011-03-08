<?php

class Application_Model_User
{
	private $username;
	private $password;
	
	public function setUsername($username)
	{
	}
	
	/**
	 * @param string $password Will be converted in md5 automatically
	 */
	public function setPassword($password)
	{
	}
	
	/**
	 * Returns true if the user is logged in and is reading some article.
	 */
	public function isActive()
	{
	}
}