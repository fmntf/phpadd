<?php

/**
 * phpadd - abandoned docblocks detector
 * Copyright (C) 2010 Francesco Montefoschi <francesco.monte@gmail.com>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @package phpadd
 * @author  Joshua Thijssen
 * @license http://www.gnu.org/licenses/gpl-3.0.html  GNU GPL 3.0
 */

class PHPADD_Result_File
{
	/**
	 * @var array
	 */
	protected $classes = array();
	
	/**
	 * @var string
	 */
	protected $filename;

	/**
	 * @param string $filename
	 */
	public function __construct($filename)
	{
		$this->filename = $filename;
	}

	/**
	 * Get file name
	 * 
	 * @return string
	 */
	public function getName()
	{
		return $this->filename;
	}

	/**
	 * Get the number of classes
	 * 
	 * @return int
	 */
	public function getCount()
	{
		return count($this->classes);
	}

	/**
	 * Get the classes in this file
	 * 
	 * @return array
	 */
	public function getClasses()
	{
		return $this->classes;
	}
	
	/**
	 * Adds a class (result) to the file.
	 * 
	 * @param string $className
	 * @param PHPADD_Result_Class $mess 
	 */
	public function addClassResult($className, PHPADD_Result_Class $mess)
	{
		$this->classes[$className] = $mess;
	}

}