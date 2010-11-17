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





class DocBlockParser
{	
	public function __construct($class)
	{
		$this->reflection = new ReflectionClass($class);
	}
	
	public function analyze($level)
	{
		foreach ($this->reflection->getMethods($level['scalar']) as $method) {
			$comment = $method->getDocComment();
			if ($comment === false && $level['access'][])
		}
	}
	
	public function getComment()
	{
		
	}
}

class ClassFinder
{
	private $path;

	public function __construct($path)
	{
		$this->path = $path;
	}

	public function getList()
	{
		$directory = new RecursiveDirectoryIterator($this->path);
		$iterator = new RecursiveIteratorIterator($directory);
		$files = new RegexIterator($iterator, '/^.+\.php$/i', RecursiveRegexIterator::GET_MATCH);
		
		$classes = array();

		foreach ($files as $file) {
			$fileName = $file[0];
			$classes[$fileName] = $this->processFile($fileName);
		}
		
		return $classes;
	}

	private function processFile($fileName)
	{
		$classes = array();
		
		$tokens = token_get_all(file_get_contents($fileName));
		foreach ($tokens as $i => $token) {
			if ($token[0] == T_CLASS) {
				echo 'aa';
				$classes[] = $this->getNextClass($tokens, $i);
			}
		}
		
		return $classes;
	}
	
	private function getNextClass(array $tokens, $i) {
		for ($i; $i < count($tokens); $i++) {
			if ($tokens[$i][0] == T_STRING) {
				return $tokens[$i][1];
			}
		}
	}
}
		
