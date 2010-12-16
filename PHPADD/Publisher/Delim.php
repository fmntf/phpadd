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
 * @author  Joshua Thijssen
 * @license http://www.gnu.org/licenses/gpl-3.0.html  GNU GPL 3.0
 */

class PHPADD_Publisher_Delim extends PHPADD_Publisher_Abstract
{
	public function publish(array $mess)
	{
		$output = $this->getHeader();

		foreach ($mess as $file => $classes) {
			foreach ($classes as $class => $methods) {
				$output .= $this->processMethods($file, $class, $methods);
			}
		}

		$output .= $this->getFooter();
		$this->output($output);
	}

	protected function output($output) {
		echo $output;
	}

	protected function processMethods($file, $class, $methods)
	{
		$output = "";

		foreach ($methods as $method)
		{
			switch ($method['type']) {
				case 'miss':
					$output .= sprintf ("%s\t%s\t%s\t%s\n", $file, $class, $method['method'], 'Missing docblock');
					break;
				case 'invalid':
					foreach ($method['detail'] as $issue) {
						$output .= sprintf ("%s\t%s\t%s\t%s\t%s\n", $file, $class, $method['method'], $this->getType($issue['type']), $issue['name']);
					}
					break;
			}
		}


		return $output;
	}

	protected function getHeader()
	{
		return "";
	}

	protected function getFooter()
	{
		return "";
	}

}