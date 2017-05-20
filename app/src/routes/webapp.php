<?php
    use \Psr\Http\Message\ServerRequestInterface as Request;
    use \Psr\Http\Message\ResponseInterface as Response;

    //Helper functions
    function authCheck($path, $app, Request $request, Response $response, $args){
        error_log(print_r("in authCheck", TRUE));

        session_start();

        $config = require dirname(__FILE__, 2) . '/config.php';

        $client = new Google_Client();
        $client->setAuthConfig($config->credentialsFile);

        if(isset($_SESSION['access_token']) && $_SESSION['access_token']){
            error_log(print_r("access_token SET", TRUE));
            $client->setAccessToken($_SESSION['access_token']);
            return $app->view->render($response, $path, $args);
        } else{
            error_log(print_r("access_token NOT SET", TRUE));
            error_log(print_r($response, TRUE));
            return $response->withRedirect('/oauth2callback');
        }
    }

    function canEdit(Response $response, $cid){
        session_start();

        $config = require dirname(__FILE__, 2) . '/config.php';

        $client = new Google_Client();
        $client->setAuthConfig($config->credentialsFile);

        if(isset($_SESSION['access_token']) && $_SESSION['access_token']){
            error_log(print_r("access_token SET", TRUE));
            $client->setAccessToken($_SESSION['access_token']);
            $token = $client->getAccessToken();
            $client->setAccessToken($token);

            error_log(print_r("Checking token data...", TRUE));
            if($token_data = $client->verifyIdToken()){
                $userid = $token_data['sub'];

                $conn = connect_db();

                $stmt = $conn->prepare("SELECT * FROM WebUserData WHERE UID = ?;");
                $stmt->execute([$userid]);
                $output = $stmt->fetch();

                if($output['UID'] == $userid){
                    error_log(print_r("UID matches userID", TRUE));
                    error_log(print_r($output['UserID'], TRUE));
                    $stmt = $conn->prepare("SELECT * FROM UserMadeCollectionList WHERE (UserID = ? AND CollectionID = ?);");
                    $stmt->execute([$output['UserID'], $cid]);
                    if($stmt->fetch()){
                        return true;
                    } else {
                        return false;
                    }
                } else{
                    return false;
                }

            } else {
                return false;
            }
        } else{
            error_log(print_r("access_token NOT SET", TRUE));
            error_log(print_r($response, TRUE));
            if($response->withRedirect('/oauth2callback')){
                error_log(print_r("access_token SET", TRUE));
                $client->setAccessToken($_SESSION['access_token']);
                $token = $client->getAccessToken();
                $client->setAccessToken($token);

                error_log(print_r("Checking token data...", TRUE));
                if($token_data = $client->verifyIdToken()){
                    return true;
                } else {
                    return false;
                }
            }
        }
    }

    // Routes
    $app->get('/', function (Request $request, Response $response, $args) {
        return $this->renderer->render($response, 'index.html', $args);
    });
    $app->get('/login', function (Request $request, Response $response, $args) {
        return $this->renderer->render($response, 'login.html', $args);
    });

    $app->get('/oauth2callback', function (Request $request, $response, $args) {
        error_log(print_r("in oauth2callback", TRUE));
        session_start();
        $config = require dirname(__FILE__, 2) . '/config.php';

        $client = new Google_Client();
        $client->setAuthConfig($config->credentialsFile);
        $client->setRedirectUri('http://' . $_SERVER['HTTP_HOST'] . '/oauth2callback');
        $client->addScope(openid);

        $allGetVars = $request->getQueryParams();
        if (!array_key_exists('code', $allGetVars)) {
            $auth_url = $client->createAuthUrl();
            return $response->withRedirect($auth_url, 200);
        } else {
            $getCode = $allGetVars['code'];
            $client->authenticate($getCode);
            $_SESSION['access_token'] = $client->getAccessToken();
            return $response->withRedirect('/');
        }
    });

    $app->post('/tokensignin', function (Request $request, $response, $args) {
        error_log(print_r("in oauth2callback", TRUE));
        //session_start();
        $config = require dirname(__FILE__, 2) . '/config.php';

        $client = new Google_Client();
        $client->setAuthConfig($config->credentialsFile);
        $client->setRedirectUri('http://' . $_SERVER['HTTP_HOST'] . '/oauth2callback');
        $client->addScope(openid);

        $json = $request->getBody();
        $data = json_decode($json, true);

        $id_token = $data['id_token'];

        $payload = $client->verifyIdToken($id_token);
        if ($payload) {
            $userid = $payload['sub'];
            $conn = connect_db();

            $stmt = $conn->prepare("SELECT UID FROM WebUserData WHERE UID = ?;");
            $stmt->execute([$userid]);
            $output = $stmt->fetch();
            if($output['UID'] != $userid){
                $stmt = $conn->prepare("INSERT INTO WebUserData (UID) VALUES (?)");
                $stmt->execute([$userid]);
            }

            $conn = null;
            return $response->withStatus(200);
        } else {
            return $response->withStatus(300);
        }
    });

    $app->get('/collections', function(Request $request, Response $response, $args){
        return authCheck('collections.twig', $this, $request, $response, $args);
    });

    $app->get('/create', function(Request $request, Response $response, $args){
        $cid;
        $conn = connect_db();
        $output = $conn->query("SELECT MAX(CID) AS MaxCid FROM Collections;");
        while($row = $output->fetch()) {
            $cid = $row['MaxCid'];
        }
        $conn = null;

        return authCheck('create.twig', $this, $request, $response, ['cid'=> $cid]);
    });

    $app->get('/contact', function(Request $request, Response $response, $args){
        return authCheck('contact.twig', $this, $request, $response, $args);
    });

    $app->get('/legal', function(Request $request, Response $response, $args){
        return authCheck('legal.twig', $this, $request, $response, $args);
    });

    $app->get('/account', function(Request $request, Response $response, $args){
        return authCheck('account.twig', $this, $request, $response, $args);
    });

    $app->get('/landmarks/{cid}', function(Request $req, Response $response, $args){
        $numBadge;
        $ara = array();
        $araDesc = array();
        $cid = (int)$req->getAttribute('cid');
        $conn = connect_db();
        $stmt = $conn->prepare("SELECT NumberOfLandmarks FROM Collections WHERE CID = ?;");
        $stmt->execute([$cid]);
        $output = $stmt->fetch();
        $numBadge = $output['NumberOfLandmarks'];
        $stmt = $conn->prepare("SELECT Count(LID) AS count FROM Landmarks INNER JOIN CollectionLandmarks ON CollectionLandmarks.LandmarkID = Landmarks.LID WHERE CollectionLandmarks.CollectionID = ?;");
        $stmt->execute([$cid]);
        $output = $stmt->fetch();
        $landmarkCount = $output['count'];
        if($landmarkCount > 0){
            $stmt = $conn->prepare("SELECT * FROM Landmarks INNER JOIN CollectionLandmarks ON CollectionLandmarks.LandmarkID = Landmarks.LID WHERE CollectionLandmarks.CollectionID = ?;");
            $stmt->execute([$cid]);
            while($row = $stmt->fetch()) {
                array_push($ara, $row);
            }

            $stmt = $conn->prepare("SELECT * FROM LandmarkDescription INNER JOIN Landmarks ON Landmarks.LID = LandmarkDescription.LID WHERE LandmarkDescription.CID = ?");
            $stmt->execute([$cid]);
            while($row = $stmt->fetch()) {
                array_push($araDesc, $row);
            }

            $conn = null;
            return authCheck('landmarks.twig', $this, $req, $response, ['cid' => (int)$req->getAttribute('cid'), 'numBadge' => $numBadge, 'numCount' => $landmarkCount, 'incomingData' => 1, 'landmarks' => json_encode($ara), 'landmarkDesc' => json_encode($araDesc)]);
        }else{
            $conn = null;
            return authCheck('landmarks.twig', $this, $req, $response, ['cid' => (int)$req->getAttribute('cid'), 'numBadge' => $numBadge, 'numCount' => $landmarkCount, 'incomingData' => 0]);
        }

    });

    $app->get('/view/{cid}', function(Request $request, Response $response, $args){
        return authCheck('view.twig', $this, $request, $response, ['cid' => (int)$request->getAttribute('cid')]);
    });

	$app->get('/edit/{cid}', function(Request $request, Response $response, $args){
		$cid = (int)$request->getAttribute('cid');
        if(canEdit($response, $cid)){
            $conn = connect_db();
            $stmt = $conn->prepare("SELECT * FROM Collections WHERE CID = ?;");
            $stmt->execute([$cid]);
            $result = $stmt->fetch();
            $name = $result['Name'];
            $abbreviation = $result['Abbreviation'];
            $description = $result['Description'];
            $numberOfLandmarks = $result['NumberOfLandmarks'];
            $isOrder = $result['IsOrder'];
            
            return authCheck('edit.twig', $this, $request, $response, ['cid'=>$cid, 'name'=>$name, 'abbreviation'=>$abbreviation, 'description'=>$description, 
            'numberOfLandmarks'=>$numberOfLandmarks, 'isOrder'=>$isOrder]);
        } else {
            echo "hey!";
        }
	});	
?>