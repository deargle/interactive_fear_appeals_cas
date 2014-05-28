<?php
require '..\..\vendor\autoload.php';

$app = new \Slim\Slim();
$app->get('/getTip/password/:password/tipType/:tipType', function($password, $tipType) use ($app) {
    $url = "http://security.byu.edu/password-api.php?password={$password}&tipType={$tipType}";
    
    $ch = curl_init($url);
    
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    
    $response = curl_exec($ch);
    curl_close($ch);
    
    echo $response;
});

$app->get('/', function() {

    echo 'The API!';
    
});

$app->run();

?>