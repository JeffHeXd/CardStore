<?php
header('Content-type:text/html; charset=utf-8');
// 开启Session
session_start();
//确定title
$title = "JStore 商品管理";
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
    <a href="editcoupon?type=add" class="btn btn-primary">新增商品</a><br><br>
    <table data-toggle="table" data-pagination="true" data-page-size="8" >
        <thead>
        <tr>
            <th data-field="id">商品ID</th>
            <th data-field="type">商品标识</th>
            <th data-field="name">商品昵称</th>
            <th data-field="total">商品金额</th>
            <th data-field="contorl">商品操作</th>
        </tr>
        </thead>
        <tbody>
        <?php
            $res = $PDO->query("SELECT * from `coupon_text`;");
            while($result=$res->fetch(PDO::FETCH_ASSOC)){
                $id = $result['id'];
                $type = $result['coupon_type'];
                $name = $result['name'];
                $total = $result['total'];
                //$control = "<button type='button' class='btn btn-primary' id='$id' onclick='fuck($id);'>修改信息</button>";
                $edit = "<a href='editcoupon?id=$id' class='btn btn-primary'>修改信息</a>";
                echo "<tr><td>$id</td><td>$type</td><td>$name</td><td>$total</td><td>$edit</td></tr>";
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
