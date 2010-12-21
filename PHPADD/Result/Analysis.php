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

class PHPADD_Result_Analysis
{
	protected $files = array();

	public function addFileResult(PHPADD_Result_File $mess)
	{
		$this->files[] = $mess;
	}

	public function getCount()
	{
		return count($this->files);
	}

	/**
	 * Gets all scanned files
	 * @return array
	 */
	public function getFiles()
	{
		return $this->files;
	}

	/**
	 * Gets only scanned files with issues
	 * @return array
	 */
	public function getDirtyFiles()
	{
		$files = array();

		foreach ($this->files as $file) {
			$clean = true;
			foreach ($file->getClasses() as $class => $result) {
				if (!$result->isClean()) {
					$clean = false;
				}
			}
			if (!$clean) {
				$files[] = $file;
			}
		}

		return $files;
	}
}