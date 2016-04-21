<?php
//开启debug模式
define('DEBUG', 'on');
define('WEBPATH', __DIR__);

require WEBPATH.'/vendor/autoload.php';

//swoole framework init
Swoole\Loader::vendor_init();

Swoole\Loader::addNameSpace('drrr', __DIR__.'server_side');

$config = require WEBPATH.'/config.php';

$drrr_server = new drrr\server;
$drrr_server->loadSetting($config);

$serv = new Swoole\Network\Server($config['server']['host'], $config['server']['port']);
$serv->setProtocol($drrr_server);
$serv->run($config['drrr']);