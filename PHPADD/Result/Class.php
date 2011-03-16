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

class PHPADD_Result_Class
{
	/**
	 * @var int
	 */
	private $regulars = 0;
	
	/**
	 * @var array
	 */
	private $missings = array();
	
	/**
	 * @var array
	 */
	private $outdates = array();
	
	/**
	 * @var ReflectionClass
	 */
	private $reflection;

	/**
	 * @param ReflectionClass $reflection
	 */
	function __construct(ReflectionClass $reflection)
	{
		$this->reflection = $reflection;
	}

	/**
	 * Get class name
	 * 
	 * @return string
	 */
	public function getName()
	{
		return $this->reflection->getName();
	}

	/**
	 * Get the line where the class definition begins.
	 * 
	 * @return int
	 */
	public function getStartline()
	{
		return $this->reflection->getStartLine();
	}

	/**
	 * Reports a docblock as regular
	 */
	public function countRegular()
	{
		$this->regulars++;
	}

	/**
	 * Adds a missing docblock warning.
	 * 
	 * @param PHPADD_Result_Mess_MissingBlock $mess 
	 */
	public function addMissing(PHPADD_Result_Mess_MissingBlock $mess)
	{
		$this->missings[] = $mess;
	}

	/**
	 * Adds an outdated docblock warning.
	 * 
	 * @param PHPADD_Result_Mess_OutdatedBlock $mess 
	 */
	public function addOutdated(PHPADD_Result_Mess_OutdatedBlock $mess)
	{
		$this->outdates[] = $mess;
	}

	/**
	 * Gets all the methods with warnings.
	 * 
	 * @return array
	 */
	public function getMethods()
	{
		return array_merge($this->missings, $this->outdates);
	}

	/**
	 * Get only the methods with missing docblocks.
	 * 
	 * @return array
	 */
	public function getMissingBlocks()
	{
		return $this->missings;
	}

	/**
	 * Get only the methods with outdated docblocks.
	 * 
	 * @return array
	 */
	public function getOutdatedBlocks()
	{
		return $this->outdates;
	}

	/**
	 * Gets the number of regular docblocks.
	 * 
	 * @return int
	 */
	public function getRegularBlocks()
	{
		return $this->regulars;
	}

	/**
	 * Returns true if the class ha no missing docblocks, nor outdated docblocks.
	 * 
	 * @return bool
	 */
	public function isClean()
	{
		$noMissings = count($this->missings) == 0;
		$noOutdated = count($this->outdates) == 0;

		return $noMissings && $noOutdated;
	}
}