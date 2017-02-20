<HTML>
	<Body>
		<? php
			$data = json_decode('{ "foo": 1, "bar": [10, "apples"] }'); // dictionaries will be returned as objects
			$data2 = json_decode('{ "foo": 1, "bar": [10, "apples"] }', true); // dictionaries will be returned as arrays

			var_dump($data);
			echo "<br />";
			var_dump($data2);
			echo "<br />";

			$sample = array( "blue" => array(1,2), "ocean" => "water" );
			
			var_dump($sample);
			echo "<br />";

			$json_string = json_encode($sample);

			echo "$json_string";
		?>
	</Body>
</HTML>