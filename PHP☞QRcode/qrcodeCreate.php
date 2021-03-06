<?php
/**
 * @url 【官方文档】http://phpqrcode.sourceforge.net/
 * @summary 生成二维码，但尽可能使用前端生成，因为前端消耗的是客户端资源，PHP消耗的是服务器资源
 */

include "./phpqrcode/qrlib.php";

//1.在网页生成一张二维码图片
//function png($text, $outfile = false, $level = QR_ECLEVEL_L, $size = 3, $margin = 4, $saveandprint=false) 
// QRcode::png("http://www.baidu.com");

//2.生成一张二维码图片文件
// QRcode::png("http://www.baidu.com", "baidu.jpg");

/**
 * L级：可纠错7%的数据码字 | QR_ECLEVEL_L
 * M级：可纠错15%的数据码字 | QR_ECLEVEL_M
 * Q级：可纠错25%的数据码字 | QR_ECLEVEL_Q
 * H级：可纠错30%的数据码字 | QR_ECLEVEL_H
 */
// 3.二维码图片级别
// QRcode::png("http://www.baidu.com", false, QR_ECLEVEL_L, 10, 4, true);