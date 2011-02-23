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

	public function __construct(array $params)
	{
		for ($i = 0; $i < count($params) -1; $i++) {
			$param = $params[$i];

			switch ($param) {
				case '--skip-protected':
					$this->skipProtected = true;
					break;

				case '--skip-private':
					$this->skipPrivate = true;
					break;

				case '--bootstrap':
					$this->bootstrap = $params[++$i];
					if (!is_file($this->bootstrap)) {
						throw new PHPADD_Exception_InvalidArgument('Invalid bootstrap: ' . $this->bootstrap . ' is not a file.');
					}
					break;

				case '--publish-html':
					$this->addPublisher('Html', $params[++$i]);
					break;

				case '--publish-xml':
					$this->addPublisher('Xml', $params[++$i]);
					break;

				case '--publish-delim':
					$this->addPublisher('Delim', $params[++$i]);
					break;


				default:
					throw new PHPADD_Exception_InvalidArgument('Invalid argument: ' . $param);
			}
		}

		if (count($this->publishers) == 0) {
			throw new PHPADD_Exception_InvalidArgument('You must specify at least one publisher.');
		}

		if (!isset($params[$i])) {
			throw new PHPADD_Exception_InvalidArgument('You must specify source directory.');
		}

		$this->path = $params[$i];
		if (!is_dir($this->path)) {
			throw new PHPADD_Exception_InvalidArgument('Invalid source directory: ' . $this->path);
		}
	}

	private function addPublisher($name, $outputFile)
	{
		$class = "PHPADD_Publisher_" . $name;
		if ($outputFile == "-") {
			$outputFile = "php://stdout";
		}

		$this->publishers[] = new $class($outputFile);
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