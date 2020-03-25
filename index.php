<?php

define('__ROOT__', __DIR__);

require_once __ROOT__ . '/vendor/autoload.php';
require __ROOT__.'/configs/common.php';


$app = new Ypf\Application($services);

$app->run();
