<?php
    use \Psr\Http\Message\ServerRequestInterface as Request;
    use \Psr\Http\Message\ResponseInterface as Response;

    // Routes
    $app->get('/', function (Request $request, Response $response, $args) {
        return $this->renderer->render($response, 'index.html', $args);
    });

    $app->get('/hello/{name}', function (Request $request, Response $response) {
        $name = $request->getAttribute('name');
        $response->getBody()->write("Hello, $name");

        return $response;
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
    });
?>