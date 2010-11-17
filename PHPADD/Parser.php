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

	public function validateDocBlock(ReflectionMethod $method)
	{
		$errors = array();
		$annotations = $this->parseAnnotations($method->getDocComment());

		foreach ($method->getParameters() as $parameter)
		{
			/* @var $parameter ReflectionParameter */
			$index = $parameter->getPosition();
			$phpType = $parameter->getClass();
			$phpName = '$' . $parameter->getName();

			if (!isset($annotations['param'][$index])) {
				$errors[] = $this->createError('missing-param', $parameter->__toString(), null);
				continue;
			}
			
			list($docType, $docName) = preg_split("/[\s]+/", $annotations['param'][$index]);

			if ($phpType) {
				$phpType = $phpType->getName();
				if ($phpType != $docType) {
					$errors[] = $this->createError('type-mismatch', $docType, $phpType);
				}
			}

			if ($docName != $phpName) {
				$errors[] = $this->createError('name-mismatch', $docName, $phpName);
			}
		}

		for ($unexpected = $index; $unexpected < count($annotations['param'])-1; $unexpected++) {
			$errors[] = $this->createError('unexpected-param', $annotations['param'][$unexpected], null);
		}

		return $errors;
	}

	private function createError($type, $doc, $php)
	{
		return array(
			'type' => $type,
			'docblock' => $doc,
			'phpfile' => $php
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