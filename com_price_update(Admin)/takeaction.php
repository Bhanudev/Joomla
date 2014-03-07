<?php
define('_JEXEC', 1);
define('DS', DIRECTORY_SEPARATOR);
if (file_exists(dirname(__FILE__) . '/defines.php')) {
	include_once dirname(__FILE__) . '/defines.php';
}
if (!defined('_JDEFINES')) {
	define('JPATH_BASE', dirname(__FILE__));
	require_once JPATH_BASE.'/includes/defines.php';
}
require_once JPATH_BASE.'/includes/framework.php';
// Mark afterLoad in the profiler.
JDEBUG ? $_PROFILER->mark('afterLoad') : null;

$ids = "";
$ids = JRequest::getVar('id','', 'get');

$type = "";
$type = JRequest::getVar('type','', 'get');
if($type=="Unpublish"){$type=1;}
if($type=="Delete"){$type=2;}
echo $type."<br />";
print_r($ids);
?>
