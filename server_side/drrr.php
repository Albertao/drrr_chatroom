<?php
/**
 * Created by PhpStorm.
 * User: albert
 * Date: 16-4-21
 * Time: 下午10:46
 */

namespace drrr;
use Swoole;
use Swoole\Filter;


class server extends Swoole\Protocol\WebSocket{

    public function __construct($config = []){

        //判断log文件夹是否存在，如不存在则重新创建
        $log_dir = dirname($config['drrr']['log_file']);
        if( !is_dir($log_dir) ) {
            mkdir($log_dir, 0777, true);
        }

        //添加文件logger
        $logger = new Swoole\Log\FileLog;
        $this->setLogger($logger);
    }


    public function onEnter($client_id){
        //todo 添加多房间功能
        //$this->connections[$client_id] = rand(1000, 9999);

        $user_info = $this->redis->get(rand(1,10));
        $result = ['msg' => $user_info['name']."加入了房间", 'status' => 'login', 'client_id' => $client_id];
        $this->log(Date('Y-m-d H:i:s')." - user $client_id logged in");
        $result = json_encode($result);
        $this->broadcast($client_id, $result);
    }

    public function onMessage($client_id, $msg){
        $msg = $this->parseJson($msg);
        $result = ['msg' => $msg['content'], 'status' => 'message'];
        $this->broadcast($client_id, $msg);

    }

    public function onExit($client_id){
        $msg = ['content' => "$client_id 退出", 'status' => 'logout'];
        $this->log(Date('Y-m-d H:i:s')." - user $client_id logout");
        $msg = json_encode($msg);
        $this->broadcast($client_id,$msg);
    }

    protected function broadcast($client_id, $msg){
        foreach( $this->connections as $clid => $info) {
            if($client_id != $clid) {
                $this->send($clid, $msg);
            }
        }
    }

    protected function parseJson($msg){
        $json_msg = json_decode($msg, true);
        if($json_msg) {
            return $json_msg;
        }else{
            return false;
        }
    }



}