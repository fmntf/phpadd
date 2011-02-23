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

class PHPADD_Publisher_Json extends PHPADD_Publisher_Abstract
{
	/**
	 * Renders the mess in an HTML page.
	 *
	 * @param PHPADD_Result_Analysis $mess
	 */
	public function publish(PHPADD_Result_Analysis $mess)
	{
		$output = $this->getJson($mess);
		file_put_contents($this->destination, $output);
	}

	/**
	 * Gets a string with the JSON
	 * 
	 * @param PHPADD_Result_Analysis $mess
	 * @return string
	 */
	private function getJson(PHPADD_Result_Analysis $mess)
	{
		$report['stats'] = $this->getStats($mess);
		
		if (!$this->statsOnly) {
			$report['report'] = array();
			foreach ($mess->getDirtyFiles() as $file) {
				$fileName = $file->getName();
				$report['report'][$fileName] = array();
				foreach ($file->getClasses() as $class) {
					$className = $class->getName();
					$report['report'][$fileName][$className] = array();
					foreach ($class->getMethods() as $method) {
						$methodName = $method->getName();
						$report['report'][$fileName][$className][$methodName] = $method->toList();
					}
				}
			}
		}
		
		return json_encode($report);
	}

	/**
	 * Gets a serializable array with the statistics.
	 * 
	 * @param PHPADD_Result_Analysis $mess
	 * @return array
	 */
	private function getStats(PHPADD_Result_Analysis $mess)
	{
		$helper = new PHPADD_Stats();
		return $helper->getStats($mess);
	}
}