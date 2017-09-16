#php☞socket通信(2)
***

###创建套接字：resource socket_create ( int $domain , int $type , int $protocol )
>作用：
>>创建并返回一个套接字，也称作一个通讯节点。
>
>>一个典型的网络连接由 2 个套接字构成，一个运行在客户端，另一个运行在服务器端。

>参数:
>
	【domain】：
		domain 参数指定哪个协议用在当前套接字上。
		Domain	描述
		AF_INET	IPv4 网络协议。TCP 和 UDP 都可使用此协议。
		AF_INET6	IPv6 网络协议。TCP 和 UDP 都可使用此协议。
		AF_UNIX	本地通讯协议。具有高性能和低成本的 IPC（进程间通讯）。
	【type】：
		type 参数用于选择套接字使用的类型。
		类型	描述
		SOCK_STREAM	提供一个顺序化的、可靠的、全双工的、基于连接的字节流。支持数据传送流量控制机制。TCP 协议即基于这种流式套接字。
		SOCK_DGRAM	提供数据报文的支持。(无连接，不可靠、固定最大长度).UDP协议即基于这种数据报文套接字。
		SOCK_SEQPACKET	提供一个顺序化的、可靠的、全双工的、面向连接的、固定最大长度的数据通信；数据端通过接收每一个数据段来读取整个数据包。
		SOCK_RAW	提供读取原始的网络协议。这种特殊的套接字可用于手工构建任意类型的协议。一般使用这个套接字来实现 ICMP 请求（例如 ping）。
		SOCK_RDM	提供一个可靠的数据层，但不保证到达顺序。一般的操作系统都未实现此功能。
	【protocol】：
		protocol 参数，是设置指定 domain 套接字下的具体协议。
		名称	描述
		icmp	Internet Control Message Protocol 主要用于网关和主机报告错误的数据通信。
		udp	User Datagram Protocol 是一个无连接的、不可靠的、具有固定最大长度的报文协议。由于这些特性，UDP 协议拥有最小的协议开销。
		tcp	Transmission Control Protocol 是一个可靠的、基于连接的、面向数据流的全双工协议。

>返回值
>>socket_create() 正确时返回一个套接字，失败时返回 FALSE。要读取错误代码，可以调用 socket_last_error()。这个错误代码可以通过socket_strerror() 读取文字的错误说明。

###套接字绑定：bool socket_bind ( resource $socket , string $address [, int $port = 0 ] )
>作用
>>绑定 address 到 socket。 该操作必须是在使用 socket_connect() 或者 socket_listen() 建立一个连接之前。
>
>参数
>
	socket
		用 socket_create() 创建的一个有效的套接字资源。
	address
		如果套接字是 AF_INET 族，那么 address 必须是一个四点分法的 IP 地址（例如 127.0.0.1 ）。
		如果套接字是 AF_UNIX 族，那么 address 是 Unix 套接字一部分（例如 /tmp/my.sock ）。
	port （可选）
		参数 port 仅仅用于 AF_INET 套接字连接的时候，并且指定连接中需要监听的端口号。
>返回值
>>成功时返回 TRUE， 或者在失败时返回 FALSE

###套接字监听:bool socket_listen ( resource $socket [, int $backlog = 0 ] )
>参数
>>socket：用 socket_create() 创建的一个有效的套接字资源。

>返回值
>>返回值

###连接套接字：bool socket_connect ( resource $socket , string $address [, int $port = 0 ] )
>参数
>
	socket
		用 socket_create() 创建的一个有效的套接字资源。
	address
		如果参数 socket 是 AF_INET ， 那么参数 address 则可以是一个点分四组表示法（例如 127.0.0.1 ） 的 IPv4 地址； 
		如果支持 IPv6 并且socket 是 AF_INET6，那么 address 也可以是有效的 IPv6 地址（例如 ::1）；
		如果套接字类型为 AF_UNIX ，那么 address 也可以是一个Unix 套接字。
	port
		参数 port 仅仅用于 AF_INET 和 AF_INET6 套接字连接的时候，并且是在此情况下是需要强制说明连接对应的远程服务器上的端口号。

>返回值
>>成功时返回 TRUE， 或者在失败时返回 FALSE。

###编码示例
>server端

	[d:\~]$ cd E:\wamp\bin\php\php5.5.12
	[E:\wamp\bin\php\php5.5.12]$ php E:\wamp\www\Test\server.php
	success..
	         receive:Ho
	first blood

>client端

	[d:\~]$ cd E:\wamp\bin\php\php5.5.12
	[E:\wamp\bin\php\php5.5.12]$  php E:\wamp\www\Test\client2.php
	<h2>TCP/IP Connection</h2>
	                          OK.
	                             connect '127.0.0.1' port '1935'...
	                                                               connectOK
	                                                                        send success
	                                                                                    send msg:<font color='red'>Ho
	first blood
	</font> <br>recevie server msg
	                              receive msg:success
	                                                  close SOCKET...
	                                                                 close OK

