<? PHP
	function connectToDatabase($host, $user, $password)
	{
		$dbConn = mysql_connect($host, $user, $password);
		
		if(! $dbConn)
		{
			die('Connection Error' . mysql_error());
		}
		
		echo 'Connection Successfully';
		
		return $dbConn;
	}
?>