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
        $ara = array();
        $conn = connect_db();
	    $output = $conn->query("SELECT * FROM Walks;");
        while($row = $output->fetch()) {
            array_push($ara, $row);
        }
        echo json_encode($ara);
        $conn = null;

    $app->get('/api/walk/{cid}', function (Request $request, Response $response){
        $conn = connect_db();
        $cid = (int)$request->getAttribute('cid');
        $stmt = $conn->prepare("SELECT * FROM Walks WHERE WID = ?;");
        $stmt->execute([$cid]);
        while($row = $stmt->fetch()) {
            echo json_encode($row);
        }
        $conn = null;
    });

    $app->get('/api/landmark/{lid}', function (Request $request, Response $response){
        $conn = connect_db();
        $lid = (int)$request->getAttribute('lid');
        $stmt = $conn->prepare("SELECT * FROM LandMarks WHERE LID = ?;");
        $stmt->execute([$lid]);
        while($row = $stmt->fetch()) {
            echo json_encode($row);
        }
        $conn = null;
    });

    $app->get('/api/landmark/all/{cid}', function (Request $request, Response $response){
        $ara = array();
        $conn = connect_db();
        $cid = (int)$request->getAttribute('cid');
        $stmt = $conn->prepare("SELECT * FROM LandMarks INNER JOIN WalkLandMarks ON WalkLandMarks.LandMarkID = LandMarks.LID WHERE WalkLandMarks.WalkID = ?;");
        $stmt->execute([$cid]);
        while($row = $stmt->fetch()) {
            array_push($ara, $row);
        }
        echo json_encode($ara);
        $conn = null;
    });

    $app->get('/image/test', function (Request $request, Response $response){
        $image = file_get_contents('../Resources/Images/test.jpg');
        $response->write($image);
        return $response->withHeader('Content-Type', 'image/jpg');
    });
?>