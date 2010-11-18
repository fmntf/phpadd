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

require_once 'Filter.php';

class PHPADD_Parser
{
	private $reflection;

	public function __construct($class)
	{
		$this->reflection = new ReflectionClass($class);
	}

	public function analyze(PHPADD_Filter $filter)
	{
		$mess = array();

		foreach ($this->reflection->getMethods($filter->getLevel()) as $method) {
			/* @var $method ReflectionMethod */

			if ($this->reflection->name !== $method->getDeclaringClass()->name) {
				continue;
			}

			if ($this->isDocBlockMissing($method)) {
				$mess[] = $this->getError('miss', $method);
			} else {
				$errors = $this->validateDocBlock($method);
				if (count($errors) > 0) {
					$mess[] = $this->getError('invalid', $method, $errors);
				}
			}
		}

		return $mess;
	}

	private function getError($type, ReflectionMethod $method, $detail = null)
	{
		$error = array(
			'type' => $type,
			'class' => $method->class,
			'method' => $method->name
		);

		if ($detail !== null) {
			$error['detail'] = $detail;
		}

		return $error;
	}

	private function isDocBlockMissing(ReflectionMethod $method)
	{
		return $method->getDocComment() === false;
	}

	private function getPhpParams(ReflectionMethod $method)
	{
		$params = array();

		foreach ($method->getParameters() as $parameter)
		{
			/* @var $parameter ReflectionParameter */
			$type = $parameter->getClass();
			$name = '$' . $parameter->getName();

			if ($parameter->isArray()) {
				$type = 'array';
			}

			if ($type) {
				if ($type != 'array') $type = $type->getName();
				$params[] = "$type $name";
			} else {
				$params[] = "$name";
			}

		}

		return $params;
	}

	private function getDocBlockParams(ReflectionMethod $method)
	{
		$params = array();

		$excluded = array('int', 'integer', 'float', 'double', 'bool', 'boolean', 'string');
		$annotations = $this->parseAnnotations($method->getDocComment());

		foreach ($annotations['param'] as $parameter)
		{
			list($type, $name) = preg_split("/[\s]+/", $parameter);

			if (!in_array($type, $excluded)) {
				$params[] = "$type $name";
			} else {
				$params[] = "$name";
			}
		}

		return $params;
	}


	public function validateDocBlock(ReflectionMethod $method)
	{
		$errors = array();

		$phpIssues = $this->getPhpParams($method);
		$docIssues = $this->getDocBlockParams($method);

		$missing = array_values(array_diff($phpIssues, $docIssues));
		foreach ($missing as $param) {
			$errors[] = $this->createError('missing-param', $param);
		}

		$unexpected = array_values(array_diff($docIssues, $phpIssues));
		foreach ($unexpected as $param) {
			$errors[] = $this->createError('unexpected-param', $param);
		}

		return $errors;
	}

	private function createError($type, $name)
	{
		return array(
			'type' => $type,
			'name' => $name
		);
	}

	private function parseAnnotations($docblock)
	{
		$annotations = array();

		if (preg_match_all('/@(?P<name>[A-Za-z_-]+)(?:[ \t]+(?P<value>.*?))?[ \t]*\r?$/m', $docblock, $matches)) {
			$numMatches = count($matches[0]);

			for ($i = 0; $i < $numMatches; ++$i) {
				$annotations[$matches['name'][$i]][] = $matches['value'][$i];
			}
		}

		return $annotations;
	}

}