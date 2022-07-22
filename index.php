<?php

require_once 'vendor/autoload.php';

$dispatcher = FastRoute\simpleDispatcher(function(FastRoute\RouteCollector $r) {
    $r->addRoute('GET', '/', 'App\Controllers\CryptoAssetController@index');
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
        print_r("404 Not Found");
        break;
    case FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
        $allowedMethods = $routeInfo[1];
        print_r("405 Method Not Allowed");
        break;
    case FastRoute\Dispatcher::FOUND:
        $handler = $routeInfo[1];
        $vars = $routeInfo[2];

        [$controller, $method] = explode("@", $handler);

        $loader = new \Twig\Loader\FilesystemLoader('app/Views');
        $twig = new \Twig\Environment($loader);

        $view = (new $controller)->$method();

        $template = $twig->load($view->getTemplatePath());
        echo $template->render($view->getData());

        break;
}