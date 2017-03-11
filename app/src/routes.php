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
        $ara = array();
        $conn = connect_db();
	    $output = $conn->query("SELECT * FROM Walks;");
        while($row = $output->fetch()) {
            array_push($ara, $row);
        }
        echo json_encode($ara);
        $conn = null;

    $app->get('/api/walk/{wid}', function (Request $request, Response $response){
        $conn = connect_db();
        $wid = (int)$request->getAttribute('wid');
        $stmt = $conn->prepare("SELECT * FROM Walks WHERE WID = ?;");
        $stmt->execute([$wid]);
        while($row = $output->fetch()) {
            echo json_encode($row);
        }
        $conn = null;
    });

    $app->get('/api/landmark/{lid}', function (Request $request, Response $response){
        $conn = connect_db();
        $lid = (int)$request->getAttribute('lid');
        $stmt = $conn->prepare("SELECT * FROM LandMarks WHERE LID = ?;");
        $stmt->execute([$lid]);
        while($row = $output->fetch()) {
            echo json_encode($row);
        }
        $conn = null;
    });

    $app->get('/api/landmark/all/{wid}', function (Request $request, Response $response){
        $ara = array();
        $conn = connect_db();
        $wid = (int)$request->getAttribute('wid');
        $stmt = $conn->prepare("SELECT * FROM LandMarks INNER JOIN WalkLandMarks ON WalkLandMarks.LandMarkID = LandMarks.LID WHERE WalkLandMarks.WalkID = ?;");
        $stmt->execute([$wid]);
        while($row = $output->fetch()) {
            array_push($ara, $row);
        }
        echo json_encode($ara);
        $conn = null;
    });
?>