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
		$cid = $request->getParam('CID');
        if ($newfile->getError() === UPLOAD_ERR_OK) {
            $uploadFileName = $newfile->getClientFilename();
            $newfile->moveTo("../Resources/Images/$cid/$uploadFileName");
        }
    });

	$app->post('/upload/images/collection', function ($request, $response, $args) {
        $files = $request->getUploadedFiles();
        if (empty($files['newfile'])) {
            throw new Exception('Expected a newfile');
        }
		
        $newfile = $files['newfile'];
		$cid = $request->getParam('CID');
        if ($newfile->getError() === UPLOAD_ERR_OK) {
            $uploadFileName = $newfile->getClientFilename();
            $newfile->moveTo("../Resources/Images/$cid/$uploadFileName");
        }
		$imageType = substr($uploadFileName, strpos($uploadFileName, ".")+1);
		$conn = connect_db();
		$stmt = $conn->prepare("INSERT INTO CollectionImages (CID, FileLocation, ImageType) Values (?, ?, ?)");
		$stmt->execute([$cid, $uploadFileName, $imageType]);
		$conn = null;
    });

	$app->post('/upload/images/landmark', function ($request, $response, $args) {
        $files = $request->getUploadedFiles();
        if (empty($files['newfile'])) {
            throw new Exception('Expected a newfile');
        }
		
        $newfile = $files['newfile'];
		$lid = $request->getParam('LID');
		$cid = $request->getParam('CID');
        if ($newfile->getError() === UPLOAD_ERR_OK) {
            $uploadFileName = $newfile->getClientFilename();
            $newfile->moveTo("../Resources/Images/$cid/$uploadFileName");//unsure here
        }
		$imageType = substr($uploadFileName, strpos($uploadFileName, ".")+1);
		$conn = connect_db();
		$stmt = $conn->prepare("INSERT INTO LandmarkImages (CID, FileLocation, ImageType) Values (?, ?, ?)");
		$stmt->execute([$cid, $uploadFileName, $imageType]);
		$conn = null;
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

    $app->post('/api/user/app', function (Request $request, Response $response){
        $id_token = $request->getParam("id_token");
        error_log(print_r($idToken, TRUE));

        $config = require dirname(__FILE__, 2) . '/config.php';

        $client = new Google_Client();
        $client->setAuthConfig($config->credentialsFile);
        $client->setRedirectUri('http://' . $_SERVER['HTTP_HOST'] . '/oauth2callback');
        $client->addScope(openid);

        $payload = $client->verifyIdToken($id_token);
        if ($payload) {
            $userid = $payload['sub'];

            $conn = connect_db();

            $stmt = $conn->prepare("SELECT * FROM AppUserData WHERE UID = ?;");
            $stmt->execute([$userid]);
            $output = $stmt->fetch();

            if($output['UID'] != $userid){
                $stmt = $conn->prepare("INSERT INTO AppUserData (UID) VALUES (?)");
                $stmt->execute([$userid]);
            } else{
                echo "found em!";
            }
        } else {
            return $response->withStatus(300);
        }
        
        $conn = null;
        return $response->withStatus(200);
    });

    $app->post('/api/user/web', function (Request $request, Response $response){
        $json = $request->getBody();
        $data = json_decode($json, true);

        $id_token = $data['id_token'];
        error_log(print_r($id_token, TRUE));

        $config = require dirname(__FILE__, 2) . '/config.php';

        $client = new Google_Client();
        $client->setAuthConfig($config->credentialsFile);
        $client->setRedirectUri('http://' . $_SERVER['HTTP_HOST'] . '/oauth2callback');
        $client->addScope(openid);

        $payload = $client->verifyIdToken($id_token);
        if ($payload) {
            $userid = $payload['sub'];

            $conn = connect_db();

            $stmt = $conn->prepare("SELECT * FROM WebUserData WHERE UID = ?;");
            $stmt->execute([$userid]);
            $output = $stmt->fetch();

            if($output['UID'] == $userid){
                $conn = null;
                return $response->withJson($output);
            } else{
                return $response->withStatus(300);
            }
        } else {
            return $response->withStatus(300);
        }
    });

    $app->post('/api/user/web/collections', function (Request $request, Response $response){
        $json = $request->getBody();
        $data = json_decode($json, true);

        $userID = $data['UserID'];
        error_log(print_r($userID, TRUE));
        $conn = connect_db();

        $stmt = $conn->prepare("SELECT CollectionID FROM UserMadeCollectionList WHERE UserID = ?;");
        $stmt->execute([$userID]);
        
        $result = array();
        while($row = $stmt->fetch()) {
            $cid = $row['CollectionID'];
            $stmt2 = $conn->prepare("SELECT * FROM Collections WHERE CID = ?;");
            $stmt2->execute([$cid]);
            $output = $stmt2->fetch();
            array_push($result, $output);
        }

        return $response->withJson($result);
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
        $idToken = $request->getParam("idToken");
		//$picID = (int)superbadgeUpload($request);

        error_log(print_r($idToken, TRUE));
        
		
		$conn = connect_db();	
		$stmt = $conn->prepare("INSERT INTO Collections (Name, Description, NumberOfLandMarks, IsOrder) VALUES (?, ?, ?, ?)");
		$stmt->execute([$name, $description, $numberOfLandmarks, $isOrdered]);

        $picID = (int)superbadgeUpload($request);
        $stmt = $conn->prepare("UPDATE Collections SET PicID = ? WHERE CID = ?");
        $stmt->execute([$picID, $cid]);

        $conn = null;

        $config = require dirname(__FILE__, 2) . '/config.php';

        $client = new Google_Client();
        $client->setAuthConfig($config->credentialsFile);
        $client->setRedirectUri('http://' . $_SERVER['HTTP_HOST'] . '/oauth2callback');
        $client->addScope(openid);

        $payload = $client->verifyIdToken($idToken);
        if ($payload) {
            $userid = $payload['sub'];
            $conn = connect_db();

            $stmt = $conn->prepare("SELECT * FROM WebUserData WHERE UID = ?;");
            $stmt->execute([$userid]);
            $output = $stmt->fetch();
            error_log(print_r($output['UserID'], TRUE));
            if($output['UID'] == $userid){
                $stmt = $conn->prepare("INSERT INTO UserMadeCollectionList (UserID, CollectionID) VALUES (?, ?);");
                $stmt->execute([$output['UserID'], $cid]);
            }

            $conn = null;
        } else {
            return $response->withStatus(300);
        }

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
            $newfile->moveTo("../Resources/Images/$cid/$uploadFileName");
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
	
	$app->get('/images/collection/{cid}', function (Request $request, Response $response){
        $conn = connect_db();
		$cid = (int)$request->getAttribute('cid');
		$fileName = "Collection".$cid."Images.zip";
		
		//Folder name for collection images assumed to be just the CID
		$rootPath = realpath("../Resources/Images/$cid");
		$zip = new ZipArchive();
		if ($zip->open($fileName, ZIPARCHIVE::CREATE | ZIPARCHIVE::OVERWRITE) !== TRUE) {
			die ("An error occurred creating your ZIP file.");
		}
		
		$files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($rootPath), 
		RecursiveIteratorIterator::LEAVES_ONLY);
		
		foreach($files as $name => $file)
		{
			if(!$file->isDir())
			{
					$filePath = $file->getRealPath();
					$relativePath = substr($filePath, strlen($rootPath) + 1);
					$zip->addFile($filePath, 'Images/'.$relativePath);
			}	
		}
		$zip->close();
		readFile($fileName);
		unlink($fileName);
		$conn = null;
		return $response->withHeader("Content-Type", "application/zip")
		->withHeader("Content-Disposition", "attachment; filename=$fileName")
		->withHeader("Cache-Control", "no-store,no-cache");
    });
?>