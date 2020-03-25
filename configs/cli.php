<?php

require __ROOT__.'/configs/common.php';

$services['factory'] = Ypf\Application\Swoole::class;

$services['swoole'] = [
    'server' => $config->get('swoole.server'),
    'options' => $config->get('swoole.options', []) + [
        'task_worker_num' => 3,
        'dispatch_mode' => 1,
        'dispatch_func' => function ($serv, $fd, $type, $data) {
            $worker_id = mt_rand(1, $serv->setting['worker_num'] - 1);

            return $worker_id;
        },
    ],
];