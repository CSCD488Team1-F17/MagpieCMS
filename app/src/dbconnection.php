<?php

function connect_db(){
	$server = "localhost";
	$user = "magpiehu_admin";
	$pass = "";
	$database = "magpiehu_cmsdb";
	$connection = new mysqli($server, $user, $pass, $database);

	return $connection;
}

?>
