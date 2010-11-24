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
	protected function blocksProtected()
	{
		return in_array('--skip-protected', $_SERVER['argv']);
	}

	protected function blocksPrivate()
	{
		return in_array('--skip-private', $_SERVER['argv']);
	}

	protected function getBootstrap()
	{
		$hasBootstrap = array_search('--bootstrap', $_SERVER['argv']);
		if ($hasBootstrap === false) {
			return false;
		}

		return $_SERVER['argv'][$hasBootstrap + 1];
	}

	protected function getPublisher()
	{
		foreach ($_SERVER['argv'] as $i => $arg) {
			$parts = explode('--publish-', $arg);
			if (isset($parts[1])) {
				$publisher = ucfirst($parts[1]);
				require_once "Publisher/$publisher.php";
				$class = "PHPADD_Publisher_$publisher";

				$arg = $_SERVER['argv'][$i+1];
				return new $class($arg);
			}
		}

		throw new InvalidArgumentException('Missing publisher.');
	}

	private function getPath()
	{
		$last = $_SERVER['argc'] - 1;
		$dir = $_SERVER['argv'][$last];
		if (!is_dir($dir)) {
			throw new InvalidArgumentException('Invalid source directory.');
		}

		return $dir;
	}

	public function run()
	{
		try {
			$detector = new PHPADD_Detector();
			$detector->setFilter(!$this->blocksProtected(), !$this->blocksPrivate());

			$bootstrap = $this->getBootstrap();
			if ($bootstrap) {
				if (is_file($bootstrap)) {
					require_once $bootstrap;
				} // else warn
			}

			$mess = $detector->getMess($this->getPath());

			$publisher = $this->getPublisher();
			$publisher->publish($mess);
		} catch (Exception $e) {
			echo $e->getMessage();
			echo "\nUsage: phpadd [options] --publish-html /path/to/output/file /directory/to/scan\n\n";
			echo "Options: \n";
			echo "   --skip-protected    skips the scanning of protected methods\n";
			echo "   --skip-private      skips the scanning of private methods\n";
			echo "   --bootstrap file    includes `file` before the scan\n";
		}
	}
}
