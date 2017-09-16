#phpmailer的使用(2)
***

###导入phpmailer扩展包

>第一步：需要下载PHPMailer文件包，并确认你的服务器系统已经支持socket 如下图，通过phpinfo();查看是否支持sockets
>如果没有这一项就请注意： socket 是属于PHP扩展部分，编译时必须给定一个用于./configure --enable-sockets 的配置选项。
>
>第二步：把文件解压到你的web服务器目录下，调用类就可以了；当前我的扩展包放在Thinkphp下的：`webroot/ThinkPHP/Library/Vendor/PHPMailer`
>
	PHPMailer
	--class.pop3.php
	--class.smtp.php
	--PhpMailer.php

###封装邮件类
>注意：
>>因为thinkphp3.2.2采用命名空间需要采用命名空间，实例化的时候需要加上根命名空间。
>
>>当发送QQ邮件的时候，发件人的密码需要是授权码，而不是独立密码和是账号密码。
	
	<?php
	namespace Api\Controller;
	use Think\Controller;
	class EmailController extends CommonController{
		protected  function _initialize() {
			Vendor('PHPMailer.PhpMailer');   
		}
	
		function sendMail($address,$title,$message,$fromName='')
		{	
			if(empty($fromName)){
				$fromName =C('site_desc');
			}
			$email_isuse = C('email_isuse');//是否启用
			$email = C("email_account");//邮箱账号
			$email_server = C("email_server");//邮箱服务器
			$email_password = base64_decode(C("email_password"));//邮箱账号密码
			
			$mail = new \PHPMailer();
			$mail->IsSMTP();
			$mail->CharSet = 'UTF-8';
			$mail->AddAddress($address);
			$mail->Body = $message;
			// $mail->SMTPDebug = true;
			$mail->Port = C('email_port') ? C('email_port'):465;
			if (C('email_port') == 465) {
				$mail->SMTPSecure = 'ssl';
			}
			$mail->From = $email;
			$mail->FromName = $fromName;
			$mail->Subject = $title;
			$mail->Host = $email_server;
			$mail->Username = $email;
			$mail->Password = $email_password;
			$mail->SMTPAuth = true;
			$mail->IsHTML(true);
			return $mail->Send();
		}
	
	}


###"通过命令行"测试邮件域名解析和邮件端口状态**【测试端口连通性】**

>测试SMTP 25端口
>
	[d:\~]$ telnet mail.efly.cc 25
	Host 'mail.efly.cc' resolved to 121.201.61.135.
	Connecting to 121.201.61.135:25...
	Connection established.
	To escape to local shell, press 'Ctrl+Alt+]'.
	220 mail.efly.cc ESMTP Postfix

>测试SMTP 465端口
>
	[d:\~]$ telnet mail.efly.cc 465
	Host 'mail.efly.cc' resolved to 121.201.61.135.
	Connecting to 121.201.61.135:465...
	Could not connect to 'mail.efly.cc' (port 465): Connection failed.


>测试POP3 110端口
>
	[d:\~]$ telnet mail.efly.cc 110
	Host 'mail.efly.cc' resolved to 121.201.61.135.
	Connecting to 121.201.61.135:110...
	Connection established.
	To escape to local shell, press 'Ctrl+Alt+]'.
	+OK Dovecot ready.

>测试IMAP服务连通性


###测试DNS服务解析
>测试DNS服务解析

	[d:\~]$ nslookup smtp.163.com
	非权威应答:
	服务器:  UnKnown
	Address:  192.168.10.1
	
	名称:    smtp.163.com
	Addresses:  220.181.12.14
		  220.181.12.15
		  220.181.12.12
		  220.181.12.11
		  220.181.12.13
		  220.181.12.18
		  220.181.12.16
		  220.181.12.17

>测试邮箱域名邮件记录（MX记录）

	[d:\~]$ nslookup
	默认服务器:  UnKnown
	Address:  192.168.10.1
	
	> set type=mx
	> 163.com
	非权威应答:
	服务器:  UnKnown
	Address:  192.168.10.1
	
	163.com	MX preference = 10, mail exchanger = 163mx01.mxmail.netease.com
	163.com	MX preference = 10, mail exchanger = 163mx02.mxmail.netease.com
	163.com	MX preference = 10, mail exchanger = 163mx03.mxmail.netease.com
	163.com	MX preference = 50, mail exchanger = 163mx00.mxmail.netease.com

>测试邮箱域名邮件记录（A记录）

	[d:\~]$ nslookup
	默认服务器:  UnKnown
	Address:  192.168.10.1

	> set type=a
	> mail.efly.cc
	非权威应答:
	服务器:  UnKnown
	Address:  192.168.10.1
	
	名称:    mail.efly.cc
	Addresses:  121.201.61.166
		  121.201.61.135
		  121.201.61.131