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

class PHPADD_Filter
{
	private $scanProtected;
	private $scanPrivate;
	
	public function __construct($scanProtected = true, $scanPrivate = true)
	{
		$this->scanProtected = $scanProtected;
		$this->scanPrivate = $scanPrivate;
	}

	/**
	 * Prepares a mask to access class methods
	 *
	 * @return int
	 */
	public function getLevel()
	{
		$level = ReflectionMethod::IS_PUBLIC;
		if ($this->scanProtected) $level += ReflectionMethod::IS_PROTECTED;
		if ($this->scanPrivate) $level += ReflectionMethod::IS_PRIVATE;

		return $level;
	}
}