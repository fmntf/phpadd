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
	/**
	 * @var PHPADD_Filter_Visibility
	 */
	protected $filter;
	
	/**
	 * @var PHPADD_Filter_Factory
	 */
	protected $filterFactory;

	/**
	 * @param PHPADD_Filter_Factory $filterFactory Used to create directory, class and method filters
	 * @param PHPADD_Filter_Visibility $scopeFilter Used to prevent the scan of private/protected methods
	 */
	public function __construct(PHPADD_Filter_Factory $filterFactory, PHPADD_Filter_Visibility $scopeFilter)
	{
		$this->filterFactory = $filterFactory;
		$this->filter = $scopeFilter;
	}

	/**
	 * Get the documentation mess in a path
	 *
	 * @param string $path
	 * @return PHPADD_Result_Analysis
	 */
	public function getMess($path)
	{
		$directoryFilter = $this->filterFactory->getDirectoryFilter();
		$classFilter = $this->filterFactory->getClassFilter();
		
		$mess = new PHPADD_Result_Analysis();

		$finder = new PHPADD_ClassFinder($path, $directoryFilter, $classFilter);
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

	/**
	 * Analyzes a class.
	 * 
	 * @param string $className
	 * @return PHPADD_Result_Class found mess
	 */
	private function analyze($className)
	{
		$parser = new PHPADD_ClassAnalyzer($className, $this->filterFactory->getMethodFilter());

		return $parser->analyze($this->filter);
	}
}
