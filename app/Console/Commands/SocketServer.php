<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class SocketServer extends Command {
	
	/**
	 * The name and signature of the console command.
	 * @var string
	 */
	protected $signature = 'SocketServer';
	
	/**
	 * The console command description.
	 * @var string
	 */
	protected $description = 'socket服务器';
	
	public $address = '127.0.0.1';
	public $port = 12346;
	public $master;
	public $sockets = [];
	public $hand;
	
	/**
	 * Create a new command instance.
	 * @return void
	 */
	public function __construct() {
		
		parent::__construct();
		$this->address = env('SERVER_INNER_IP');
	}
	
	/**
	 * Execute the console command.
	 * @return mixed
	 */
	public function handle() {
		
		# 创建socket
		$this->master = socket_create(AF_INET, SOCK_STREAM, SOL_TCP) or die('socket_create() failed');
		socket_set_option($this->master, SOL_SOCKET, SO_REUSEADDR, 1) or die('socket_option() failed');
		# 绑定ip和端口
		socket_bind($this->master, $this->address, $this->port) or die('socket_bind() failed');
		# 监听
		socket_listen($this->master) or die('socket_listen() failed');
		
		$this->sockets[] = $this->master;
		
		$this->start();
	}
	
	/**
	 * 开启socket server
	 * @author 李小同
	 * @date   2019-1-4 15:01:01
	 */
	public function start() {
		
		$lastCount = [];
		
		while (true) {
			
			$arr   = $this->sockets;
			$write = $except = null;
			//接收套接字数字 监听他们的状态 todo lxt 这里有问题，只返回可读的socket，始终读上一个连接上的socket
			@socket_select($arr, $write, $except, null); # 有阻塞 没有新的客户端接入，即没有可以可读的socket，一直阻塞
			
			//遍历套接字数组
			foreach ($arr as $k => $v) {
				//如果是新建立的套接字返回一个有效的套接字资源
				if ($this->master == $v) {
					$client = socket_accept($this->master); # 有阻塞或有timeout
					if ($client < 0) {
						continue;
					} else {
						//将有效的套接字资源放到套接字数组 客户端连接成功
						$this->sockets[] = $client;
					}
				} else {
					
					//判断有没有握手没有握手则进行握手,如果握手了 则进行处理
					if (empty($this->hand[(int)$v])) {
						
						//从已连接的socket接收数据  返回的是从socket中接收的字节数
						$byte = @socket_recv($v, $buff, 2048, 0); # 有阻塞或有timeout
						
						//如果接收的字节是0
						if (!$byte) {
							socket_close($v);
							continue;
						}
						
						//进行握手操作
						$this->hands($v, $buff);
						$lastCount[(int)$v] = 0;
					}
					
					//处理数据操作
					$msg['count'] = redisGet('notice_count');
					if ($lastCount[(int)$v] != $msg['count']) {
						$lastCount[(int)$v] = $msg['count'];
						//发送数据
						$this->sendToAll($msg, $v);
					}
				}
			}
		}
	}
	
	/**
	 * TCP握手
	 * @param $client : socket resource
	 * @param $buff   : string
	 * @author 李小同
	 * @date   2019-1-4 15:16:26
	 */
	public function hands($client, $buff) {
		
		//提取websocket传的key并进行加密  （这是固定的握手机制获取Sec-WebSocket-Key:里面的key）
		$buf = substr($buff, strpos($buff, 'Sec-WebSocket-Key:') + 18);
		//去除换行空格字符
		$key = trim(substr($buf, 0, strpos($buf, "\r\n")));
		//固定的加密算法
		$new_key     = base64_encode(sha1($key."258EAFA5-E914-47DA-95CA-C5AB0DC85B11", true));
		$new_message = "HTTP/1.1 101 Switching Protocols\r\n";
		$new_message .= "Upgrade: websocket\r\n";
		$new_message .= "Sec-WebSocket-Version: 13\r\n";
		$new_message .= "Connection: Upgrade\r\n";
		$new_message .= "Sec-WebSocket-Accept: ".$new_key."\r\n\r\n";
		//将套接字写入缓冲区
		socket_write($client, $new_message, strlen($new_message));
		//标记此套接字握手成功
		$this->hand[(int)$client] = true;
	}
	
	//发送数据单发，适用于私聊场景
	public function send($msg, $client) {
		
		# 成功握手则进行数据发送
		if (!empty($this->hand[(int)$client])) {
			$msg['name'] = "{$client}";
			$str         = json_encode($msg);
			$writes      = "\x81".chr(strlen($str)).$str;
			@socket_write($client, $writes, strlen($writes));
		}
	}
	
	# todo lxt 群发消息，适合聊天室场景，需结合上下文来改写
	public function sendToAll($msg, $client) {
		
		//遍历套接字数组 成功握手的  进行数据群发
		foreach ($this->sockets as $keys => $values) {
			//用系统分配的套接字资源id作为用户昵称
			$msg['name'] = "{$client}";
			$str         = json_encode($msg);
			$writes      = "\x81".chr(strlen($str)).$str;
			if (!empty($this->hand[(int)$values])) {
				@socket_write($values, $writes, strlen($writes));
			}
		}
	}
}
