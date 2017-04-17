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

    $app->get('/test', function($req, $res, $args){
        return $this->view->render($res, 'create.twig');
    });

    $app->post('/upload', function ($request, $response, $args) {
        $files = $request->getUploadedFiles();
        if (empty($files['newfile'])) {
            throw new Exception('Expected a newfile');
        }
    
        $newfile = $files['newfile'];
        if ($newfile->getError() === UPLOAD_ERR_OK) {
            $uploadFileName = $newfile->getClientFilename();
            $newfile->moveTo("../public_html/img/$uploadFileName");
        }
    });

    $app->get('/api/collection/', function (Request $request, Response $response){
        $ara = array();
        $conn = connect_db();
	    $output = $conn->query("SELECT * FROM Collections;");
        while($row = $output->fetch()) {
            array_push($ara, $row);
        }
        echo json_encode($ara);
        $conn = null;
    });

    $app->get('/api/collection/{wid}', function (Request $request, Response $response){
        $conn = connect_db();
        $wid = (int)$request->getAttribute('wid');
        $stmt = $conn->prepare("SELECT * FROM Collections WHERE CID = ?;");
        $stmt->execute([$wid]);
        $output = $stmt->fetch();
            echo json_encode($output);
        $conn = null;
    });

    $app->get('/api/landmark/{lid}', function (Request $request, Response $response){
        $conn = connect_db();
        $lid = (int)$request->getAttribute('lid');
        $stmt = $conn->prepare("SELECT * FROM Landmarks WHERE LID = ?;");
        $stmt->execute([$lid]);
        while($row = $stmt->fetch()) {
            echo json_encode($row);
        }
        $conn = null;
    });

    $app->get('/api/landmark/all/{wid}', function (Request $request, Response $response){
        $ara = array();
        $conn = connect_db();
        $wid = (int)$request->getAttribute('wid');
        $stmt = $conn->prepare("SELECT * FROM Landmarks INNER JOIN CollectionLandmarks ON CollectionLandmarks.LandmarkID = Landmarks.LID WHERE CollectionLandmarks.CollectionID = ?;");
        $stmt->execute([$wid]);
        while($row = $stmt->fetch()) {
            array_push($ara, $row);
        }
        echo json_encode($ara);
        $conn = null;
    });

    $app->get('/image/logo/{wid}', function (Request $request, Response $response){
        //$imgName = (string)$request->getAttribute('imageid');
        $conn = connect_db();
        $wid = (int)$request->getAttribute('wid');
        $stmt = $conn->prepare("SELECT FileLocation FROM CollectionImages INNER JOIN Collections ON Collections.PicID = CollectionImages.PicID WHERE Collections.CID = ?;");
        $stmt->execute([$wid]);
        $result = $stmt->fetch();
        $image = file_get_contents('../Resources/Images/' . $result['FileLocation']);
        $response->write($image);
        return $response->withHeader('Content-Type', 'image/png');
        //echo $image;
    });
	
	
	$app->post('/database/collection', function(Request $request){
		$cid = (int)$request->getParsedBodyParam("CID", $default = null);
		$isActive = (int)$request->getParsedBodyParam("IsActive", $default = null);
		$name = $request->getParsedBodyParam("Name", $default = null);
		$city = $request->getParsedBodyParam("City", $default = null);
		$state = $request->getParsedBodyParam("State", $default = null);
		$rating = $request->getParsedBodyParam("Rating", $default = null);
		$description = $request->getParsedBodyParam("Description", $default = null);
		$numberOfLandmarks = (int)$request->getParsedBodyParam("NumberOfLandMarks", $default = null);
		$collectionLength = (double)$request->getParsedBodyParam("CollectionLength", $default = null);
		$isOrder = (int)$request->getParsedBodyParam("IsOrder", $default = null);
		$picID = (int)$request->getParsedBodyParam("PicID", $default = null);
		
		$conn = connect_db();	
		$stmt = $conn->prepare("INSERT INTO collections (CID, IsActive, Name, City, State, Rating, Description, NumberOfLandMarks, CollectionLength, IsOrder, PicID)
		VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
		$stmt->execute([$cid, $isActive, $name, $city, $state, $rating, $description, $numberOfLandmarks, $collectionLength, $isOrder, $picID]);
		$conn = null;
	});
?>