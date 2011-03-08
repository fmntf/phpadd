<?php

class Application_Model_Post
{
	private $title;
	private $text;
	private $author;
	
	public function setTitle($title)
	{
	}
	
	public function setText($text)
	{
	}
	
	public function setAuthor($author)
	{
	}
	
	/**
	 * @param bool $removeExistingComments
	 */
	public function closeComments($removeExistingComments)
	{
	}
	
	/**
	 * @param int $olderThanDate
	 */
	public function deleteComments(DateTime $olderThanDate)
	{
	}
	
	public function clearVisitStats()
	{
	}
}
