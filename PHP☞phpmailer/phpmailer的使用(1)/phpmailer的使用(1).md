#phpmailer的使用(1)
***

#什么是phpmailer？
>PHPMailer是一个用于发送电子邮件的PHP函数包；根据SMTP协议并结合socket功能就可以编写高效稳定的邮件发送程序。

>在使用phpmailer之前先来熟悉一下SMTP协议~~

###邮件的路由过程
IMG

>步骤2过程详解：
>
	b. 若SMTP服务器mail.sohu.com收到一封信要发送到gacl@sina.com：
	a: SendMail请求DNS给出主机gacl@sina.com的CNAME记录，如有，假若CNAME到gacl2@sina.com，
		则再次请求gacl2@sina.com的CNAME记录，直到没有为止；
>	
	b: 假定被CNAME到gacl2@sina.com，然后SendMail请求@sina.com域的DNS给出gacl2@sina.com的MX记录为mail@sina.com，
>	
	c: SendMail做好请求DNS给出为mail@sina.com的A记录，即IP地址，若返回值为1.2.3.4
>		
	d: SwndMail与1.2.3.4连接，传送这封信给gacl@sina.com的信到1.2.3.4这台服务器的SMTP后台程序。

>注意：
>>DNS MX记录一定要放在A记录之前,否则和邮件后缀相匹配的域名没有指向邮件服务器，很有可能邮件服务器收不到邮件。
>
>>邮件服务器发送邮件首先寻找DNS ＭＸ记录，如果查找ＭＸ记录失败，则直接利用Ａ记录收发信

###什么是SMTP协议？
>SMTP称为简单邮件传输协议（Simple Mail Transfer Protocal），目标是向用户提供高效、可靠的邮件传输。它的一个重要特点是它能够在传送中接力传送邮件，即邮件可以通过不同网络上的主机接力式传送。
>
>SMTP是一个请求/响应协议，它监听25号端口，用于接收用户的Mail请求，并与远端Mail服务器建立SMTP连接。

###SMTP协议工作机制
>SMTP通常有两种工作模式。发送SMTP和接收SMTP,发送SMTP在接收到用户的邮件请求后，进行判断：
>
>>1.判断是否是本地邮件，若是则直接投送到用户的邮箱。
>
>>2.若不是本地邮件，非本地邮件向DNS查询远端邮件服务器的MX记录，并建立与远端接收SMTP之间的一个双向传送通道，此后SMTP命令由发送SMTP发出，由接收SMTP接收，而应答则反方向传送。
>
>>3.一旦传送通道建立，SMTP发送者发送MAIL命令指明邮件发送者。
>>>如果SMTP接收者可以接收邮件则返回OK应答；否则则拒绝应答。
>
>>>SMTP发送者再发出RCPT命令确认邮件是否接收到。如果SMTP接收者接收，则返回OK应答；如果不能接收到，则发出拒绝接收应答（但不中止整个邮件操作），双方将如此反复多次。
>
>>>当接收者收到全部邮件后会接收到特别的序列，表示接收者成功处理了邮件，则返回OK应答。

###SMTP的连接和发送具体过程

	a. 建立TCP连接
	b. 客户端发送HELO命令以标识发件人自己的身份，然后客户端发送MAIL命令；服务器端正希望以OK作为响应，表明准备接收
	c. 客户端发送RCPT命令，以标识该电子邮件的计划接收人，可以有多个RCPT行；服务器端则表示是否愿意为收件人接收邮件
	d. 协商结束，发送邮件，用命令DATA发送
	e. 以.表示结束输入内容一起发送出去
	f. 结束此次发送，用QUIT命令退出
IMG

