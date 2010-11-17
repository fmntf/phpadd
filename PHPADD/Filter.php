<?php

class PHPADD_Filter
{
	private $protectedMethods;
	private $privateMethods;
	
	public function __construct($protected, $private)
	{
		$this->protectedMethods = $protected;
		$this->privateMethods = $private;
	}

	public function getLevel()
	{
		$level = ReflectionMethod::IS_PUBLIC;
		if ($this->protectedMethods) $level += ReflectionMethod::IS_PROTECTED;
		if ($this->privateMethods) $level += ReflectionMethod::IS_PRIVATE;

		return $level;
	}

	public function skipProtected()
	{
		return $this->protectedMethods;
	}

	public function skipPrivate()
	{
		return $this->privateMethods;
	}
}