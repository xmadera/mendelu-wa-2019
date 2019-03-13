<?php
// DIC configuration

use Latte\Engine;
use Latte\Loaders\FileLoader;
use Ujpef\LatteView;

$container = $app->getContainer();

// view renderer

$container['view'] = function ($container) use ($settings) {
    //Create instance of Latte engine and configure path to cache files

    $engine = new Engine();
    $engine->setLoader(new FileLoader(__DIR__ . '/../templates/'));
    $engine->setTempDirectory(__DIR__ . '/../cache');

    //configure Latte wrapper and return it
    $latteView = new LatteView($engine);
    $latteView->addParam('router', $container->router);
    //define the {link} macro to generate URLs in templates from route names
    $latteView->addMacro('link', function (MacroNode $node, PhpWriter $writer) {
        if (strpos($node->args, ' ') !== false) {
            return $writer->write("echo \$router->pathFor(%node.word, %node.args);");
        } else {
            return $writer->write("echo \$router->pathFor(%node.word);");
        }
    });
    return $latteView;
};

// monolog
$container['logger'] = function ($c) {
    $settings = $c->get('settings')['logger'];
    $logger = new Monolog\Logger($settings['name']);
    $logger->pushProcessor(new Monolog\Processor\UidProcessor());
    $logger->pushHandler(new Monolog\Handler\StreamHandler($settings['path'], $settings['level']));
    return $logger;
};

// database
$container['db'] = function ($c) {
    $db = $c['settings']['db'];
    //connect to database
    $pdo = new PDO($db['dbtype'] . ":host=" . $db['dbhost'] . ";dbname=" . $db['dbname'],
        $db['dbuser'],
        $db['dbpass']);
    //define error mode -> we want to throw exceptions
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    //define how should fetch() and fetchAll() work
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    //configure character set for database communication - everything is UTF-8
    $pdo->query("SET NAMES 'utf8'");
    return $pdo;
};