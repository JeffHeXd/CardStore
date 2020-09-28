<?php
//error_reporting(0);
date_default_timezone_set('PRC');
#券码类型
$coupons = $_REQUEST['change_coupon'];
#操作类型
$control = $_REQUEST['type'];
#购买凭证
$mobile = $_POST['mobile'];
#连接数据库
$PDO = new PDO('mysql:host=localhost;dbname=数据库名', '账号', '密码');
#输出信息函数
function msg($code,$msg,$total,$sum){
    $out = array();
    $out['code'] = $code;
    $out['total'] = $total;
    $out['sum'] = $sum;
    $out['msg'] = $msg;
    $out_string = json_encode($out,true);
    return $out_string;
}
#判断手机or电脑
function ismobile()
{
        if (isset($_SERVER['HTTP_VIA']) && stristr($_SERVER['HTTP_VIA'], "wap")) {
            return true;
        } elseif (isset($_SERVER['HTTP_ACCEPT']) && strpos(strtoupper($_SERVER['HTTP_ACCEPT']), "VND.WAP.WML")) {
            return true;
        } elseif (isset($_SERVER['HTTP_X_WAP_PROFILE']) || isset($_SERVER['HTTP_PROFILE'])) {
            return true;
        } elseif (isset($_SERVER['HTTP_USER_AGENT']) && preg_match('/(blackberry|configuration\/cldc|hp |hp-|htc |htc_|htc-|iemobile|kindle|midp|mmp|motorola|mobile|nokia|opera mini|opera |Googlebot-Mobile|YahooSeeker\/M1A1-R2D2|android|iphone|ipod|mobi|palm|palmos|pocket|portalmmm|ppc;|smartphone|sonyericsson|sqh|spv|symbian|treo|up.browser|up.link|vodafone|windows ce|xda |xda_)/i', $_SERVER['HTTP_USER_AGENT'])) {
            return true;
        } else {
            return false;
        }
    }

switch ($control) {
    case 'get_coupon':
        #取得库存
        $get_sum = $PDO->query("SELECT count(*) FROM `coupons` WHERE `coupon_type`='$coupons' AND coupon_status=0 and buy_time='0000-00-00 00:00:00' and tradeno=0;");
        $get_sum = $get_sum->fetch();
        $coupon_sum = $get_sum[0];
        #取得金额
        $get_total = $PDO->query("SELECT * FROM `coupon_text` WHERE `coupon_type`='$coupons';");
        $get_total = $get_total->fetch(PDO::FETCH_ASSOC);
        $total = $get_total['total'];
        #取得商品简介
        $get_text = $PDO->query("SELECT * FROM `coupon_text` WHERE `coupon_type`='$coupons';");
        $get_text = $get_text->fetch(PDO::FETCH_ASSOC);
        $text = $get_text['text'];
        echo msg('200',$text,$total,$coupon_sum);
        break;
    case 'order':
        #极验证
        require('public/geetest.php');
        // #取得库存
        $get_sum = $PDO->query("SELECT count(*) FROM `coupons` WHERE `coupon_type`='$coupons' AND coupon_status=0 and buy_time='0000-00-00 00:00:00' and tradeno=0;");
        $get_sum = $get_sum->fetch();
        $coupon_sum = $get_sum[0];
        if($coupon_sum<1){exit('<script type="text/javascript">alert("错误:没有库存");window.history.go(-1);</script>');}
        #取得商品昵称
        #查询券码在库存的金额
        $get_total = $PDO->query("SELECT * FROM `coupon_text` WHERE `coupon_type`='$coupons';");
        $get_total = $get_total->fetch(PDO::FETCH_ASSOC);
        $auto_name = $get_total['name'];
        $auto_total = $get_total['total'];
        $outTradeNo = uniqid();
        $now = date("Y-m-d H:i:s");
        #将订单写入数据库
        $PDO->query("INSERT INTO `order`(`mobile`, `order_type`, `total`, `tradeno`, `order_time`, `payment_status`) VALUES ('$mobile','$coupons','$auto_total','$outTradeNo','$now','0');");
        #锁定卡密
        $PDO->query("UPDATE coupons SET buy_time='$now',mobile='$mobile',tradeno='$outTradeNo' WHERE coupon_status=0 and buy_time='0000-00-00 00:00:00' and tradeno=0 and coupon_type='$coupons' LIMIT 1;");
        if(!ismobile()){
            require('public/pc.php');
        }else{
            require('public/mobile.php');
        }
        
        break;
    default:
        echo msg('-1','调用错误','','');
        break;
}

?>
