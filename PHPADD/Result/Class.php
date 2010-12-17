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
	private $regulars = 0;
	private $missings = array();
	private $outdates = array();
	private $classname;

	function __construct($classname) {
		$this->classname = $classname;
	}

	public function getName() {
		return $this->classname;
	}

	public function countRegular()
	{
		$this->regulars++;
	}

	public function addMissing(PHPADD_Result_Mess_MissingBlock $mess)
	{
		$this->missings[] = $mess;
	}

	public function addOutdated(PHPADD_Result_Mess_OutdatedBlock $mess)
	{
		$this->outdates[] = $mess;
	}

	public function getMethods() {
		return array_merge ($this->missings, $this->outdates);
	}

	public function getMissingBlocks()
	{
		return $this->missings;
	}

	public function getOutdatedBlocks()
	{
		return $this->outdates;
	}

	public function getRegularBlocks()
	{
		return $this->regulars;
	}

	public function isClean()
	{
		$noMissings = count($this->missings) == 0;
		$noOutdated = count($this->outdates) == 0;

		return $noMissings && $noOutdated;
	}
}