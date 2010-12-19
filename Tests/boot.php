<?php

set_include_path(dirname(__FILE__)."/.." . PATH_SEPARATOR . get_include_path());

spl_autoload_register("autoloader");

function autoloader($class) {
	$path = str_replace('_', '/', $class).'.php';
	require_once($path);
}