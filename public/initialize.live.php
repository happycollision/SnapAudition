<?php

// Define the core paths
// Define them as absolute paths to make sure that require_once works as expected

// DIRECTORY_SEPARATOR is a PHP pre-defined constant
// (\ for Windows, / for Unix)
defined('DS') ? null : define('DS', DIRECTORY_SEPARATOR);

defined('SITE_ROOT') ? null : 
	define('SITE_ROOT', DS.'home'.DS.'happycol'.DS.'public_html'.DS.'snapaudition');

defined('LIB_PATH') ? null : define('LIB_PATH', DS.'home'.DS.'happycol'.DS.'happycollisionassets'.DS.'snapaudition');

defined('URL') ? null :define('URL', 'http://snapaudition.happycollision.com');

defined('TEMPLATE_PATH') ? null :define('TEMPLATE_PATH', SITE_ROOT.DS.'templates'.DS );

//setting timezone to central time for all users
date_default_timezone_set('America/New York');

// load config file first
require_once(LIB_PATH.DS.'config.php');

// load basic functions next so that everything after can use them
require_once(LIB_PATH.DS.'functions.php');

// load core objects
require_once(LIB_PATH.DS.'session.php');
require_once(LIB_PATH.DS.'database.php');
require_once(LIB_PATH.DS.'database_object.php');
require_once(LIB_PATH.DS.'pagination.php');

// load database table related classes
require_once(LIB_PATH.DS.'user.php');

?>