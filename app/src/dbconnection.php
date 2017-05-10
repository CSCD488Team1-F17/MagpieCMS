<?php

function connect_db(){
	$connection = new PDO("mysql:host=$config->server;dbname=$config->database" ,$config->username, $config->password);
	$connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

	return $connection;
}

?>
