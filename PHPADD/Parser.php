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
				if (!$this->isDocBlockValid($method)) {
					$mess[] = $this->getError('invalid', $method);
				}
			}
		}

		if ($mess === array()) {
			return false;
		}
		return $mess;
	}

	private function getError($type, ReflectionMethod $method)
	{
		return array(
			'type' => $type,
			'class' => $method->class,
			'method' => $method->name
		);
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

	public function isDocBlockValid(ReflectionMethod $method)
	{
//		$comment = $method->getDocComment();
//
//		var_dump('aa', $comment);

		return false;
	}
}