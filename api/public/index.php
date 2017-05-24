<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

use Zend\Expressive\Application;
use Zend\Expressive\Helper\BodyParams\BodyParamsMiddleware;
use Zend\Expressive\Helper\UrlHelperMiddleware;
use App\Middleware;

// Delegate static file requests back to the PHP built-in webserver
if (php_sapi_name() === 'cli-server'
    && is_file(__DIR__ . parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH))
) {
    return false;
}

chdir(dirname(__DIR__));
require 'vendor/autoload.php';

/** @var \Interop\Container\ContainerInterface $container */
$container = require 'config/container.php';

/** @var \Zend\Expressive\Application $app */
$app = $container->get(Application::class);

$app->pipe(BodyParamsMiddleware::class);
$app->pipeRoutingMiddleware();
$app->pipe(UrlHelperMiddleware::class);
$app->pipeDispatchMiddleware();

// Routes
$app->get('/api/ping', Middleware\Ping::class, 'api.ping');
$app->post('/api/login', Middleware\User::class, 'api.user.post');

/** These were the default skeleton calls, we don't need them for this assignment
$app->get('/api/user[/{id:\d+}]', Middleware\User::class, 'api.user.get');
$app->post('/api/user', Middleware\User::class, 'api.user.post');
$app->patch('/api/user/{id:\d+}', Middleware\User::class, 'api.user.patch');
$app->delete('/api/user/{id:\d+}', Middleware\User::class, 'api.user.delete');
/**/

// Appointment CRUD calls
$app->post('/api/appointment', Middleware\Appointment::class, 'api.appointment.post');
$app->get('/api/appointment[/{id:\d+}]', Middleware\Appointment::class, 'api.appointment.get');
$app->patch('/api/appointment/{id:\d+}', Middleware\Appointment::class, 'api.appointment.patch');
$app->delete('/api/appointment/{id:\d+}', Middleware\Appointment::class, 'api.appointment.delete');

$app->run();
