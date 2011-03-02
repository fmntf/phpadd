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

class PHPADD_ScanFilter
{
	private $paths;
	private $classes;
	private $methods;
	
	public function __construct(array $paths, array $classes, array $methods)
	{
		$this->paths = $paths;
		$this->classes = $classes;
		$this->methods = $methods;
	}
	
	/**
	 * Returns true if the given directory has to be scanned.
	 * 
	 * @param string $directory
	 * @return bool
	 */
	public function keepsDirectory($directory)
	{
		return $this->keeps($this->paths, $directory);
	}
	
	/**
	 * Returns true if the given class has to be scanned.
	 * 
	 * @param string $class
	 * @return bool
	 */
	public function keepsClass($class)
	{
		return $this->keeps($this->classes, $class);
	}
	
	/**
	 * Returns true if the given method has to be scanned.
	 * 
	 * @param string $class
	 * @return bool
	 */
	public function keepsMethod($method)
	{
		return $this->keeps($this->methods, $method);
	}
	
	/**
	 * Returns true if $test has to be scanned, starting from an
	 * array of banning regular expressions. 
	 * 
	 * @param array $matchers banning regular expressions
	 * @param string $test
	 * @return bool
	 */
	private function keeps($matchers, $test)
	{
		foreach ($matchers as $matcher) {
			if (preg_match("/$matcher/", $test)) {
				return false;
			}
		}
		
		return true;
	}
}