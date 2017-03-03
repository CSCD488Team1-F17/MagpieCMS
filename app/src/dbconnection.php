<?php

function connect_db(){
	$server = "localhost";
	$user = "root";
	$pass = "supersecretpw2016";
	$database = "magpiecms";
	$connection = new mysqli($server, $user, $pass, $database);

	return $connection;
}

?>