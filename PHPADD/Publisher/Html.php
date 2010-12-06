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
		$output = $this->getHeader();

		foreach ($mess as $file => $classes) {
			foreach ($classes as $class => $methods) {
				$output .= "\t<h1 title=\"Defined in: $file\">$class</h1>" . PHP_EOL;
				$output .= $this->processMethods($methods);
			}
		}

		$output .= $this->getFooter();
		file_put_contents($this->output, $output);
	}

	private function processMethods($methods)
	{
		$output = '';
		
		foreach ($methods as $method)
		{
			$output .= "\t\t<h2>Method: " . $method['method'] . '</h2><ul>' . PHP_EOL;

			switch ($method['type']) {
				case 'miss':
					$output .= "\t\t\t<li>Missing docblock</li>" . PHP_EOL;
					break;
				case 'invalid':
					foreach ($method['detail'] as $issue) {
						$output .= "\t\t\t<li>" . $this->getType($issue['type']) . ": - <code>{$issue['name']}</code></li>" . PHP_EOL;
					}
					break;
			}

			$output .= "\t\t</ul>\n";
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

	private function getHeader()
	{
		return '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
        <title>phpadd result</title>
		<style type="text/css">
			h1 {
				font-size: 19px;
				border-bottom: 1px solid #c0c0c0;
				margin-top: 19px;
			}
			h2 {
				font-size: 16px;
				margin: 2px 0 0 9px;
			}
			ul {
				list-style-type: circle;
				font-size: 14px;
				margin: 7px 0 13px 0;
			}
			code {
				font-size: 13px;
			}
		</style>
    </head>
    <body>';
	}

	private function getFooter()
	{
		return '</body>
</html>';
	}

}