<?php

class PHPADD_Filter
{
	private $skipProtected;
	private $skipPrivate;
	
	public function __construct($skipProtected = false, $skipPrivate = false)
	{
		$this->skipProtected = $skipProtected;
		$this->skipPrivate = $skipPrivate;
	}

	public function getLevel()
	{
		$level = ReflectionMethod::IS_PUBLIC;
		if (!$this->skipProtected) $level += ReflectionMethod::IS_PROTECTED;
		if (!$this->skipPrivate) $level += ReflectionMethod::IS_PRIVATE;

		return $level;
	}

	public function skipProtected()
	{
		return $this->skipProtected;
	}

	public function skipPrivate()
	{
		return $this->skipPrivate;
	}
}