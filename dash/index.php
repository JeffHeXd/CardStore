<?php 
	header('Content-type:text/html; charset=utf-8');
	// 开启Session
	session_start();
	//确定title
	$title = "JStore 后台管理首页";
	require('header.php');
 
	// 首先判断Cookie是否有记住了用户信息
	if (isset($_COOKIE['username'])) {
		# 若记住了用户信息,则直接传给Session
		$_SESSION['username'] = $_COOKIE['username'];
		$_SESSION['islogin'] = 1;
	}
	if (isset($_SESSION['islogin'])) {
	    
	    //通过SESSion获取当前用户信息
	    $username = $_SESSION['username'];
	    $date = date("Y-m-d").'%';
	    //连接数据库
	    $PDO = new PDO('mysql:host=localhost;dbname=数据库名', '账号', '密码');
	    
	    //获取交易情况变量
	    $order = $PDO->query("SELECT count(*) FROM `coupons` WHERE `coupon_status`='1' AND `buy_time` like '$date';");
	    $order = $order->fetch();
	    $order2 = $PDO->query("SELECT count(*) FROM `coupons` WHERE `coupon_status`='3' AND `buy_time` like '$date';");
	    $order2 = $order2->fetch();
	    $total = $PDO->query("SELECT sum(total) FROM `order` WHERE `payment_status`='1' AND `order_time` like '$date';");
	    $total = $total->fetch();
	    $total2 = $PDO->query("SELECT sum(total) FROM `order` WHERE `payment_status`='3' AND `order_time` like '$date';");
	    $total2 = $total2->fetch();
	    if(empty($total[0])){$total = 0;}else{$total = $total[0];}
	    $refund = $PDO->query("SELECT sum(total) FROM `order` WHERE `payment_status`='3' AND `order_time` like '$date';");
	    $refund = $refund->fetch();
	    if(empty($refund[0])){$refund = 0;}else{$refund = $refund[0];}
	    $service = $total[0]*0.006;
	    $service = intval($service);
	    //获取商品消息变量
	    $coupon_type = $PDO->query("SELECT count(*) FROM `coupon_text`;");
	    $coupon_type = $coupon_type->fetch();
	    
	    $coupon_sum = $PDO->query("SELECT count(*) FROM `coupons`;");
	    $coupon_sum = $coupon_sum->fetch();
	    
	    $coupon_canuse = $PDO->query("SELECT count(*) FROM `coupons` WHERE `coupon_status`='0';");
	    $coupon_canuse = $coupon_canuse->fetch();
	    
	    $coupon_add = $PDO->query("SELECT count(*) FROM `coupons` WHERE `add_time` like '$date';");
	    $coupon_add = $coupon_add->fetch();
	    
	    //获取账户相关消息
	    $user = $PDO->query("SELECT * FROM `admin` WHERE `username`='$username';");
	    $user = $user->fetch(PDO::FETCH_ASSOC);
	    //获取登录失败日志
	    $error = $PDO->query("SELECT count(*) FROM `login_log` WHERE `login_status`='失败' AND `type`='登录' AND `login_time` like '$date';");
	    $error = $error->fetch();
	    
?>

<br>
<div class="container">
    <p class="cnfont">您好，<?php echo $_SESSION['name']; ?>，以下是网站简报：</p>
    <div class="card">
        <div class="card-header">今日交易情况(<?php echo date("Y-m-d"); ?>)</div>
        <div class="card-body">
            <center>
                <ul class="list-inline">
                    <li class="list-inline-item">
                        <font style="font-size:25px;color:black;"><?php echo $order[0]+$order2[0]; ?></font><br> 今日订单
                    </li>
                    <li class="list-inline-item">
                        <font style="font-size:25px;color:#9400D3;"><?php echo $total+$total2[0]; ?></font><br> 交易金额
                    </li>
                    <li class="list-inline-item">
                        <font style="font-size:25px;color:red;"><?php echo $refund; ?></font><br> 退款金额
                    </li>
                    <li class="list-inline-item">
                        <font style="font-size:25px;color:#888888;"><?php echo $service; ?></font><br> 产生费用
                    </li>
                </ul>
            </center>
        </div>
    </div>
    <br>
    <div class="card">
        <div class="card-header">当前商品信息</div>
        <div class="card-body">
            <center>
                <ul class="list-inline">
                    <li class="list-inline-item">
                        <font style="font-size:25px;color:black;"><?php echo $coupon_type[0]; ?></font><br> 分类数量
                    </li>
                    <li class="list-inline-item">
                        <font style="font-size:25px;color:black;"><?php echo $coupon_sum[0]; ?></font><br> 商品数量
                    </li>
                    <li class="list-inline-item">
                        <font style="font-size:25px;color:black;"><?php echo $coupon_canuse[0]; ?></font><br> 剩余数量
                    </li>
                    <li class="list-inline-item">
                        <font style="font-size:25px;color:black;"><?php echo $coupon_add[0]; ?></font><br> 上架数量
                    </li>
                </ul>
            </center>
        </div>
    </div>
    <br>
    <div class="card">
        <div class="card-header">站点其他消息</div>
        <div class="card-body">
            <p class="cnfont">为了账号安全，您可查阅以下消息：</p>
            <ul class="list-group list-group-flush">
                <li class="list-group-item">上一次登录的IP：<b><?php echo $user['last_login_ip']; ?></b></li>
                <li class="list-group-item">上一次登录的位置：<b><?php echo $user['last_login_adress']; ?></b></li>
                <li class="list-group-item">今日登录错误次数：<b> <?php echo $error[0]; ?> </b>次</li>
            </ul>
        </div>
    </div>
</div>
<br>
<div class="card-footer text-muted text-center">
    <div style="font-size:10px;">
        ldygo.fun @版权所有
    </div>
</div>
            


<?php
	}else {
		// 若没有登录
		exit("<script>alert('还没登录，即将为您跳转到登录页面');window.location.href='login';</script>");
	}
 ?>
