<?php
// print_r(apache_get_modules());
// echo "<pre>"; print_r($_SERVER); die;
// $_SERVER["REQUEST_URI"] = str_replace("/phalt/","/",$_SERVER["REQUEST_URI"]);
// $_GET["_url"] = "/";
use Phalcon\Di\FactoryDefault;
use Phalcon\Loader;
use Phalcon\Mvc\View;
use Phalcon\Mvc\Application;
use Phalcon\Url;
use Phalcon\Db\Adapter\Pdo\Mysql;
use Phalcon\Config;
use Phalcon\Mvc\Router;

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

$loader->register();

$container = new FactoryDefault();

// $container->set(
//     'view',
//     function () {
//         $view = new View();
//         $view->setViewsDir(APP_PATH . '/views/');
//         return $view;
//     }
// );

$container->set(
    'url',
    function () {
        $url = new Url();
        $url->setBaseUri('/');
        return $url;
    }
);


$container->set(
    'router',
    function () {
        $router = new Router();

        $router->setDefaultModule('front');

        $router->add(
            '/login',
            [
                'module'     => 'back',
                'controller' => 'login',
                'action'     => 'index',
            ]
        );

        // $router->add('/admin', array(
        //     'module' => "back",
        //     'action' => "index",
        //     'params' => "index"
        // ));

        $router->add('/admin/:controller', array(
            'module' => "back",
            'controller' => 1,
            'action' => "index"
        ));

        $router->add('/admin/:controller/:action/', array(
            'module' => "back",
            'controller' => 1,
            'action' => 2
        ));

        $router->add('/admin/:controller/:action/:params', array(
            'module' => "back",
            'controller' => 1,
            'action' => 2,
            'params' => 3
        ));

        return $router;
    }
);

$application = new Application($container);

$application->registerModules(
    [
        'front' => [
            'className' => \Multi\Front\Module::class,
            'path'      => APP_PATH . '/front/Module.php',
        ],
        'back'  => [
            'className' => \Multi\Back\Module::class,
            'path'      => APP_PATH . '/back/Module.php',
        ]
    ]
);

// $container->set(
//     'db',
//     function () {
//         return new Mysql(
//             [
//                 'host'     => 'localhost',
//                 'username' => 'root',
//                 'password' => '',
//                 'dbname'   => 'phalt',
//                 ]
//             );
//         }
// );

$container->set(
    'mongo',
    function () {
        $mongo = new MongoClient();

        return $mongo->selectDB('phalt');
    },
    true
);

try {
    // Handle the request
    $response = $application->handle(
        $_SERVER["REQUEST_URI"]
    );

    $response->send();
} catch (\Exception $e) {
    echo "<pre>";
    print_r($e);
}
