<?php
use Phalcon\Di\FactoryDefault;
use Phalcon\Loader;
use Phalcon\Mvc\View;
use Phalcon\Mvc\Application;
use Phalcon\Url;
use Phalcon\Db\Adapter\Pdo\Mysql;
use Phalcon\Config;
use Phalcon\Events\Manager as EventsManager;
use App\Handlers\Listener;
use handler\Aware\Aware;


$config = new Config([]);

// Define some absolute path constants to aid in locating resources
define('BASE_PATH', dirname(__DIR__));
define('APP_PATH', BASE_PATH . '/app');

// Register an autoloader
$loader = new Loader();

$loader->registerDirs(
    [
        APP_PATH . "/controllers/",
        APP_PATH . "/models/",
    ]
);
$loader->registerNamespaces([
    'App\Handlers' => APP_PATH . '/handlers/',
    'handler\Aware' => APP_PATH . '/handlers/',
]);

$loader->register();

$container = new FactoryDefault();

$container->set(
    'view',
    function () {
        $view = new View();
        $view->setViewsDir(APP_PATH . '/views/');
        return $view;
    }
);

$container->set(
    'url',
    function () {
        $url = new Url();
        $url->setBaseUri('/');
        return $url;
    }
);

$eventsManager = new EventsManager();

$container->set(
    'customEvent',
    function () {
        $eventsManager = new EventsManager();
        $component = new Aware();

        $component->setEventsManager($eventsManager);
        $eventsManager->attach(
            'application:escape',
            new Listener()
        );
        return $component->process();
    }
);
$container->set(
    'EventsManager',
    $eventsManager
);
$application = new Application($container);
$application->setEventsManager($eventsManager);

$container->set(
    'db',
    function () {
        return new Mysql(
            [
                'host' => 'mysql-server',
                'username' => 'root',
                'password' => 'secret',
                'dbname' => 'firstDB',
            ]
        );
    }
);

try {
    // Handle the request
    $response = $application->handle(
        $_SERVER["REQUEST_URI"]
    );
    $response->send();
} catch (\Exception $e) {
    echo 'Exception: ', $e->getMessage();
}