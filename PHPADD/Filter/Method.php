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

class PHPADD_Filter_Method implements PHPADD_Filterable
{
	/**
	 * @var array
	 */
	private $methods;
	
	/**
	 * @param array $methods List of regexp of excluded method names
	 */
	public function __construct(array $methods)
	{
		$this->methods = $methods;
	}
	
	/**
	 * Returns true if the given method has NOT to be scanned.
	 * 
	 * @param string $method
	 * @return bool
	 */
	public function isFiltered($method)
	{
		foreach ($this->methods as $path) {
			if (preg_match("/$path/", $method)) {
				return true;
			}
		}
		
		return false;
	}
}