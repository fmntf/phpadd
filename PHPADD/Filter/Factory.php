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
 * @author  Francesco Montefoschi
 * @license http://www.gnu.org/licenses/gpl-3.0.html  GNU GPL 3.0
 */

class PHPADD_Filter_Factory
{
	/**
	 * @var array
	 */
	private $paths;
	
	/**
	 * @var array
	 */
	private $classes;
	
	/**
	 * @var array
	 */
	private $methods;
	
	/**
	 *
	 * @param array $paths Regular expressions of paths to exclude
	 * @param array $classes Regular expressions of class names to exclude
	 * @param array $methods  Regular expressions of method names to exclude
	 */
	public function __construct(array $paths, array $classes, array $methods)
	{
		$this->paths = $paths;
		$this->classes = $classes;
		$this->methods = $methods;
	}
	
	/**
	 * Builds a directory filter
	 * 
	 * @return PHPADD_Filter_Directory
	 */
	public function getDirectoryFilter()
	{
		return new PHPADD_Filter_Directory($this->paths);
	}
	
	/**
	 * Builds a class filter
	 * 
	 * @return PHPADD_Filter_Class
	 */
	public function getClassFilter()
	{
		return new PHPADD_Filter_Class($this->classes);
	}
	
	/**
	 * Builds a method filter
	 * 
	 * @return PHPADD_Filter_Method
	 */
	public function getMethodFilter()
	{
		return new PHPADD_Filter_Method($this->methods);
	}
}