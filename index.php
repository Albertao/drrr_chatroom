<?php
$server = new swoole_websocket_server('0.0.0.0', 9777);
$redis = new Redis();
$redis->connect('127.0.0.1', 6379);
$users = $redis->sMembers('users');

//开启连接时进行房间广播并将用户存入redis集合中
$server->on('open', function(swoole_websocket_server $server, $request) use ($redis){
    $redis->sAdd('users', $request->fd);
    echo "zxczxc";

});

//接收信息时对房间进行广播
$server->on('message', function(swoole_websocket_server $server, $frame) use ($users){
    $data['msg'] = json_decode($frame->data)['msg'];
    $data['status'] = 1;
    foreach ($users as $user) {
        if ($user != $frame->fd) {
            $server->push($user, $data);
        }
    }
});

$server->on('close', function(swoole_websocket_server $server, $fd) use($users){
    foreach ($users as $user) {
        if($user != $fd){
            $server->push($user, "{$fd} exit");
        }
    }
});

$server->start();