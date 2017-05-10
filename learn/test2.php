<HTML>
	<Body>
		<? PHP
			$set = array(5, 8, 9, 2, 3);
	
			echo "~Before sort~ <br />";

			foreach($set as $value)
			{
				echo "Value is $value <br />";
			}

			asort($set);

			echo "~After sort~ <br />";

			foreach($set as $value)
			{
				echo "Value is $value <br />";
			}
			
			$set[0] = "able";
			$set[1] = "baker";
			$set[2] = "char";
			$set[3] = "dog";
			$set[4] = "echo";
			
			foreach($set as $value)
			{
				echo "Value is $value <br />";
			}
		?>
	</Body>
</HTML>