<?php

require '../vendor/autoload.php';

use Untitled\Authentication\Storage\EncryptedCookie;
use Untitled\Middleware\Authentication;
use Untitled\Middleware\HttpAuthentication;
use Untitled\Session;
use Slim\Extras\Views\Twig;
use Zend\Authentication\AuthenticationService;

defined('APPLICATION_ENV') || define('APPLICATION_ENV', 'development');
$config = require __DIR__ . '/../config.php';
Session::start($config['session']);

// Prepare app.
$app = new Slim\Slim($config['slim']);
$app->configureMode('development', function () {
	error_reporting(-1);
	ini_set('display_errors', 1);
	ini_set('display_startup_errors', 1);
});

// Prepare view
Twig::$twigOptions = $config['twig'];
$app->view(new Twig());

// If you are building a REST API, you may not
// care to use cookies.
$auth = new AuthenticationService();
$storage = new EncryptedCookie($app);
$auth->setStorage($storage);

// Set up the Doctrine Entity Manager.
$em = \Doctrine\ORM\EntityManager::create(
	array(
		'driver'   => $config['db'][APPLICATION_ENV]['driver'],
		'host'	   => $config['db'][APPLICATION_ENV]['host'],
		'port'	   => $config['db'][APPLICATION_ENV]['port'],
		'user'     => $config['db'][APPLICATION_ENV]['user'],
		'password' => $config['db'][APPLICATION_ENV]['password'],
		'dbname'   => $config['db'][APPLICATION_ENV]['dbname']
	),
	\Doctrine\ORM\Tools\Setup::createAnnotationMetadataConfiguration(
		array(__DIR__ . '/library'),
		APPLICATION_ENV == 'development',
		__DIR__ . '/../library/Proxies',
		new \Doctrine\Common\Cache\ArrayCache
	)
);

// Include our required UDFs.
require '../src/functions.php';

// Add any middleware.
$app->add(new Authentication($auth, $config));

// This function allows us to conditionally call this middleware
// only for API requests.
function APIRequest() {
	$app = \Slim\Slim::getInstance();
	$app->view(new \JsonApiView());
	$app->add(new \JsonApiMiddleware());
}

// http://docs.slimframework.com/#Not-Found-Handler
$app->notFound(function () use ($app) {
	echo '{"message":"Not found"}';
});

// Example html route.
$app->get('/', function () use ($app) {
    $app->render('index.html');
});

// Example api route.
$app->get('/api', 'APIRequest', function() use ($app) {
    $app->render(200, array(
        'hello' => 'world!',
    ));
});

// Declare your routes in separate files so that we can keep this
// file fairly tidy.
// require '../app/routes/login.php';

/**
 * Run the Slim application
 *
 * This method should be called last. This executes the Slim application
 * and returns the HTTP response to the client.
 */
$app->run();
