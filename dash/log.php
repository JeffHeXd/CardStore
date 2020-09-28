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


    <br>
    <div class="container">
        <table data-toggle="table" data-pagination="true" data-search="true" data-page-size="8" >
            <thead>
            <tr>
                <th>事件ID</th>
                <th>操作用户</th>
                <th>操作时间</th>
                <th>请求状态</th>
                <th>事件类型</th>
            </tr>
            </thead>
            <tbody>
            <!--此处是表格内容-->
            <?php
            $res = $PDO->query("SELECT * from `login_log` WHERE 1 order by `login_time` desc;");
            while($result=$res->fetch(PDO::FETCH_ASSOC)){
                $id = $result['id'];
                $user = $result['username'];
                $time = $result['login_time'];
                $status = $result['login_status'];
                $type = $result['type'];

                $control = "<button type='button' class='btn btn-danger' id='$id' onclick='fuck($id,$total);'>关闭</button>";
                echo "<tr><td>$id</td><td>$user</td><td>$time</td><td>$status</td><td>$type</td></tr>";
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
