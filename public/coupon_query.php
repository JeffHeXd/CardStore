<?php
require_once '../../gt/lib/class.geetestlib.php';
require_once '../../gt/config/config.php';
header('Content-type:text/html; Charset=utf-8');
/**识别cdnIP**/
if ($HTTP_SERVER_VARS["HTTP_X_FORWARDED_FOR"]){$ip = $HTTP_SERVER_VARS["HTTP_X_FORWARDED_FOR"];}
elseif ($HTTP_SERVER_VARS["HTTP_CLIENT_IP"]){$ip = $HTTP_SERVER_VARS["HTTP_CLIENT_IP"];}
elseif ($HTTP_SERVER_VARS["REMOTE_ADDR"]){$ip = $HTTP_SERVER_VARS["REMOTE_ADDR"];}
elseif (getenv("HTTP_X_FORWARDED_FOR")){$ip = getenv("HTTP_X_FORWARDED_FOR");}
elseif (getenv("HTTP_CLIENT_IP")){$ip = getenv("HTTP_CLIENT_IP");}
elseif (getenv("REMOTE_ADDR")){$ip = getenv("REMOTE_ADDR");}
else{$ip = "127.0.0.1";}
session_start();
$GtSdk = new GeetestLib(CAPTCHA_ID, PRIVATE_KEY);
$data = array(
        "user_id" => $_SESSION['user_id'], # 网站用户id
        "client_type" => $_SESSION['client_type'], #web:电脑上的浏览器；h5:手机上的浏览器，包括移动应用内完全内置的web_view；native：通过原生SDK植入APP应用的方式
        "ip_address" => $ip # 请在此处传输用户请求验证时所携带的IP
    );
if ($_SESSION['gtserver'] == 1) {   //服务器正常
    $result = $GtSdk->success_validate($_POST['geetest_challenge'], $_POST['geetest_validate'], $_POST['geetest_seccode'], $data);
    if ($result) {
        $geek = 'ok';
    } else{
      echo '<script type="text/javascript">alert("错误:极验验证验证失败，请检查是否已完成验证码或重试");</script>';
      $geek = 'no';
    }
}else{  //服务器宕机,走failback模式
    if ($GtSdk->fail_validate($_POST['geetest_challenge'],$_POST['geetest_validate'],$_POST['geetest_seccode'])) {
        $geek = 'ok';
    }else{
        echo '<script type="text/javascript">alert("错误:极验验证验证失败，请检查是否已完成验证码或重试");"</script>';
        $geek = 'no';
    }
}
?>
<!DOCTYPE html>
<html lang="zh-CN">
    <head>
        <meta charset="utf-8">
        <link rel="icon" type="image/png" href="../../../favicon.png" /> 
        <meta name="keywords" content="联动云企业用车登记,共享汽车,联动云企业优惠,联动云95折企业优惠,联动云优惠用车" /> 
        <meta name="description" content="联动云企业用车,自助加入企业用户，享受用车充值95折优惠" /> 
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
        <title>Jstore - 卡密查询</title>
        <!--此处加载CSS文件-->
        <link href="../../../src/css/all.min.css" rel="stylesheet" type="text/css" /> 
        <link href="../../../src/css/sb-admin.css" rel="stylesheet" />
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.4.1/dist/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous" />
        <link rel="stylesheet" href="https://unpkg.com/bootstrap-table@1.15.3/dist/bootstrap-table.min.css">
        <!--此处加载必要的JS文件-->
        <script src="https://cdn.jsdelivr.net/npm/jquery@3.4.1/dist/jquery.slim.min.js" integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.4.1/dist/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>
        <script src="https://cdn.bootcdn.net/ajax/libs/jquery/3.5.1/jquery.js"></script>
        <script src="https://cdn.bootcdn.net/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.4.1/dist/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>
        <script src="https://unpkg.com/bootstrap-table@1.15.3/dist/bootstrap-table.min.js"></script>
        <script src="https://cdn.bootcdn.net/ajax/libs/jquery.qrcode/1.0/jquery.qrcode.min.js"></script>
        <style>
            body{
                background-color:#778899;
            }
            .cnfont{
                font-family:Avenir,Helvetica,Arial,sans-serif; 
                color: #606266;
                font-weight:bold
            }
        </style>
    </head>
    <body>
    <br>
        <div class="container">
            <div class="card">
                <div class="card-header"><center>卡密查询</center></div>
                <div class="card-body">
                    
                    <p class="cnfont" style="text-align:center">感谢购买 欢迎下次再来</p>
                    <p class="cnfont" style="color:red">兑换方式如下：<br>1、【联动云APP】-->>【左上角小人】<br>2、【钱包】-->>【优惠券】<br>3、【右上角兑换】-->【选择租车券】</p>
                    <div class="alert alert-danger" role="alert" id="alertcheek" style="display:none">
                        <center>请进行人机验证后查询<br><a href="https://ldygo.fun/static/store/query">返回验证</a></center>
                    </div>
                    
                    <!--表格-->
                    <table data-toggle="table" id="show">
                        <thead>
                            <tr>
                                <th>购买时间</th>
                                <th>卡密内容</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            #连接数据库
                            $PDO = new PDO('mysql:host=localhost;dbname=数据库名', '账号', '密码');
                            $tradeno = $_REQUEST['tradeno'];
                            $mobile = $_REQUEST['mobile'];
                            if(!empty($mobile)){
                                $res = $PDO->query("SELECT * from `coupons` WHERE `mobile`='$mobile' AND `coupon_status`='1' order by `use_time` desc;");
                                while($result=$res->fetch(PDO::FETCH_ASSOC)){
                                    $trade = $result['tradeno'];
                                    $buy_time = $result['buy_time'];
                                    $coupon_value = $result['coupon'];
                                    echo "<tr><td>$buy_time</td><td>$coupon_value</td></tr>";
                                }
                            }else{$geek='no';}
                            ?>
                        </tbody>
                    </table>
                    
                    
                </div>
                <div class="card-footer text-muted">
                    <div style="font-size:10px;text-align:center">
                        <a href="http://wpa.qq.com/msgrd?v=3&uin=734430160&site=qq&menu=yes">联系客服(09:00~17:00)</a>
                        <br>本站位于中国·香港并遵循当地法律法规<br>ldygo.fun © 版权所有
                    </div>
                </div>
            </div>
        </div>
        <script>
            var geek = '<?php echo $geek; ?>';
            if(geek=='' || geek=='no'){
                $("#show").css('display','none');
                $("#alertcheek").css('display','block');
            }
        </script>
    </body>
</html>
