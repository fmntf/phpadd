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

class PHPADD_Publisher_Html
{
	public function __construct($argument)
	{
		$this->output = $argument;
	}

	public function publish(array $mess)
	{
		$output = '';

		foreach ($mess as $file => $classes) {
			$output .= "<h1>$file</h1>" . PHP_EOL;
			foreach ($classes as $class => $methods) {
				$output .= "\t<h2>$class</h2>" . PHP_EOL;
				$output .= $this->processMethods($methods);
			}
		}

		file_put_contents($this->output, $output);
	}

	private function processMethods($methods)
	{
		$output = '';
		
		foreach ($methods as $method)
		{
			$output .= "\t\t<h3>Method: " . $method['method'] . '</h3><ul>' . PHP_EOL;

			switch ($method['type']) {
				case 'miss':
					$output .= "\t\t\t<p>Missing docblock</p>" . PHP_EOL;
					break;
				case 'invalid':
					foreach ($method['detail'] as $issue) {
						$output .= "\t\t\t<p>" . $this->getType($issue['type']) . ": - {$issue['name']}</p>" . PHP_EOL;
					}
					break;
			}

			$output .= "\t\t</ul>";
		}

		return $output;
	}

	private function getType($symbolic)
	{
		switch ($symbolic) {
			case 'missing-param':
				return 'Missing parameter';
			case 'unexpected-param':
				return 'Unexpected parameter';
		}
	}
}