<?php

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

			if ($this->isDocBlockMissing($method)) {
				if (!$this->canDocBlockMiss($method, $filter)) {
					$mess[] = $this->getError('miss', $method);
				}
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

	private function canDocBlockMiss(ReflectionMethod $method, PHPADD_Filter $filter)
	{
		if ($filter->skipPrivate() && $method->isPrivate() ||
			$filter->skipProtected() && $method->isProtected()) {
			return true;
		}
		return false;
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