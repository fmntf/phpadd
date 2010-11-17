<?php

class PHPADD_Detector
{
	private $scanPublicMethods = true;
	private $scanProtectedMethods = true;
	private $scanPrivateMethods = true;

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

		$finder = new ClassFinder($path);
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

	private function getScanLevel()
	{
		$level = 0;
		if ($this->scanPublicMethods) $level += ReflectionMethod::IS_PUBLIC;
		if ($this->scanProtectedMethods) $level += ReflectionMethod::IS_PROTECTED;
		if ($this->scanPrivateMethods) $level += ReflectionMethod::IS_PRIVATE;

		return array(
			'scalar' => $level,
			'access' => array(
				'public' => $this->scanPublicMethods,
				'protected' => $this->scanProtectedMethods,
				'private' => $this->scanPrivateMethods
			)
		);
	}

	private function analyze($className)
	{
		$parser = new DocBlockParser($className);

		return $parser->analyze($this->getScalLevel());
	}
}
