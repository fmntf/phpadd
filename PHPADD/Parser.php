<?php

class PHPADD_Parser
{
	public function __construct($class)
	{
		$this->reflection = new ReflectionClass($class);
	}

	public function analyze($level)
	{
		foreach ($this->reflection->getMethods($level['scalar']) as $method) {
			$comment = $method->getDocComment();
//			if ($comment === false && $level['access'][])
		}
	}

	public function getComment()
	{

	}
}