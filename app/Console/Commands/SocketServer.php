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
			//接收套接字数字 监听他们的状态
			@socket_select($arr, $write, $except, null); # 有阻塞或有timeout
			
			//遍历套接字数组
			foreach ($arr as $k => $v) {
				//如果是新建立的套接字返回一个有效的 套接字资源
				if ($this->master == $v) {
					$client = socket_accept($this->master); # 有阻塞或有timeout
					if ($client < 0) {
						continue;
					} else {
						//将有效的套接字资源放到套接字数组
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
						$lastCount[$k] = 0;
					}
					
					//处理数据操作
					$mess['count'] = redisGet('notice_count');
					if ($lastCount[$k] != $mess['count']) {
						$lastCount[$k] = $mess['count'];
						//发送数据
						$this->send($mess, $v);
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
	
	//解析数据
	public function decodeData($buff) {
		
		//$buff  解析数据帧
		$mask = array();
		$data = '';
		$msg  = unpack('H*', $buff);  //用unpack函数从二进制将数据解码
		$head = substr($msg[1], 0, 2);
		if (hexdec($head{1}) === 8) {
			$data = false;
		} else if (hexdec($head{1}) === 1) {
			$mask[] = hexdec(substr($msg[1], 4, 2));
			$mask[] = hexdec(substr($msg[1], 6, 2));
			$mask[] = hexdec(substr($msg[1], 8, 2));
			$mask[] = hexdec(substr($msg[1], 10, 2));
			//遇到的问题  刚连接的时候就发送数据  显示 state connecting
			$s = 12;
			$e = strlen($msg[1]) - 2;
			$n = 0;
			for ($i = $s; $i <= $e; $i += 2) {
				$data .= chr($mask[$n % 4] ^ hexdec(substr($msg[1], $i, 2)));
				$n++;
			}
			//发送数据到客户端
			//如果长度大于125 将数据分块
			$block = str_split($data, 125);
			$mess  = array(
				'mess' => $block[0],
			);
			return $mess;
		}
	}
	
	//发送数据
	public function send($mess, $v) {
		
		//遍历套接字数组 成功握手的  进行数据群发
		foreach ($this->sockets as $keys => $values) {
			//用系统分配的套接字资源id作为用户昵称
			$mess['name'] = "{$v}";
			$str          = json_encode($mess);
			$writes       = "\x81".chr(strlen($str)).$str;
			if (!empty($this->hand[(int)$values])) {
				@socket_write($values, $writes, strlen($writes));
			}
		}
	}
}
