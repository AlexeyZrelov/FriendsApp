<?php

use App\Controllers\UsersController;
use App\Controllers\ArticlesController;
use App\View;
use App\Redirect;
use \Twig\Environment;
use \Twig\Loader\FilesystemLoader;

require_once 'vendor/autoload.php';

$dispatcher = FastRoute\simpleDispatcher(function(FastRoute\RouteCollector $r) {

    $r->addRoute('GET', '/users', [UsersController::class, 'index']);
    $r->addRoute('GET', '/users/{id:\d+}', [UsersController::class, 'show']);

    // Articles
    $r->addRoute('GET', '/articles', [ArticlesController::class, 'index']);
    $r->addRoute('GET', '/articles/{id:\d+}', [ArticlesController::class, 'show']);
    $r->addRoute('POST', '/articles', [ArticlesController::class, 'store']);
    $r->addRoute('GET', '/articles/create', [ArticlesController::class, 'create']);
    $r->addRoute('POST', '/articles/{id:\d+}/delete', [ArticlesController::class, 'delete']);

    $r->addRoute('GET', '/articles/{id:\d+}/edit', [ArticlesController::class, 'edit']);
    $r->addRoute('POST', '/articles/{id:\d+}', [ArticlesController::class, 'update']);

    // Registration


});

// Fetch method and URI from somewhere
$httpMethod = $_SERVER['REQUEST_METHOD'];
$uri = $_SERVER['REQUEST_URI'];

// Strip query string (?foo=bar) and decode URI
if (false !== $pos = strpos($uri, '?')) {
    $uri = substr($uri, 0, $pos);
}
$uri = rawurldecode($uri);

$routeInfo = $dispatcher->dispatch($httpMethod, $uri);
switch ($routeInfo[0]) {
    case FastRoute\Dispatcher::NOT_FOUND:
        // ... 404 Not Found
        var_dump('404 Not Found');
        break;
    case FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
        $allowedMethods = $routeInfo[1];
        // ... 405 Method Not Allowed
        var_dump('405 Method Not Allowed');
        break;
    case FastRoute\Dispatcher::FOUND:
        $controller = $routeInfo[1][0];
        $method = $routeInfo[1][1];

       /** @var View $response */
        $response = (new $controller)->$method($routeInfo[2]);

        $twig = new Environment(new FilesystemLoader('app/Views'));


        if ($response instanceof View) {
            echo $twig->render($response->getPath(), $response->getVariables());
        }

        if ($response instanceof Redirect) {
            header('Location: ' . $response->getLocation());
            exit;
        }

        break;

}

