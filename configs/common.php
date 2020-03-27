<?php

use Symfony\Component\Yaml\Yaml;

if (file_exists(__ROOT__ . '/.env')) {
    $ini_array = parse_ini_file(__ROOT__ . '/.env');
    foreach ($ini_array as $k => $v) {
        putenv("$k=$v");
    }
}

$configDir = [__DIR__ . '/common/'];
if (!getenv('ENVIRONMENT')) {
    $logger->warn('The environment variable "ENVIRONMENT" has not been set yet. Please set it in php.ini');
} else {
    $configDir[] = __DIR__ . '/' . getenv('ENVIRONMENT');
}

$config = new Noodlehaus\Config($configDir);

date_default_timezone_set($config->get('app.timezone'));

$logger = new Monolog\Logger($config->get('log.id'));
$logger->pushProcessor(new Monolog\Processor\PsrLogMessageProcessor(null, true));
$logger->pushHandler(new Monolog\Handler\StreamHandler($config->get('log.path'), (getenv('DEBUG') ? Monolog\Logger::DEBUG : Monolog\Logger::WARNING)));

$services = [
];

$services['logger'] = $logger;

$services['config'] = $config;

$services['db'] = function () use ($config) {
    $db = new Ypf\Database\Connection($config->get('db'));

    return $db;
};

$services['view'] = function () {
    $smarty = new Smarty();
    $smarty->setTemplateDir(__ROOT__ . '/module');
    $smarty->setCacheDir(__ROOT__ . '/runtime/templates_cache');
    $smarty->setCompileDir(__ROOT__ . '/runtime/templates_compile');
    $smarty->left_delimiter = '<!--{';
    $smarty->right_delimiter = '}-->';
    $view = new \App\View\View($smarty);

    return $view;
};

$services['request'] = new Ypf\Http\Request;
$services['response'] = new Ypf\Http\Response(new GuzzleHttp\Psr7\Response);

$router = new Ypf\Route\Router();
$it = new \DirectoryIterator(__ROOT__ . '/module');
foreach ($it as $fi) {
    if ($fi->isFile() || $fi->isDot()) {
        continue;
    }

    $module_path = $fi->getPath() . "/" . $fi->getFilename();
    $info_filename = $module_path . "/info.yml";
    if (file_exists($info_filename)) {
        $infos = Yaml::parseFile($info_filename);
        switch ($infos['type']) {
            case 'module':
                if (file_exists($module_path . '/func.php')) {
                    require_once $module_path . '/func.php';
                }
                break;

        }
    }

    $router_filename = $module_path . "/router.yml";
    if (file_exists($router_filename)) {
        $routes = Yaml::parseFile($router_filename);
        foreach ($routes as $route) {
            $router->map($route['method'], $route['pattern'], $route['controller']);
        }
    }

}

$services['middleware'] = [
    // new Tuupola\Middleware\JwtAuthentication([
    //     "secure" => false,
    //     "attribute" => "jwt",
    //     "path" => ["/console"],
    //     "ignore" => ["/console/account/signup", "/console/account/signin"],
    //     "error" => function ($response, $arguments) {
    //         $data["success"] = false;
    //         $data["code"] = 4010;
    //         $data["message"] = $arguments["message"];
    //         return $response
    //             ->withHeader("Content-Type", "application/json")
    //             ->withHeader('Access-Control-Allow-Origin', '*')
    //             ->withHeader("Access-Control-Allow-Headers", "Origin, X-Requested-With, Content-Type, Accept")
    //             ->withBody(GuzzleHttp\Psr7\stream_for(json_encode($data, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT)));
    //     },
    //     "secret" => getenv("JWT_SECRET")
    // ]),
    // new App\Middleware\Jwt,
    new Ypf\Route\Middleware($router),
];
