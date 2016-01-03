<?php
require_once("settings.php");

$script = dirname(__FILE__)."/scripts".$_SERVER['REDIRECT_URL'];

if(file_exists($script)){
	require_once($script);
}
else{
	echo "Not found!";
}