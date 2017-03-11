<?php

function connect_db(){
	$server = "localhost";
	$user = "magpiehu_admin";
	$pass = "";
	$database = "magpiecms";
	$connection = new PDO("mysql:host=$server;dbname=$database" ,$user, $pass);
	$connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	return $connection;
}

?>