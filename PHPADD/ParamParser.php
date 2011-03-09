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
	/**
	 * @var bool
	 */
	private $skipProtected = false;
	
	/**
	 * @var bool
	 */
	private $skipPrivate = false;
	
	/**
	 * @var string Project to scan path
	 */
	private $path;
	
	/**
	 * @var string User-defined bootstrap
	 */
	private $bootstrap;
	
	/**
	 * @var array User-requested publishers
	 */
	private $publishers = array();
	
	/**
	 * @var array User-excluded paths
	 */
	private $excludedPaths = array();
	
	/**
	 * @var array User-excluded method names
	 */
	private $excludedMethods = array();
	
	/**
	 * @var array User-excluded class names
	 */
	private $excludedClasses = array();
	
	const PUBLISHER_MATCHER = '/\-\-publish\-(?P<name>\w+)(?P<stats>\-stats)?/';

	/**
	 * @param array $params
	 */
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

	/**
	 * It looks for valid params in the given array.
	 * If a valid param is found, it's behaviour is applied
	 * then the method returns the input array, minus the
	 * applied parameter.
	 * 
	 * @param array $params
	 * @return array Reduced params array
	 */
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
				
			case '--exclude-paths':
				$argument = $this->parsePath($argument);
				$this->excludedPaths[] = $argument;
				unset($params[1]);
				break;
			
			case '--exclude-classes':
				$this->excludedClasses[] = $argument;
				unset($params[1]);
				break;
			
			case '--exclude-methods':
				$this->excludedMethods[] = $argument;
				unset($params[1]);
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
	
	/**
	 * Checks if the given param is a (valid) publisher.
	 * 
	 * @param string $param Raw param, like --bootstrap or --publish-xml
	 * @return bool
	 */
	private function isPublisher($param)
	{
		return preg_match(self::PUBLISHER_MATCHER, $param, $matches) && $this->hasPublisher($matches['name']);
	}
	
	/**
	 * Checks if the given publisher is valid.
	 * 
	 * @param string $type xml, html, ...
	 * @return bool
	 */
	private function hasPublisher($type)
	{
		$type = ucfirst($type);
		$class = "PHPADD_Publisher_" . $type;
		
		return @class_exists($class);
	}

	/**
	 * Adds a valid publisher into the internal collection
	 * 
	 * @param string $switch Raw param, like --publish-xml
	 * @param string $outputFile Where to save the output
	 */
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
	
	/**
	 * Replaces wildcards and dots in the path.
	 * 
	 * @param string $argument
	 * @return string Regexp friendly path
	 */
	private function parsePath($argument)
	{
		$argument = str_replace('.', '\.', $argument);
		$argument = str_replace('*', '.*', $argument);
		
		return $argument;
	}

	/**
	 * True if the params contained to skip private methods.
	 * 
	 * @return bool
	 */
	public function getSkipPrivate()
	{
		return $this->skipPrivate;
	}

	/**
	 * True if the params contained to skip protected methods.
	 * 
	 * @return bool
	 */
	public function getSkipProtected()
	{
		return $this->skipProtected;
	}

	/**
	 * Returns the path of the application to scan.
	 * 
	 * @return string
	 */
	public function getPath()
	{
		return $this->path;
	}

	/**
	 * Returns the path of the user-defined boostrap.
	 * 
	 * @return string
	 */
	public function getBootstrap()
	{
		return $this->bootstrap;
	}

	/**
	 * Returns a list of user-requested publishers.
	 * 
	 * @return array
	 */
	public function getPublishers()
	{
		return $this->publishers;
	}
	
	/**
	 * Returns a list of user excluded paths.
	 * 
	 * @return array
	 */
	public function getExcludedPaths()
	{
		return $this->excludedPaths;
	}
	
	/**
	 * Returns a list of user excluded class names.
	 * 
	 * @return array
	 */
	public function getExcludedClasses()
	{
		return $this->excludedClasses;
	}
	
	/**
	 * Returns a list of user excluded method names.
	 * 
	 * @return array
	 */
	public function getExcludedMethods()
	{
		return $this->excludedMethods;
	}
}