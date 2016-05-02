<?php
$server = new swoole_websocket_server('0.0.0.0', 9777);
$redis = new Redis();
$redis->connect('127.0.0.1', 6379);
$users = $redis->sMembers('users');
//广播函数
function broadcast ($users, $client_id, $server, $data){
    foreach ($users as $user) {
        if ($user != $client_id) {
            $server->push($user, $data);
        }
    }
}

//开启连接时进行房间广播,将用户存入redis集合中
$server->on('open', function(swoole_websocket_server $server, $request) use ($redis){
    $redis->sAdd('users', $request->fd);
    $data = json_encode(['status' => 'login', 'msg' => '用户'.$request->fd.'登入']);
    broadcast($redis->sMembers('users'), $request->fd, $server, $data);

});

//接收信息时对房间进行广播
$server->on('message', function(swoole_websocket_server $server, $frame) use ($users){
    $data['msg'] = json_decode($frame->data)['msg'];
    $data['status'] = 1;
    broadcast($users, $frame->fd, $server, $data);
});

//关闭链接时进行房间广播
$server->on('close', function(swoole_websocket_server $server, $fd) use($redis){
    $redis->sRem('users', $fd);
    $data = json_encode(['status' => 'logout', 'msg' => '用户'.$fd.'登出']);
    broadcast($redis->sMembers('users'), $fd, $server, $data);
});

$server->start();