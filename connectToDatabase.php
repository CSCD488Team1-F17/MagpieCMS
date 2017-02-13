<? PHP
	function connectToDatabase()
	{
		$host = 'CMSdatabase.db';
		$user = 'admin';
		$password = 'password';
		$dbConn = mysql_connect($host, $user, $password);
		
		if(! $dbConn)
		{
			die('Connection Error' . mysql_error());
		}
		
		echo 'Connection Successfully';
		
		return $dbConn;
	}
?>