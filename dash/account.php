<?php 
	header('Content-type:text/html; charset=utf-8');
	// 开启Session
	session_start();
	//确定title
	$title = "JStore 账户管理";
	require('header.php');
 
	// 首先判断Cookie是否有记住了用户信息
	if (isset($_COOKIE['username'])) {
		# 若记住了用户信息,则直接传给Session
		$_SESSION['username'] = $_COOKIE['username'];
		$_SESSION['islogin'] = 1;
	}
	if (isset($_SESSION['islogin'])) {
	    $username = $_SESSION['username'];
	    $PDO = new PDO('mysql:host=localhost;dbname=数据库名', '账号', '密码');
	    $result = $PDO->query("select * from `admin` where `username`='$username';");
	    $result = $result->fetch(PDO::FETCH_ASSOC);
	    
?>
<br>
<div class="container">
    <div class="card">
        <div class="card-header">账户信息维护</div>
        <div class="card-body">
            <form action="##" id="form1">
              <div class="form-group">
                <label for="adminaccount">管理员账号</label>
                <input type="text" class="form-control" id="username" aria-describedby="username" name="username" readonly="readonly" value="<?php echo $result['username'];?>">
                <small id="emailHelp" class="form-text text-muted">用于登录面板的用户名，不可更改</small>
              </div>
              <div class="form-group">
                <label for="adminaccount">管理员称谓</label>
                <input type="text" class="form-control" id="name" aria-describedby="name" name="name" value="<?php echo $result['name'];?>">
                <small id="emailHelp" class="form-text text-muted">用于登录面板的称谓，可更改</small>
              </div>
              <div class="form-group">
                  【若无需修改密码请勿修改】
              </div>
              <div class="form-group">
                <label for="adminpasswd">新密码</label>
                <input type="password" class="form-control" id="password" name="password" placeholder="输入新的密码">
              </div>
              <div class="form-group">
                <label for="adminpasswd">重复一次</label>
                <input type="password" class="form-control" id="repassword" name="repassword" placeholder="再次输入新的密码">
              </div>

              <button type="submit" class="btn btn-primary" onclick="fuck();" id="fuckme">提交修改</button>
            </form>
        </div>
    </div>
    <script src="https://cdn.bootcss.com/blueimp-md5/2.10.0/js/md5.js"></script>
    <script>
        function submit(){
            $.ajax({
                type: "POST",
                dataType: "json",
                url: "ajax?type=account",
                data: $('#form1').serialize(),
                success: function (result) {
                    if (result.code==200){
                        alert("更改成功，当前用户信息失效，请重新登录！");
                        window.location.href='ajax?type=loginout';
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
            var passwd = $("#password").val();
            var repasswd = $("#repassword").val();
            var username  = $("#name").val();
            if (username != '') {
                if (passwd != '' && repasswd != '' && passwd == repasswd) {
                    $("#password").val(md5(passwd));
                    $("#repassword").val(md5(repasswd));
                }
                $("#fuckme").attr("disabled", "disabled");
                $("#fuckme").html("请稍后...");
                submit();
            }
        }
    </script>
</div>

<?php
}else {
		// 若没有登录
		exit("<script>alert('还没登录，即将为您跳转到登录页面');window.location.href='login';</script>");
	}
?>
