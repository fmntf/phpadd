<?php
/** 
* A test class
*
* @param  foo bar
* @return baz
*/
class TestClass {
	public function cazzo($e)
	{
		
	}
	
	/**
	 *
	 * @param culo $e 
	 */
	protected function fica($e)
	{
		
	}
}

$rc = new ReflectionClass('TestClass');
$m = $rc->getMethods();
var_dump($m);
die();
var_dump($m[0]->getDocComment());
var_dump($m[1]->getDocComment());

die();

$finder = new ClassFinder('C:\Users\Francesco\php\example');

var_dump($finder->getList());
die();




	