>端口查看

	[d:\~]$ netstat -ano | findstr "1935"
	  TCP    127.0.0.1:1935         0.0.0.0:0              LISTENING       20740
	  TCP    127.0.0.1:1935         127.0.0.1:50870        TIME_WAIT       0

>服务器端源码

【服务器端】：
	<?php
	/*
	 +-------------------------------
	 *    @socket通信整个过程
	 +-------------------------------
	 *    @socket_create
	 *    @socket_bind
	 *    @socket_listen
	 *    @socket_accept
	 *    @socket_read
	 *    @socket_write
	 *    @socket_close
	
	 */
	
	header("Content-Type: text/html; charset=UTF-8");
	set_time_limit(0);//确保在连接客户端时不会超时
	
	$ip = '127.0.0.1';
	$port = 1935;
	
	//使用socket_creat()函数创建一个Socket了—这个函数返回一个Socket句柄,这个句柄将用在以后所有的函数中.
	/*
	 * 使用socket_creat()函数创建一个Socket了—这个函数返回一个Socket句柄,这个句柄将用在以后所有的函数中.
	 * param1：指定哪个协议用在当前套接字上。第一个参数"AF_INET"用来指定域名;
	 * param2：第二个参数"SOCK_STREM"告诉函数将创建一个什么类型的Socket(在这个例子中是TCP类型)
	 */
	if(($sock = socket_create(AF_INET,SOCK_STREAM,SOL_TCP)) < 0) {
	    echo "socket_create() 失败的原因是:".socket_strerror($sock)."\n";
	}
	
	if(($ret = socket_bind($sock,$ip,$port)) < 0) {
	    echo "socket_bind() 失败的原因是:".socket_strerror($ret)."\n";
	}
	
	if(($ret = socket_listen($sock,4)) < 0) {
	    echo "socket_listen() 失败的原因是:".socket_strerror($ret)."\n";
	}
	
	$count = 0;
	
	do{
	    //你的服务器除了等待来自客户端的连接请求外基本上什么也没有做.一旦一个客户端的连接被收到
		,socket_accept()函数便开始起作用了,它接收连接请求并调用另一个子Socket来处理客户端–服务器间的信息
	    if (($msgsock = socket_accept($sock)) < 0) {
	        echo "socket_accept() failed: reason: " . socket_strerror($msgsock) . "\n";
	        break;
	    }else{
	        //socker_read的第2个参数用以指定读入的字节数,你可以通过它来限制从客户端获取数据的大小.
	        //注意:socket_read函数会一直读取壳户端数据,直到遇见\n,\t或者\0字符.PHP脚本把这写字符看做是输入的结束符.
	        echo "success..\n";
	        $buf = socket_read($msgsock,8192);
	
	        $talkback = "receive:$buf\n";
	        echo $talkback;      
	
	        //socket发回一个数据流到客户端
	        $msg ="success\n";
	        socket_write($msgsock, $msg, strlen($msg));
	
	        //接受5次信号就跳出循环
	        if(++$count >= 5){
	            break;
	        };
	    }
	    socket_close($msgsock);
	
	}while(true);
	
	socket_close($sock);
	?>

>客户端
	
	<?php
	/*
	 +-------------------------------
	 *    @socket连接整个过程
	 +-------------------------------
	 *    @socket_create
	 *    @socket_connect
	 *    @socket_write
	 *    @socket_read
	 *    @socket_close
	 +--------------------------------
	 */
	
	header("Content-Type: text/html; charset=UTF-8");
	error_reporting(E_ALL);
	set_time_limit(0);
	echo "<h2>TCP/IP Connection</h2>\n";
	
	$port = 1935;
	$ip = "127.0.0.1";
	
	/*
	 +-------------------------------
	 *    @socket连接整个过程
	 +-------------------------------
	 *    @socket_create
	 *    @socket_connect
	 *    @socket_write
	 *    @socket_read
	 *    @socket_close
	 +--------------------------------
	 */
	
	$socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
	if ($socket < 0) {
	    echo "socket_create() failed: reason: " . socket_strerror($socket) . "\n";
	}else {
	    echo "OK.\n";
	}
	
	echo "connect '$ip' port '$port'...\n";
	$result = socket_connect($socket, $ip, $port);
	if ($result < 0) {
	    echo "socket_connect() failed.\nReason: ($result) " . socket_strerror($result) . "\n";
	}else {
	    echo "connectOK\n";
	}
	
	$in = "Ho\r\n";
	$in .= "first blood\r\n";
	$out = '';
	
	if(!socket_write($socket, $in, strlen($in))) {
	    echo "socket_write() failed: reason: " . socket_strerror($socket) . "\n";
	}else {
	    echo "send success\n";
	    echo "send msg:<font color='red'>$in</font> <br>";
	}
	
	while($out = socket_read($socket, 8192)) {
	    echo "recevie server msg\n";
	    echo "receive msg:",$out;
	}
	
	
	echo " close SOCKET...\n";
	socket_close($socket);
	echo "close OK\n";
	?>

