<?php
    error_log(0);
    header('Content-type:text/html; charset=utf-8');
    session_start();
    $type = $_REQUEST['type'];
    $user = $_SESSION['username'];
    $date = date("Y-m-d H:i:s");
    #连接数据库
    $PDO = new PDO('mysql:host=localhost;dbname=数据库名', '账号', '密码');
    #定义输出消息函数
    function msg($code,$msg){
        $out = array();
        $out['code'] = $code;
        $out['msg'] = $msg;
        $out_string = json_encode($out,true);
        return $out_string;
    }
    function getadress($ip){
        $result_ip = file_get_contents("http://ip.taobao.com/outGetIpInfo?accessKey=alibaba-inc&ip=$ip");
        $Arr=json_decode($result_ip,true);
        $isp = $Arr['data']['isp'];
        $regon = $Arr['data']['regon'];
        $city = $Arr['data']['city'];
        $country = $Arr['data']['country'];
        return $country.$regon.$city.$isp;
    }

    if($type=='login'){
        require('geetest.php');
        $log_adress = getadress($ip);
        $username = trim($_POST['username']);
        $password = trim($_POST['passwd']);
        if ($username == '' || $password == '') {
            exit(msg('400', '账号或密码为空！'));
        }
        #校验账号密码
        $check = $PDO->query("SELECT * FROM `admin` WHERE `username`='$username' AND `passwd`='$password';");
        $check = $check->fetch(PDO::FETCH_ASSOC);
        $user = $check['username'];
        $pass = $check['passwd'];
        $name = $check['name'];
        if ($username == $user and $password == $pass) {
            $PDO->query("UPDATE admin SET last_login_time='$date',last_login_ip='$ip',last_login_adress='$log_adress' WHERE username='$user';");
            $PDO->query("INSERT INTO `login_log`(`username`, `login_time`, `login_status`, `type`) VALUES ('$user','$date','成功','登录');");
            echo msg('200', '登录成功，正在为您跳转！');
            $_SESSION['username'] = $user;
            $_SESSION['name'] = $name;
            $_SESSION['islogin'] = 1;
        } else {
            $PDO->query("INSERT INTO `login_log`(`username`, `login_time`, `login_status`, `type`) VALUES ('$user','$date','失败','登录');");
            exit(msg('400', '登录失败！账号或密码错误，已记录错误次数'));
        }
    }

