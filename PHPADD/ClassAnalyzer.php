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

class PHPADD_ClassAnalyzer
{
	/**
	 * @var ReflectionClass
	 */
	private $reflection;
	
	/**
	 * @var PHPADD_Filterable
	 */
	private $methodFilter;

	/**
	 * @param string $class
	 * @param PHPADD_Filterable $methodFilter
	 */
	public function __construct($class, PHPADD_Filterable $methodFilter)
	{
		$this->reflection = new ReflectionClass($class);
		$this->methodFilter = $methodFilter;
	}

	/**
	 * Analyzes the class with the given filtering level.
	 *
	 * @param PHPADD_Filter_Visibility $filter
	 * @return PHPADD_Result_Class Found mess
	 */
	public function analyze(PHPADD_Filter_Visibility $filter)
	{
		$mess = new PHPADD_Result_Class($this->reflection);

		foreach ($this->reflection->getMethods($filter->getLevel()) as $method) {
			/* @var $method ReflectionMethod */
			
			if ($this->methodFilter->isFiltered($method->getName()) ||
				$this->methodBelongsToParentClass($method)) {
				continue;
			}

			if ($this->isDocBlockMissing($method)) {
				$mess->addMissing($this->createMissing($method));
			} else {
				if ($this->isInheritDoc($method)) {
					$method = $this->getParentBlock($method);
				}
				
				$errors = $this->validateDocBlock($method);
				if (count($errors) > 0) {
					$mess->addOutdated($this->createOutdated($method, $errors));
				} else {
					$mess->countRegular($method);
				}
			}
		}

		return $mess;
	}
	
	private function isInheritDoc(ReflectionMethod $method)
	{
		return strstr($method->getDocComment(), '{@inheritdoc}') !== false;
	}
	
	private function getParentBlock(ReflectionMethod $method)
	{
		$interfaces = $method->getDeclaringClass()->getInterfaces();
		foreach ($interfaces as $interface) {
			/* @var $interface ReflectionClass */
			if ($interface->hasMethod($method->getName())) {
				return new ReflectionMethod(
					$interface->getName(),
					$method->getName()
				);
			}
		}
		
		$parent = $method->getDeclaringClass()->getParentClass();
		
		return new ReflectionMethod(
			$parent->getName(),
			$method->getName()
		);
	}

	/**
	 * Detects if the given method is defined in the parent class or not.
	 * 
	 * @param ReflectionMethod $method
	 * @return bool
	 */
	private function methodBelongsToParentClass(ReflectionMethod $method)
	{
		return $this->reflection->name !== $method->getDeclaringClass()->name;
	}
	
	/**
	 * Factory method for "missing docblock".
	 * 
	 * @param ReflectionMethod $method
	 * @return PHPADD_Result_Mess_MissingBlock 
	 */
	private function createMissing(ReflectionMethod $method)
	{
		return new PHPADD_Result_Mess_MissingBlock($method->name);
	}

	/**
	 * Factory method for "outdated docblock".
	 * 
	 * @param ReflectionMethod $method
	 * @param array $detail
	 * @return PHPADD_Result_Mess_OutdatedBlock 
	 */
	private function createOutdated(ReflectionMethod $method, array $detail)
	{
		return new PHPADD_Result_Mess_OutdatedBlock($method->name, $detail);
	}

	/**
	 * Checks if the method is without docblock or not.
	 * 
	 * @param ReflectionMethod $method
	 * @return bool
	 */
	private function isDocBlockMissing(ReflectionMethod $method)
	{
		return $method->getDocComment() === false;
	}

	/**
	 * Gets a list of params found in the PHP source code.
	 * A parameter may be "$param" or "array $param".
	 * 
	 * @param ReflectionMethod $method
	 * @return array
	 */
	private function getPhpParams(ReflectionMethod $method)
	{
		$params = array();

		foreach ($method->getParameters() as $parameter)
		{
			/* @var $parameter ReflectionParameter */
			$name = '$' . $parameter->getName();

			if ($parameter->isArray()) {
				$params[] = "array $name";
			} else {
				$type = $parameter->getClass();
				if ($type) {
					$params[] = "{$type->getName()} $name";
				} else {
					$params[] = "$name";
				}
			}
		}

		return $params;
	}

	/**
	 * Gets a list of params found in the docblock.
	 * A parameter may be "$param" or "array $param".
	 * If the type is primite, it will not be included.
	 * 
	 * @param ReflectionMethod $method
	 * @return array
	 */
	private function getDocBlockParams(ReflectionMethod $method)
	{
		$params = array();

		$excluded = array('int', 'integer', 'float', 'double', 'bool', 'boolean', 'string', 'mixed', 'object');
		$annotations = $this->parseAnnotations($method->getDocComment());

		if (isset($annotations['param']))
		{
			foreach ($annotations['param'] as $parameter)
			{
				list($blockTypes, $name) = $this->getParameterTypeAndName($parameter);
				
				$types = explode('|', $blockTypes);
				$filtered = array();
				foreach ($types as $type) {
					if (!in_array($type, $excluded)) {
						$filtered[] = "$type";
					}
				}
				
				$type = implode('|', $filtered);

				if ($type != '') {
					$params[] = "$type $name";
				} else {
					$params[] = "$name";
				}
			}
		}

		return $params;
	}

	/**
	 * Detects param type and name by a string like "string $x" or "$var".
	 * @param string $parameter
	 * @return array
	 */
	private function getParameterTypeAndName($parameter)
	{
		$parameterParts = preg_split("/[\s]+/", $parameter);

		if (count($parameterParts) < 2) {
			// Some automatically generated docblocks may be invalid,
			// and only provide a datatype OR a variable name. Determine
			// which it is, and output appropriately
			if (substr($parameterParts[0], 0, 1) === '$') {
				$name = $parameterParts[0];
				$type = '';
			} else {
				$name = '';
				$type = $parameterParts[0];
			}
		} else {
			list($type, $name) = $parameterParts;
		}

		return array($type, $name);
	}

	/**
	 * Check if the given method has the right docblock.
	 *
	 * @param ReflectionMethod $method
	 * @return array Issues in the docblock
	 */
	public function validateDocBlock(ReflectionMethod $method)
	{
		$errors = array();

		$phpParams = $this->getPhpParams($method);
		$docParams = $this->getDocBlockParams($method);

		$diff = new PHPADD_ParamsDiff();
		return $diff->diff($phpParams, $docParams);
	}

	/**
	 * Factory method for docblock error.
	 * 
	 * @param string $type Can be missing-param or unexpected-param
	 * @param string $param Could be "array $list"
	 * @return array
	 */
	private function createError($type, $param)
	{
		return array(
			'type' => $type,
			'name' => $param
		);
	}

	/**
	 * Gets a list of annotations from docblock text.
	 * 
	 * @param string $docblock
	 * @return array
	 */
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