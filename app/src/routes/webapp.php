<?php
    // Routes
    $app->get('/', function (Request $request, Response $response, $args) {
        return $this->renderer->render($response, 'index.html', $args);
    });

    $app->get('/oauth2callback', function (Request $request, Response $response, $args) {
        session_start();

        $client = new Google_Client();
        $client->setAuthConfig($conifg->credentialsFile);
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

    $app->get('/dashboard', function($req, $response, $args){
        session_start();
        
        $client = new Google_Client();
        $client->setAuthConfig($conifg->credentialsFile);

        if(isset($_SESSION['access_token']) && $_SESSION['access_token']){
            $client->setAccessToken($_SESSION['access_token']);
            return $this->view->render($response, 'dashboard.twig');
        } else{
            return $response->withRedirect('/oauth2callback'); 
        }
    });
?>