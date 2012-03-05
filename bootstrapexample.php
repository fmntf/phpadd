<?php

/**
 * Auto-loads a class file. Do not call this directly!
 *
 * @param string $class
 */
function autoloader($class)
{
	$bases = array(
		'/home/francesco/project/library/',
	);

	foreach ($bases as $base)
	{
		$path = $base . str_replace('_', '/', $class) . '.php';
		$path = $base . str_replace('\\', '/', $class) . '.php';

		if (is_file($path)) {
			require_once $path;
			return true;
		}
	}
}

spl_autoload_register('autoloader');
