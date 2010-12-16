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

abstract class PHPADD_Publisher_Abstract
{
	protected $filename;
	
	public function __construct($argument)
	{
		if ($argument == "-") {
			$argument = "php://stdout";
		}
		$this->filename = $argument;
	}

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

	protected function getType($symbolic)
	{
		switch ($symbolic) {
			case 'missing-param':
				return 'Missing parameter';
			case 'unexpected-param':
				return 'Unexpected parameter';
		}
	}
	
	abstract protected function processMethods($file, $class, $methods);

	abstract protected function getHeader();

	abstract protected function getFooter();

	abstract protected function output($output);
}