<?php
/**
 * Created by PhpStorm.
 * User: albert
 * Date: 16-4-21
 * Time: 下午10:53
 */

return [
    'server' => [
        'host' => '127.0.0.1',
        'port' => '9777'
    ],
    'drrr' => [
        'log_file' => __DIR__.'/log/drrr_chatroom.log',
        'worker_num' => 1,
        //不要修改这里
        'max_request'     => 0,
        'task_worker_num' => 1,
        //是否要作为守护进程
        'daemonize'       => 1,
    ]
];