<?php

require_once 'ClassFinder.php';
require_once 'Parser.php';

class PHPADD_Detector
{
	protected $filter;

	public function setFilter($scanProtectedMethods, $scanPrivateMethods)
	{
		$this->filter = new PHPADD_Filter($scanProtectedMethods, $scanPrivateMethods);
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


	private function analyze($className)
	{
		$parser = new PHPADD_Parser($className);

		return $parser->analyze($this->filter);
	}
}
