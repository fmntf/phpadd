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

class PHPADD_Cli
{
	const VERSION = '1.3.0';

	private $skipProtected = false;
	private $skipPrivate = false;
	private $excludedPaths = array();
	private $excludedClasses = array();
	private $excludedMethods = array();
	private $bootstrap = null;
	private $publishers = array();
	private $path = null;
	
	/**
	 * Includes (require_once) the bootstrap, if any.
	 */
	private function includeBootstrap()
	{
		if ($this->bootstrap !== null) {
			require_once $this->bootstrap;
		}
	}

	/**
	 * Parses the command line parms and starts the execution.
	 * If something of PHPADD is wrong, the 'usage' message is displayed.
	 * Any other Exception will go up.
	 */
	public function run()
	{
		try {
			$this->parseParams();
			$this->includeBootstrap();
			
			$scopeFilter = new PHPADD_Filter_Visibility(!$this->skipProtected, !$this->skipPrivate);
			$filterFactory = new PHPADD_Filter_Factory($this->excludedPaths, $this->excludedClasses, $this->excludedMethods);
			$detector = new PHPADD_Detector($filterFactory, $scopeFilter);
			
			$mess = $detector->getMess($this->path);
			foreach ($this->publishers as $publisher) {
				$publisher->publish($mess);
			}
			
		} catch (PHPADD_Exception_InvalidArgument $e) {
			echo $e->getMessage() . PHP_EOL;
			echo $this->usage();
			exit(1);
		}
	}
	
	/**
	 * Prints usage menu.
	 */
	private function usage()
	{
		return
			"PHPADD v" . self::VERSION . PHP_EOL .
			"Usage: phpadd [options] <publisher> /directory/to/scan" .
			PHP_EOL . PHP_EOL .
			"At least one publisher must be given: ". PHP_EOL .
			"   --publish-html  <file>     HTML output" . PHP_EOL .
			"   --publish-xml   <file>     XML output" . PHP_EOL .
			"   --publish-delim <file>     Tab delimited output" . PHP_EOL .
			"   --publish-jsnon <file>     Tab delimited output" . PHP_EOL .
			PHP_EOL .
			"<file> may be a regular file or a dash (-) for stdout." . PHP_EOL .
			"Appending '-stats' you can generate only general statistics" . PHP_EOL . 
			"instead of the full report (e.g. --publish-html-stats <file>)" . PHP_EOL .
			PHP_EOL .
			"Options:" . PHP_EOL .
			"   --skip-protected    skips the scanning of protected methods" . PHP_EOL .
			"   --skip-private      skips the scanning of private methods" . PHP_EOL .
			"   --bootstrap file    includes `file` before the scan" . PHP_EOL .
			PHP_EOL .
			"Filters: ". PHP_EOL .
			"   --exclude-paths <regex>    Skips all paths matching <regex>" . PHP_EOL .
			"   --exclude-classes <regex>  Skips all classes matching <regex>" . PHP_EOL .
			"   --exclude-methods <regex>  Skips all methods matching <regex>" . PHP_EOL .
			PHP_EOL .
			"Regular expression examples:" . PHP_EOL .
			"   Foo            matches strings containing Foo" . PHP_EOL .
			"   ^(g|s)et       matches getters and setters" . PHP_EOL .
			"   ^__construct$  matches constructors" . PHP_EOL .
			"The symbol `*` means \"everything in every quantity\"." . PHP_EOL .
			"In your shell you may need to escape some symbols (e.g: ^\(s\|g\)et )." . PHP_EOL;
	}

	/**
	 * Sets CLI internal state by unpacking argv
	 */
	private function parseParams()
	{
		$params = $_SERVER['argv'];
		unset($params[0]);
		$parser = new PHPADD_ParamParser(array_values($params));

		$this->skipProtected = $parser->getSkipProtected();
		$this->skipPrivate = $parser->getSkipPrivate();
		$this->excludedPaths = $parser->getExcludedPaths();
		$this->excludedClasses = $parser->getExcludedClasses();
		$this->excludedMethods = $parser->getExcludedMethods();
		$this->bootstrap = $parser->getBootstrap();
		$this->publishers = $parser->getPublishers();
		$this->path = $parser->getPath();
	}

}
