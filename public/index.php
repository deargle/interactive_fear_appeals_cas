<?php
require '.\..\vendor\autoload.php';

# Session cookie management
$domain = $_SERVER['HTTP_HOST'];

if (strpos($_SERVER['HTTP_HOST'],'byu.edu') !== false) {
    $cookie_domain = '.byu.edu';
} else {
    $cookie_domain = $domain;
}

session_cache_limiter(false);
session_set_cookie_params(60*45,'/', $cookie_domain, false, false);
session_start();

/**
 * Identify the four treatments... 
 */
if (!isset($_SESSION['treatment'])){
    $_SESSION['treatment'] = rand(0,3);
}

$app = new \Slim\Slim(array(
    'templates.path' => '.\..\templates',
    'view' => new \Slim\Views\Twig(),
    'mode' => 'development'
));

$app->configureMode('development', function() use ($app) {
    $app->config(array(
        'cas.link' => '/cas'
        ));
});

$app->configureMode('production', function() use ($app){
    $app->config(array(
        'cas.link' => 'http://cas.byu.edu/cas'
    ));
});

$app->group('/api', function() use ($app) {
    
    $app->get('/getTip/password/:password/tipType/:tipType', function($password, $tipType) use ($app) {
        $url = "http://security.byu.edu/password-api.php?password={$password}&tipType={$tipType}";

        $ch = curl_init($url);

        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        $response = curl_exec($ch);
        curl_close($ch);

        $app->response->headers->set('Content-Type', 'application/json');

        $response_array = json_decode($response, true);

        $morphed_response = array('status'=>'success', 'response'=>$response_array);

        echo json_encode($morphed_response);
    });
    
});

$app->group('/cas', function() use ($app) {
    
    $app->get('/', function() use ($app) {
        $app->render('cas/sign-in.html');
    });

    $app->get('/update', function() use ($app) {
        $app->render('cas/update.html');
    });
});

$app->get('/', function() use ($app) {
    $app->render('index.html', array('link' => $app->config('cas.link')));
});


$app->run();

?>