>注意
>>PHP语言自身定位决定了它只适合做客户端，而不适合做服务器端。因为Socket是面向底层和网络服务开发，一般服务器是用C、JAVA等语言实现，这样能更好的操控底层，对网络服务开发中遇到的问题(比如并发、阻塞)比较好的解决方案。
>
>>实际上，PHP操作MYSQL数据库也是通过Socket进行的，这正是由于Socket屏蔽了底层协议，是的网络服务之间的互联互通变得更加简单。


###fsockopen创建套接字
>作用
>>fsockopen — 打开一个网络连接或者一个Unix套接字连接，即初始化一个套接字连接到指定主机（hostname）

>参数
	hostname
		如果安装了OpenSSL，那么你也许应该在你的主机名地址前面添加访问协议ssl://或者是tls://，从而可以使用基于TCP/IP协议的SSL或者TLS的客户端连接到远程主机。
	port
		端口号。如果对该参数传一个-1，则表示不使用端口，例如unix://。
	errno
		如果传入了该参数，holds the system level error number that occurred in the system-level connect() call。
		如果errno的返回值为0，而且这个函数的返回值为FALSE，那么这表明该错误发生在套接字连接（connect()）调用之前，导致连接失败的原因最大的可能是初始化套接字的时候发生了错误。
	errstr
		错误信息将以字符串的信息返回。
	timeout
		设置连接的时限，单位为秒。

>注意
>>如果你要对建立在套接字基础上的读写操作设置操作时间设置连接时限，请使用stream_set_timeout()，fsockopen()的连接时限（timeout）的参数仅仅在套接字连接的时候生效
>
>返回值
>>fsockopen()将返回一个文件句柄，之后可以被其他文件类函数调用（例如：fgets()，fgetss()，fwrite()，fclose()还有feof()）。如果调用失败，将返回FALSE。

###Example #1 fsockopen()的例子	
	<?php
		$fp = fsockopen("www.example.com", 80, $errno, $errstr, 30);
		if (!$fp) {
		    echo "$errstr ($errno)<br />\n";
		} else {
		    $out = "GET / HTTP/1.1\r\n";
		    $out .= "Host: www.example.com\r\n";
		    $out .= "Connection: Close\r\n\r\n";
		    fwrite($fp, $out);
		    while (!feof($fp)) {
		        echo fgets($fp, 128);
		    }
		    fclose($fp);
		}
	?>

###Example #2 使用UDP连接

	<?php
		$fp = fsockopen("udp://127.0.0.1", 13, $errno, $errstr);
		if (!$fp) {
		    echo "ERROR: $errno - $errstr<br />\n";
		} else {
		    fwrite($fp, "\n");
		    echo fread($fp, 26);
		    fclose($fp);
		}
	?>


###socket、fsockopen、curl区别
>socket：水泥、沙子，底层的东西
>>水泥、沙子不但可以修房子，还能修路、修桥、大型雕塑。socket也是，不但可以用于网页传输，还能传送其他东西，可以做聊天工具、下载器、ftp……几乎可以用网络传送的东西都能用它写出来，当然，需要掌握的知识也不少，例如建墙你就要知道怎么让墙笔直、不易倒、防冻、隔热等等都需要自己学

>fsockopen：水泥预制件，可以用来搭房子
>>预制件你就不用管它是否笔直、结构如何、怎样隔热了，这些造的人帮你想好了，你想的就是怎样搭成你想要的形状就行。fsockopen就是，你可以忽略socket里面的creat, connect, send, recv等等函数的用法，直接就open了

>curL：毛坯房，自己装修一下就能住了
>>毛坯房就更简单了，你装修就能住，最简单刷墙就行了，但想更舒适，就用更多更好的装修材料吧，但缺点就是——这是房子，你不能把它改造为渡河、交通的用途，只能住。curl也一样，各种连接什么的都帮你做好了，底层容错处理也做了，你就传参数给它就能得到你想要的结果，但缺点就是只能http / ftp，你想把它改成聊天工具，那就难难难了

>总结
>>大致意思差不多，不过fsockopen（$ address）和socket_connect（socket_create（），$ address）本质的区别是：
>
>>>fsockopen创建到主机的连接，而不是侦听套接字。即fsockopen（$ address）〜== socket_connect（socket_create（），$ address）。
>
>>>您的托管提供商不希望您在备用端口/协议上侦听。