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

require_once 'Exception/InvalidArgument.php';

class PHPADD_ParamParser
{
	private $skipProtected = false;
	private $skipPrivate = false;
	private $path;
	private $bootstrap;
	private $publishers;
	const PUBLISHER_MATCHER = '/\-\-publish\-(?P<name>\w+)(?P<stats>\-stats)?/';

	public function __construct(array $params)
	{
		while ($params !== array()) {
			$params = $this->processParam($params);
		}
		
		if (count($this->publishers) == 0) {
			throw new PHPADD_Exception_InvalidArgument('You must specify at least one publisher.');
		}
		if (!$this->path) {
			throw new PHPADD_Exception_InvalidArgument('You must specify source directory.');
		}
	}
	
	private function processParam(array $params)
	{
		$param = $params[0];
		
		if (count($params)==1) {
			$this->path = $param;
			if (!is_dir($this->path)) {
				throw new PHPADD_Exception_InvalidArgument('Invalid source directory: ' . $this->path);
			}
			return array();
		}
		
		$argument = (isset($params[1])) ? $params[1] : null;

		switch ($param) {
			case '--skip-protected':
				$this->skipProtected = true;
				break;

			case '--skip-private':
				$this->skipPrivate = true;
				break;

			case '--bootstrap':
				$this->bootstrap = $argument;
				unset($params[1]);
				if (!is_file($this->bootstrap)) {
					throw new PHPADD_Exception_InvalidArgument('Invalid bootstrap: ' . $this->bootstrap . ' is not a file.');
				}
				break;
				
			default:
				if ($this->isPublisher($param)) {
					unset($params[1]);
					$this->addPublisher($param, $argument);
				} else {
					throw new PHPADD_Exception_InvalidArgument('Invalid argument: ' . $param);
				}
		}

		unset($params[0]);
		return array_values($params);
	}
	
	
	private function isPublisher($param)
	{
		return preg_match(self::PUBLISHER_MATCHER, $param, $matches) && $this->hasPublisher($matches['name']);
	}
	
	private function hasPublisher($type)
	{
		$type = ucfirst($type);
		$class = "PHPADD_Publisher_" . $type;
		
		return @class_exists($class);
	}

	private function addPublisher($switch, $outputFile)
	{
		preg_match(self::PUBLISHER_MATCHER, $switch, $matches);
		
		$type = ucfirst($matches['name']);
		$class = "PHPADD_Publisher_" . $type;
		
		if ($outputFile == "-") {
			$outputFile = "php://stdout";
		}
		
		$statsOnly = isset($matches['stats']);

		$this->publishers[] = new $class($outputFile, $statsOnly);
	}

	public function getSkipPrivate()
	{
		return $this->skipPrivate;
	}

	public function getSkipProtected()
	{
		return $this->skipProtected;
	}

	public function getPath()
	{
		return $this->path;
	}

	public function getBootstrap()
	{
		return $this->bootstrap;
	}

	public function getPublishers()
	{
		return $this->publishers;
	}
}