?>
<?php
if (isset($_SESSION['islogin'])) {
    switch ($type) {
        case 'account':
            /* *
            账户称谓
            新密码
            重复输入新密码
             * */
            $account_name = trim($_POST['name']);
            $password = trim($_POST['password']);
            $repasswd = trim($_POST['repassword']);
            $user = $_SESSION['username'];

            if (!empty($account_name) and !empty($password) and !empty($repasswd)) {
                if ($password != $repasswd) {
                    exit(msg(500, '新密码和重复密码不一致，请核对后再提交。本次操作已写入日志！'));
                    #将操作写入日志
                    $PDO->query("INSERT INTO `login_log`(`username`, `login_time`, `login_status`, `type`) VALUES ('$user','$date','失败','更改用户信息');");
                }
                #更改账户称谓+密码
                $PDO->query("UPDATE admin SET name='$account_name',passwd='$password' WHERE username='$user';");
                #将操作写入日志
                $PDO->query("INSERT INTO `login_log`(`username`, `login_time`, `login_status`, `type`) VALUES ('$user','$date','成功','更改密码');");
                echo msg(200, "更改账户称谓和密码成功！");
            }
            if (!empty($password) and !empty($repasswd) and empty($account_name)) {
                if ($password != $repasswd) {
                    exit(msg(500, '新密码和重复密码不一致，请核对后再提交。本次操作已写入日志！'));
                }
                #更改密码
                $PDO->query("UPDATE admin SET passwd='$password' WHERE username='$user';");
                #将操作写入日志
                $PDO->query("INSERT INTO `login_log`(`username`, `login_time`, `login_status`, `type`) VALUES ('$user','$date','成功','更改密码');");
                echo msg(200, "更改账户密码成功！");
            }
            if (empty($password) and empty($repasswd) and !empty($account_name)) {
                #更改账户称谓
                $PDO->query("UPDATE admin SET name='$account_name' WHERE username='$user';");
                #将操作写入日志
                $PDO->query("INSERT INTO `login_log`(`username`, `login_time`, `login_status`, `type`) VALUES ('$user','$date','成功','更改名称');");
                echo msg(200, "更改账户称谓成功！");
            }
            if (empty($password) and empty($repasswd) and empty($account_name)) {
                #将操作写入日志
                $PDO->query("INSERT INTO `login_log`(`username`, `login_time`, `login_status`, `type`) VALUES ('$user','$date','失败','更改用户信息');");
                exit(msg(500, '可选参数不能为空，请重新提交。本次操作已写入日志！'));
            }
            break;

        case 'editcoupon':
            $id = $_POST['id'];
            $coupon_type = $_POST['coupon_type'];
            $name = $_POST['name'];
            $total = $_POST['total'];
            $text = $_POST['text'];
            if ($id==''||$coupon_type==''||$name==''||$total==''||$text==''){
                #将操作写入日志
                $PDO->query("INSERT INTO `login_log`(`username`, `login_time`, `login_status`, `type`) VALUES ('$user','$date','失败','更改商品信息');");
                exit(msg(500,'任意参数不得为空，请重新填写！'));
            }else{
                $PDO->query("UPDATE coupon_text SET coupon_type='$coupon_type',name='$name',total='$total',text='$text' WHERE `id`='$id';");
                #将操作写入日志
                $PDO->query("INSERT INTO `login_log`(`username`, `login_time`, `login_status`, `type`) VALUES ('$user','$date','成功','更改商品信息');");
                exit(msg(200,'更改成功，请留意生效情况！'));
            }
            break;
        case 'addcoupon':
            $coupon_type = $_POST['coupon_type'];
            $name = $_POST['name'];
            $total = $_POST['total'];
            $text = $_POST['text'];
            if ($coupon_type==''||$name==''||$total==''||$text==''){
                #将操作写入日志
                $PDO->query("INSERT INTO `login_log`(`username`, `login_time`, `login_status`, `type`) VALUES ('$user','$date','失败','添加商品');");
                exit(msg(500,'任意参数不得为空，请重新填写！'));
            }else{
                $PDO->query("INSERT INTO `coupon_text`(`coupon_type`, `name`, `total`, `text`) VALUES ('$coupon_type','$name','$total','$text');");
                #将操作写入日志
                $PDO->query("INSERT INTO `login_log`(`username`, `login_time`, `login_status`, `type`) VALUES ('$user','$date','成功','添加商品');");
                exit(msg(200,'添加成功，请留意生效情况！'));
            }
            break;
        case 'deletecoupon':
            $id = $_POST['id'];
            if (!empty($id)){
                #将操作写入日志
                $PDO->query("INSERT INTO `login_log`(`username`, `login_time`, `login_status`, `type`) VALUES ('$user','$date','成功','删除商品');");
                $PDO->query("DELETE FROM `coupon_text` WHERE `id`='$id';");
                exit(msg(200,'删除成功，请留意生效情况！'));
            }else{
                #将操作写入日志
                $PDO->query("INSERT INTO `login_log`(`username`, `login_time`, `login_status`, `type`) VALUES ('$user','$date','失败','删除商品');");
                exit(msg(500,'任意参数不得为空，请重新填写！'));
            }
            break;
        case 'loginout':
            $_SESSION = array();
            session_destroy();
            // 清除Cookie
            setcookie('username', '', time() - 99);
            setcookie('code', '', time() - 99);
            #将操作写入日志
            $PDO->query("INSERT INTO `login_log`(`username`, `login_time`, `login_status`, `type`) VALUES ('$user','$date','成功','登出');");
            //输出信息
            //echo msg('200','注销成功');
            echo "<script>window.location.href='login';</script>";
            break;

        default:
            // code...
            break;
    }
}else {
    // 若没有登录
    exit("<script>alert('还没登录，即将为您跳转到登录页面');window.location.href='login';</script>");
}



?>
