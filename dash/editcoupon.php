<?php
    header('Content-type:text/html; charset=utf-8');
    // 开启Session
    session_start();
    //确定title
    $title = "JStore 修改商品";
    require('header.php');

    // 首先判断Cookie是否有记住了用户信息
    if (isset($_COOKIE['username'])) {
        # 若记住了用户信息,则直接传给Session
        $_SESSION['username'] = $_COOKIE['username'];
        $_SESSION['islogin'] = 1;
    }
    if (isset($_SESSION['islogin'])) {
        $PDO = new PDO('mysql:host=localhost;dbname=数据库名', '账号', '密码');
        $res = $PDO->query("SELECT * from `coupon_text` WHERE `id`='$id';");
        $res = $res->fetch(PDO::FETCH_ASSOC);
        $type = $_GET['type'];
?>

<br>
<div class="container">
    <a href="coupons_type">< 返回商品管理页</a>
    <div class="card">
        <div class="card-header">修改商品信息(<?php echo $res['id'];?>):</div>
        <div class="card-body">
            <form action="##" method="POST" id="form1">
                <div class="form-group">
                    <label for="couponid">商品ID：</label>
                    <input type="number" class="form-control" id="couponid" aria-describedby="couponid" name="id" readonly="readonly" value="<?php echo $res['id'];?>">
                    <small id="Help" class="form-text text-muted">每个商品独有的ID，无法更改！</small>
                </div>
                <div class="form-group">
                    <label for="couponname">商品编码：</label>
                    <input type="text" class="form-control" id="coupontype" aria-describedby="couponname" name="coupon_type" value="<?php echo $res['coupon_type'];?>">
                    <small id="Help" class="form-text text-muted">每个商品独有的编码，谨慎更改！</small>
                </div>
                <div class="form-group">
                    <label for="couponname">商品名称：</label>
                    <input type="text" class="form-control" id="couponname" aria-describedby="couponname" name="name" value="<?php echo $res['name'];?>">
                </div>
                <div class="form-group">
                    <label for="couponid">商品金额（人民币）：</label>
                    <input type="number" class="form-control" id="coupontotal" aria-describedby="coupontotal" name="total" value="<?php echo $res['total'];?>">
                </div>
                <div class="form-group">
                    <label for="couponid">商品描述(支持HTML)：</label>
                    <textarea class="form-control" name="text" rows="5" id="text"><?php echo $res['text'];?></textarea>
                </div>
                <?php
                if ($type == 'add') {
                    echo '<button type = "button" class="btn btn-primary" onclick = "add();" id = "fuckme" > 添加商品</button>';
                }else{
                    echo '<button type = "button" class="btn btn-primary" onclick = "fuck();" id = "fuckme" > 提交修改</button>';
                }
                ?>
                | <button type = "button" class="btn btn-danger" onclick = "del()" id = "fuckme" > 删除商品</button>
            </form>
        </div>
    </div>
</div>
        <script>
            function d(){
                $.ajax({
                    type: "POST",
                    dataType: "json",
                    url: "ajax?type=deletecoupon",
                    data: $('#form1').serialize(),
                    success: function (result) {
                        if (result.code==200){
                            alert("删除成功！");
                            window.location="https://ldygo.fun/store/dash/coupons_type";
                        }else{
                            alert(result.msg);
                        }
                    },
                    error: function(){
                        alert("更改失败，后端返回错误，可查看日志！");

                    }
                });
            }

            function del(){
                var msg = "请确认删除商品，删除后不可恢复！";
                if(confirm(msg)){
                    d();
                    console.log("执行删除商品");
                }else{
                    return false;
                }
            };
            function go(){
                $.ajax({
                    type: "POST",
                    dataType: "json",
                    url: "ajax?type=editcoupon",
                    data: $('#form1').serialize(),
                    success: function (result) {
                        if (result.code==200){
                            alert("更改成功！");
                        }else{
                            alert(result.msg);
                        }
                    },
                    error: function(){
                        alert("更改失败，后端返回错误，可查看日志！");

                    }
                });
            }
            function addsubmit(){
                $.ajax({
                    type: "POST",
                    dataType: "json",
                    url: "ajax?type=addcoupon",
                    data: $('#form1').serialize(),
                    success: function (result) {
                        if (result.code==200){
                            alert("添加成功！");
                        }else{
                            alert(result.msg);
                        }
                    },
                    error: function(){
                        alert("更改失败，后端返回错误，可查看日志！");

                    }
                });
            }
            function fuck(){
                var id = $("#id").val();
                var name = $("#name").val();
                var total = $("#total").val();
                var text = $("#text").val();
                if (id==''||name==''||total==''||text==''){
                    alert("不允许留空，请检查填写内容！");
                }else{
                    console.log("Go");
                    go();
                }
            }
            function add(){
                var id = $("#id").val();
                var name = $("#name").val();
                var total = $("#total").val();
                var text = $("#text").val();
                if (name==''||total==''||text==''){
                    alert("不允许留空，请检查填写内容！");
                }else{
                    console.log("Go");
                    addsubmit();
                }
            }
        </script>

<?php
    }else {
        // 若没有登录
        exit("<script>alert('还没登录，即将为您跳转到登录页面');window.location.href='login';</script>");
    }
?>
