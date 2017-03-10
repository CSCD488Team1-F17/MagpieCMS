<?php
    use \Psr\Http\Message\ServerRequestInterface as Request;
    use \Psr\Http\Message\ResponseInterface as Response;

    // Routes
    $app->get('/', function (Request $request, Response $response, $args) {
        return $this->renderer->render($response, 'index.html', $args);
    });

    $app->get('/login', function (Request $request, Response $response, $args) {
        return $this->renderer->render($response, 'login.html', $args);
    });

    $app->get('/login-success', function (Request $request, Response $response, $args) {
        echo "success!";
    });

    $app->get('/api/all', function (Request $request, Response $response){
        $conn = connect_db();
	    $output = $conn->query("SELECT * FROM Walks;");
	    if ($output->num_rows > 0) {
    	    while($row = $output->fetch_assoc()) {
        	    echo json_encode($row);
    	    }
	    } else {
   		    echo "0 results";
	    }
        $conn->close();
    });

    $app->get('/api/walk/{wid}', function (Request $request, Response $response){
        $conn = connect_db();
        $stmt = $conn->prepare("SELECT * FROM Walks WHERE WID = ?");
        $stmt->bind_param("i", $request->getAttribute('wid'));
        $output = $stmt->execute();
        if ($output->num_rows > 0) {
    	    while($row = $output->fetch_assoc()) {
        	    echo json_encode($row);
    	    }
	    } else {
   		    echo "0 results";
	    }
        $stmt->close();
        $conn->close();
    });

    $app->get('/api/landmark/{lid}', function (Request $request, Response $response){
        $conn = connect_db();
        $stmt = $conn->prepare("SELECT * FROM LandMarks WHERE LID = ?");
        $stmt->bind_param("i", $request->getAttribute('lid'));
        $output = $stmt->execute();
        if ($output->num_rows > 0) {
    	    while($row = $output->fetch_assoc()) {
        	    echo json_encode($row);
    	    }
	    } else {
   		    echo "0 results";
	    }
        $stmt->close();
        $conn->close();
    });

    $app->get('/api/landmark/all/{wid}', function (Request $request, Response $response){
        $conn = connect_db();
        $stmt = $conn->prepare("SELECT * FROM LandMarks INNER JOIN WalkLandMarks ON WalkLandMarks.LandMarkID = LandMarks.LID WHERE WalkLandMarks.WalkID = ?");
        $stmt->bind_param("i", $request->getAttribute('wid'));
        $output = $stmt->execute();
        if ($output->num_rows > 0) {
    	    while($row = $output->fetch_assoc()) {
        	    echo json_encode($row);
    	    }
	    } else {
   		    echo "0 results";
	    }
        $stmt->close();
        $conn->close();
    });
	
	
	$app->post('/api/landmark', function(Request $request)
	{
		$parsedBody = $request->getParsedBody();
		$lid = json_decode($parsedBody["Lid"], true);
		$name = json_decode($parsedBody["Name"], true);
		$long = json_decode($parsedBody["Longitude"], true);
		$lat = json_decode($parsedBody["Latitude"], true);
		$walkCount = json_decode($parsedBody["NumberOfWalks"], true);
		$description = json_decode($parsedBody["Description"], true);
		
		//store landmark in DB
		$conn = connect_db();
		$stmt = $conn->prepare("INSERT INTO LandMarks (Lid, Name, Longitude, Latitude, NumberOfWalks, Description) VALUES ($lid, $name, $long, $lat, $walkCount, $description)");
		$stmt->execute();
		$stmt->close();
		$conn->close();
	});
?>