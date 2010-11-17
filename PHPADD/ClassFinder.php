<?php

class PHPADD_ClassFinder
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
