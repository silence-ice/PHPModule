#PHP☞CVS报表导出
***

###什么是CSV文件？
>CSV，是Comma Separated Value（逗号分隔值）的英文缩写，通常都是纯文本文件。

>如果你导出的Excel没有什么高级用法的话，只是做导出数据用那么建议使用本方法,要比PHPexcel要高效的多。
二十万数据导出大概需要2到3秒。

###CSV文件导出缺点
>CSV文件没有任何格式，不可设置列宽度、文字颜色、样式。
>
>CSV不可以创建多个Sheet。


###解决乱码情况
>通常以UTF8编码导出CSV文件，文件头需要添加BOM头，否则会显示乱码。
>
>有这个BOM头它就能识别到编码格式是UTF-8，否则识别不了用默认的GBK来处理，自然就是乱码。
>
    $fp = fopen('php://output', 'a');
    fwrite($fp, chr(0xEF).chr(0xBB).chr(0xBF));

>使用iconv函数进行转码为GBK
>
>缺点就是需要每个数组值进行转码
>
	$arr['username'] = "黄斌";
	$arr['username'] = iconv('utf-8', 'gbk', $arr['username']);

###fputcsv() 函数用法
>定义和用法
>>fputcsv() 函数将行格式化为 CSV 并写入一个打开的文件。
>
>>该函数返回写入字符串的长度。若出错，则返回 false。。

>语法
>>fputcsv(file,fields,seperator,enclosure)

>参数	描述
>
	file	必需。规定要写入的打开文件。
	fields	必需。规定要从中获得数据的数组。
	seperator	可选。规定字段分隔符的字符。默认是逗号 (,)。
	enclosure	可选。规定字段环绕符的字符。默认是双引号 "。

###代码示例
>通过php://output向终端输出内容

	<?php
	header('Content-Type: application/csv');
	header('Content-Disposition: attachment; filename="test1.csv"');
	
	$output = fopen('php://output','w') or die("Can't open php://output");
	fwrite($output, chr(0xEF).chr(0xBB).chr(0xBF));
	
	// 输出表头
	fputcsv($output, array('站点名', '域名', '行业'));
	
	//数据内容
	$rows = array(
	     array('天猫', 'http://tmall.com', '电子商务')
	    ,array('爱E族', 'http://aiezu.com', '互联网技术')
	    ,array('腾讯', 'http://qq.com', '社交网络')
	);
	
	foreach($rows as $val) {
	    fputcsv($output, $val);
	}
	fclose($output) or die("Can't close php://output");


###封装CSV函数
	
	<?php
	$bodyData = array();   //声明空数组防止报错
	$bodyData[] = array ("\t"."651651616",'1','0');//"\t"防止转换成科学计数法
	$bodyData[] = array ("\t".'651651616','1','0');
	
	$headerDate = array('PO单编号', '拣货方式', '自动补货');
	$fileName = 'huangbin';
	
	to_csv($fileName, $headerDate, $bodyData);
	
	  
	function to_csv($file_name, $headerDate, $bodyData) {
	    header('Content-Type: application/csv');
	    header('Content-Disposition: attachment;filename="' . $file_name . '.csv"');
	
	    $fp = fopen('php://output', 'a');
	
	    fwrite($fp, chr(0xEF).chr(0xBB).chr(0xBF));
	
	    fputcsv($fp, $headerDate);//导入表头数据
	
	    foreach($bodyData as $val) {
		    fputcsv($fp, $val);
		}
	}
	  
