<?php

require 'Parser.php';

class PHPADD_Detector
{
	protected $scanProtectedMethods = true;
	protected $scanPrivateMethods = true;

	public function preventProtectedScanning()
	{
		$this->scanProtectedMethods = false;
	}

	public function preventPrivateScanning()
	{
		$this->scanPrivateMethods = false;
	}

	public function getMess($path)
	{
		$mess = array();

		$finder = new PHPADD_ClassFinder($path);
		foreach ($finder->getList() as $file => $classes) {
			include $file;
			foreach ($classes as $class) {
				$result = $this->analyze($class);
				if ($result->hasMess()) {
					$mess[$file][$class] = $result;
				}
			}
		}

		return $mess;
	}

	protected function getScanLevel()
	{
		$level = ReflectionMethod::IS_PUBLIC;
		if ($this->scanProtectedMethods) $level += ReflectionMethod::IS_PROTECTED;
		if ($this->scanPrivateMethods) $level += ReflectionMethod::IS_PRIVATE;

		return array(
			'scalar' => $level,
			'access' => array(
				'protected' => $this->scanProtectedMethods,
				'private' => $this->scanPrivateMethods
			)
		);
	}

	private function analyze($className)
	{
		$parser = new PHPADD_Parser($className);

		return $parser->analyze($this->getScalLevel());
	}
}
