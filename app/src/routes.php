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
		$isActive = (int)$request->getParsedBodyParam("IsActive", $default = 1);
		$name = $request->getParsedBodyParam("Name", $default = null);
		$city = $request->getParsedBodyParam("City", $default = "Spokane");
		$state = $request->getParsedBodyParam("State", $default = "Washington");
		$rating = $request->getParsedBodyParam("Rating", $default = "E");
		$description = $request->getParsedBodyParam("Description", $default = null);
		$numberOfLandmarks = (int)$request->getParsedBodyParam("NumberOfLandMarks", $default = 0);
		$collectionLength = (double)$request->getParsedBodyParam("CollectionLength", $default = 0);
		$isOrder = (int)$request->getParsedBodyParam("IsOrder", $default = 0);
		$picID = (int)$request->getParsedBodyParam("PicID", $default = 0);
		
		$conn = connect_db();	
		$stmt = $conn->prepare("INSERT INTO Collections (IsActive, Name, City, State, Rating, Description, NumberOfLandMarks, CollectionLength, IsOrder, PicID)
		VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
		$stmt->execute([$isActive, $name, $city, $state, $rating, $description, $numberOfLandmarks, $collectionLength, $isOrder, $picID]);
		$conn = null;
	});
	
	$app->post('/database/landmark', function(Request $request) {
		$name = $request->getParsedBodyParam("Name", $default = null);
		$long = (double)$request->getParsedBodyParam("Longitude", $default = 0);
		$lat = (double)$request->getParsedBodyParam("Latitude", $default = 0);
		$descID = (int)$request->getParsedBodyParam("DescID", $default = 0);
		$QRCode = $request->getParsedBodyParam("QRCode", $default = "{ Empty }");
		$picID = (int)$request->getParsedBodyParam("PicID", $default = 0);
		$cid = (int)$request->getParsedBodyParam("CID", $default = 0);
		$description = $request->getParsedBodyParam("Description", $default = "{ Empty }");
		
		$conn = connect_db();
		$stmt = $conn->prepare("INSERT INTO landmarks (Name, Longitude, Latitude, DescID, QRCode, PicID) VALUES (?, ?, ?, ?, ?, ?)");
		$stmt->execute([$name, $long, $lat, $descID, $QRCode, $picID]);
		
		$stmt = $conn->prepare("Select LID FROM Landmarks WHERE Landmarks.Name = ?");
		$stmt->execute([$name]);
		$result = $stmt->fetch();
		$lid = $result['LID'];
		
		echo $lid;
		$stmt = $conn->prepare("INSERT INTO CollectionLandmarks (CollectionID, LandmarkID) VALUES (?, ?)");
		$stmt->execute([$cid, $lid]);
		
		$stmt = $conn->prepare("INSERT INTO LandmarkDescription (DesID, LID, CID, Description) VALUES (?, ?, ?, ?)");
		$stmt->execute([$descID, $lid, $cid, $description]);
		
		$conn = null;
	});
	
	$app->get('/qrcode/{cid}', function (Request $request, Response $response){
		$conn = connect_db();
        $cid = (int)$request->getAttribute('cid');
        $stmt = $conn->prepare("SELECT Name FROM Collections WHERE Collections.CID = ?;");
        $stmt->execute([$cid]);
        $result = $stmt->fetch();
		$path = '../Resources/QRcodes/'.$cid.'.png';
		$name = $result['Name'];
		QRcode::png($name, $path);
		$image = file_get_contents($path);
		$response->write($image);
		return $response->withHeader('Content-Type', 'image/png');
    });
?>