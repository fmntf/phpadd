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
			require_once $file;
			foreach ($classes as $class) {
				$classMess = $this->analyze($class);
				if ($classMess) {
					$mess[$file][$class] = $classMess;
				}
			}
		}

		return $mess;
	}

	protected function createFilter()
	{
		return new PHPADD_Filter($this->scanProtectedMethods, $this->scanPrivateMethods);
	}

	private function analyze($className)
	{
		$parser = new PHPADD_Parser($className);

		return $parser->analyze($this->getScalLevel());
	}
}
