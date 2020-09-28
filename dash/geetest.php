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