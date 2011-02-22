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

class PHPADD_Detector
{
	protected $filter;

	public function __construct(PHPADD_Filter $filter)
	{
		$this->filter = $filter;
	}

	/**
	 * Get the documentation mess in a path
	 *
	 * @param string $path
	 * @return PHPADD_Result_Analysis
	 */
	public function getMess($path)
	{
		$mess = new PHPADD_Result_Analysis();

		$finder = new PHPADD_ClassFinder($path);
		foreach ($finder->getList() as $file => $classes) {
			$result = new PHPADD_Result_File($file);

			require_once $file;
			foreach ($classes as $class) {
				$result->addClassResult($class, $this->analyze($class));
			}

			$mess->addFileResult($result);
		}

		return $mess;
	}

	private function analyze($className)
	{
		$parser = new PHPADD_Parser($className);

		return $parser->analyze($className, $this->filter);
	}
}
