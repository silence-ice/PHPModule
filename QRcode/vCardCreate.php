<?php
/**
 * @summary 生成电子名片
 */
include "./phpqrcode/qrlib.php";


$content = 'BEGIN:VCARD';
$content .= 'VERSION:2.1'."\n";
$content .= 'N:张'."\n";//姓
$content .= 'FN:三'."\n";//名
$content .= 'ORG:广东省惠州市'."\n";//公司地址
$content .= 'EMAIL:548491577@qq.com'."\n";//邮箱
$content .= 'URL:http://www.baidu.com'."\n";//网址
$content .= 'TEL;WORK;VOICE:13790716637'."\n";//工作单位电话
$content .= 'TEL;HOME;VOICE:0752-2513337'."\n";//家里电话
$content .= 'TEL;TYPE=cell:13790716637'."\n";//移动电话
$content .= 'ADR;HOME;:广东省惠州市河南岸'."\n";//家庭住址
$content .= 'END:VCARD'."\n";//网址
 

QRcode::png($content);
