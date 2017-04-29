<?php
    use \Psr\Http\Message\ServerRequestInterface as Request;
    use \Psr\Http\Message\ResponseInterface as Response;

    $config = require dirname(__FILE__, 2) . '/config.php';

    // Helper functions
    function connect_db(){
        $config = require dirname(__FILE__, 2) . '/config.php';
        $connection = new PDO("mysql:host=$config->server;dbname=$config->database" ,$config->username, $config->password);
        $connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        return $connection;
    }

    //api calls

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
	    $output = $conn->query("SELECT * FROM Collections WHERE STATUS = 1;");
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
		$cid =(int)$request->getParam("cid") + 1;
		$name = $request->getParam("name");
		$description = $request->getParam("summary");
		$numberOfLandmarks = (int)$request->getParam("numBadge");
        $isOrdered = (int)$request->getParam("ordered");
		//$picID = (int)superbadgeUpload($request);
		
		$conn = connect_db();	
		$stmt = $conn->prepare("INSERT INTO Collections (Name, Description, NumberOfLandMarks, IsOrder) VALUES (?, ?, ?, ?)");
		$stmt->execute([$name, $description, $numberOfLandmarks, $isOrdered]);

        $picID = (int)superbadgeUpload($request);
        $stmt = $conn->prepare("UPDATE Collections SET PicID = ? WHERE CID = ?");
        $stmt->execute([$picID, $cid]);
        echo("success");
	});

    // $app->post('/database/user', function(Request $request){
    //     $params = $request->getParsedBody();
    //     $id_token = $params['id_token']; 
	// });

    function superbadgeUpload($request){
        $files = $request->getUploadedFiles();
        $cid = (int)$request->getParam("cid") + 1;
        $pid;
        if (empty($files['newfile'])) {
            throw new Exception('Expected a newfile');
        }
    
        $newfile = $files['newfile'];
        if ($newfile->getError() === UPLOAD_ERR_OK) {
            $uploadFileName = $newfile->getClientFilename();
            $newfile->moveTo("../public_html/img/$uploadFileName");
        }

        $conn = connect_db();
        $stmt = $conn->prepare("INSERT INTO CollectionImages (CID, FileLocation) VALUES (?,?)");
        $stmt->execute([$cid, $uploadFileName]);
        $output = $conn->query("SELECT MAX(PicID) AS MaxPid FROM CollectionImages;");
        while($row = $output->fetch()) {
            $pid = $row['MaxPid'];
        }
        $conn = null;

        return $pid;
    }
?>