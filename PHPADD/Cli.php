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

require_once 'Detector.php';

class PHPADD_Cli
{
	const VERSION = '1.1.0';

	private $skipProtected = false;
	private $skipPrivate = false;
	private $bootstrap = null;
	private $publisher = null;
	private $path = null;
	
	protected function blocksProtected()
	{
		return $this->skipProtected;
	}

	protected function blocksPrivate()
	{
		return $this->skipPrivate;
	}

	private function includeBootstrap()
	{
		if ($this->bootstrap !== null) {
			require_once $this->bootstrap;
		}
	}

	/**
	 * Parses the command line parms and starts the execution.
	 * If something is wrong, the 'usage' message is displayed.
	 */
	public function run()
	{
		try {
			$this->parseParams();
			$this->includeBootstrap();
			
			$detector = new PHPADD_Detector();
			$detector->setFilter(!$this->blocksProtected(), !$this->blocksPrivate());
			
			$mess = $detector->getMess($this->path);
			$this->publisher->publish($mess);
			
		} catch (Exception $e) {
			echo $e->getMessage() . PHP_EOL;
			echo $this->usage();
		}
	}
	
	private function usage()
	{
		return
			"Usage: phpadd [options] --publish-html /path/to/output/file /directory/to/scan" .
			PHP_EOL . PHP_EOL .
			"Options:" . PHP_EOL .
			"   --skip-protected    skips the scanning of protected methods" . PHP_EOL .
			"   --skip-private      skips the scanning of private methods" . PHP_EOL .
			"   --bootstrap file    includes `file` before the scan" . PHP_EOL;
	}
	
	private function parseParams()
	{
		for ($i = 1; $i < $_SERVER['argc'] -1; $i++) {
			$param = $_SERVER['argv'][$i];
			
			switch ($param) {
				case '--skip-protected':
					$this->skipProtected = true;
					break;
				
				case '--skip-private':
					$this->skipPrivate = true;
					break;
				
				case '--bootstrap':
					$this->bootstrap = $_SERVER['argv'][++$i];
					if (!is_file($this->bootstrap)) {
						throw new InvalidArgumentException('Not a file: ' . $this->bootstrap);
					}
					break;
				
				case '--publish-html':
					require_once "Publisher/Html.php";
					$class = "PHPADD_Publisher_Html";
					$outFile = $_SERVER['argv'][++$i];
					$this->publisher = new $class($outFile);
					break;
				
				default:
					throw new InvalidArgumentException('Invalid argument: ' . $param);
			}
		}
		
		if ($this->publisher === null) {
			throw new InvalidArgumentException('You must specify a publisher.');
		}
		
		if (!isset($_SERVER['argv'][$i])) {
			throw new InvalidArgumentException('You must specify source directory.');
		}
		
		$this->path = $_SERVER['argv'][$i];
		if (!is_dir($this->path)) {
			throw new InvalidArgumentException('Not a directory: ' . $this->path);
		}
	}

}
