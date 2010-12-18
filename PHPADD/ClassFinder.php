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

class PHPADD_ClassFinder
{
	private $path;

	public function __construct($path)
	{
		$this->path = $path;
	}

	/**
	 * Get the classes in the files of the given path.
	 *
	 * @return array
	 */
	public function getList()
	{
		$directory = new RecursiveDirectoryIterator($this->path);
		$iterator = new RecursiveIteratorIterator($directory);
		$files = new RegexIterator($iterator, '/^.+\.php$/i', RecursiveRegexIterator::GET_MATCH);

		$classes = array();

		foreach ($files as $file) {
			$fileName = $file[0];
			$classes[$fileName] = $this->processFile($fileName);
		}

		return $classes;
	}

	private function processFile($fileName)
	{
		$classes = array();

		$tokens = token_get_all(file_get_contents($fileName));
		foreach ($tokens as $i => $token) {
			if ($token[0] == T_CLASS) {
				$classes[] = $this->getNextClass($tokens, $i);
			}
		}

		return $classes;
	}

	private function getNextClass(array $tokens, $i) {
		for ($i; $i < count($tokens); $i++) {
			if ($tokens[$i][0] == T_STRING) {
				return $tokens[$i][1];
			}
		}
	}
}
