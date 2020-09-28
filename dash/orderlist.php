<?php 
	header('Content-type:text/html; charset=utf-8');
	// 开启Session
	session_start();
	//确定title
	$title = "JStore 订单管理";
	require('header.php');
 
	// 首先判断Cookie是否有记住了用户信息
	if (isset($_COOKIE['username'])) {
		# 若记住了用户信息,则直接传给Session
		$_SESSION['username'] = $_COOKIE['username'];
		$_SESSION['islogin'] = 1;
	}
	if (isset($_SESSION['islogin'])) {
	    $PDO = new PDO('mysql:host=localhost;dbname=数据库名', '账号', '密码');
	    
?>
<script>
    function fuck(id,total){
        console.log("支付宝订单号："+id);
        
        $.ajax({
            type: "POST",
            dataType: "json",
            url: "close",
            data: {
                id:id,
                total:total
            },
            success: function (result) {
                if(result.code==200){
                    alert("退款成功！");
                    location.reload();
                }else{
                    alert(result.msg);
                }
            },
            error: function(){
                alert(后端错误或网络错误);
            }
        });
    }
</script>
<br>
<div class="container">
    <table data-toggle="table" data-pagination="true" data-search="true" data-page-size="8" >
        <thead>
            <tr>
                <th>订单编号</th>
                <th>券码类型</th>
                <th>关联券码</th>
                <th>出库时间</th>
                <th>手机号码</th>
                <th>支付宝单号</th>
                <th>订单操作</th>
            </tr>
        </thead>
        <tbody>
            <!--此处是表格内容-->
            <?php
                $res = $PDO->query("SELECT * from `coupons` WHERE `coupon_status`='1' order by `use_time` desc;");
                while($result=$res->fetch(PDO::FETCH_ASSOC)){
                    $id = $result['id'];
                    $trade = $result['tradeno'];
                    $type = $result['coupon_type'];
                    $coupon = $result['coupon'];
                    $buy_time = $result['buy_time'];
                    $out_time = $result['use_time'];
                    $mobile = $result['mobile'];
                    $ali_trade = $result['alipay_tradeno'];
                    
                    $coupon_text = $PDO->query("SELECT * from `coupon_text` WHERE `coupon_type`='$type';");
                    $coupon_text = $coupon_text->fetch(PDO::FETCH_ASSOC);
                    $type = $coupon_text['name'];
                    $total = $coupon_text['total'];
                    
                    $control = "<button type='button' class='btn btn-danger' id='$id' onclick='fuck($id,$total);'>关闭</button>";
                    echo "<tr><td>$trade</td><td>$type</td><td>$coupon</td><td>$out_time</td><td>$mobile</td><td>$ali_trade</td><td>$control</td></tr>";
                }
            ?>
        </tbody>
    </table>
</div>



<?php
}else {
		// 若没有登录
		exit("<script>alert('还没登录，即将为您跳转到登录页面');window.location.href='login';</script>");
	}
?>
