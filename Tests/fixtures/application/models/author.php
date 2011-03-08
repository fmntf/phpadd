<?php

class Application_Model_Author extends Application_Model_User
{
	/**
	 * {@inheritdoc}
	 * Checks also if the user is writing some article.
	 */
	public function isActive()
	{
	}
	
	/**
	 * @param Application_Model_Post $post The post to sign
	 */
	public function sign(Application_Model_Post $post)
	{
	}
}