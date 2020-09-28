<?php

#连接数据库
$PDO = new PDO('mysql:host=localhost;dbname=数据库名', '账号', '密码');
#获取5分钟之前的时间
$before = date("Y-m-d H:i:s",strtotime("-3 minute"));
#更新订单
$PDO->query("UPDATE coupons SET buy_time='0000-00-00 00:00:00',tradeno='0',mobile='0' WHERE buy_time <'$before' and coupon_status='0';");
$PDO->query("DELETE FROM `order` WHERE `order_time` <'$before' and `payment_status`='0';");

?>
