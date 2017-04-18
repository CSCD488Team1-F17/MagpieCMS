<?php
    use \Psr\Http\Message\ServerRequestInterface as Request;
    use \Psr\Http\Message\ResponseInterface as Response;
    
    //Helper functions
    function authCheck($path, $app, Request $request, Response $response, $args){
        
        session_start();

        $config = require dirname(__FILE__, 2) . '/config.php';
        
        $client = new Google_Client();
        $client->setAuthConfig($config->credentialsFile);

        if(isset($_SESSION['access_token']) && $_SESSION['access_token']){
            $client->setAccessToken($_SESSION['access_token']);
            return $app->view->render($response, $path);
        } else{
            return $response->withRedirect('/oauth2callback'); 
        }
    }

    // Routes
    $app->get('/', function (Request $request, Response $response, $args) {
        return $this->renderer->render($response, 'index.html', $args);
    });

    $app->get('/oauth2callback', function (Request $request, $response, $args) {
        session_start();
        $config = require dirname(__FILE__, 2) . '/config.php';

        $client = new Google_Client();
        $client->setAuthConfig($config->credentialsFile);
        $client->setRedirectUri('http://' . $_SERVER['HTTP_HOST'] . '/oauth2callback');
        $client->addScope(openid);

        $allGetVars = $request->getQueryParams();
        if (!array_key_exists('code', $allGetVars)) {
            $auth_url = $client->createAuthUrl();
            return $response->withRedirect($auth_url); 
        } else {
            $getCode = $allGetVars['code'];
            $client->authenticate($getCode);
            $_SESSION['access_token'] = $client->getAccessToken();
            return $response->withRedirect('/'); 
        }
    });

    $app->get('/dashboard', function(Request $request, Response $response, $args){
        authCheck('dashboard.twig', $this, $request, $response, $args);
    });
?>