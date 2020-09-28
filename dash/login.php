<?php

	header('Content-type:text/html; charset=utf-8');
	// 开启Session
	session_start();
 
	// 首先判断Cookie是否有记住了用户信息
	if (isset($_COOKIE['username'])) {
		# 若记住了用户信息,则直接传给Session
		$_SESSION['username'] = $_COOKIE['username'];
		$_SESSION['islogin'] = 1;
	}
	if (isset($_SESSION['islogin'])) {
		// 若已经登录
		echo "<script>window.location.href='index.php'</script>";
	}
	
	$text = $_GET['text'];
    $title = '登录页面';
    require('login_header.php');
?>
<body><br>
    <div class="container">
            <div class="card">
                <div class="card-header"><center>后台登录</center></div>
                <div class="card-body">
                    <p class="cnfont" style="text-align:center">请先完成登录</p>
                    <?php
                        if($text){
                            echo "<center><div class='alert alert-danger' role='alert'>$text</div></center>";
                        }
                    ?>
                    <form action="##" method="POST" id="form1">
                        <div class="form-group">
                            <div class="form-label-group">
                                <input type="text" name="username" id="username" class="form-control" required="required">
                                <label for="inputUsername">账户名：</label>
                                <div class="invalid-feedback">请输入正确的账户名</div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="form-label-group">
                                <input type="password" name="passwd" id="passwd" class="form-control" required="required">
                                <label for="inputUsername">管理员密码：</label>
                                <div class="invalid-feedback">请输入正确的密码</div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div id="embed-captcha" class="small"></div>
                            <p id="wait" class="cnfont" style="text-align:center;font-size:13px">正在加载验证码，若长时间未加载请刷新页面</p>
                        </div>
                        <button type="button" id="fuckme" class="btn btn-primary btn-block" disabled="disabled "onclick="cheeck_form()">登录</button>
                    </form>
                </div>
                <div class="card-footer text-muted">
                    <div style="font-size:10px;text-align:center">
                        本站位于中国·香港并遵循当地法律法规<br>ldygo.fun © 版权所有
                    </div>
                </div>
            </div>
            <script>
            //登录页面
            function login(){
                console.log("已发送登录请求，等待服务器返回");
                $.ajax({
                    type: "POST",
                    dataType: "json",
                    url: "ajax?type=login",
                    time: 10000,
                    data: $('#form1').serialize(),
                    success: function (result) {
                        console.log(result);
                        if(result.code == 200){
                            alert("登录成功，即将为您跳转到首页");
                            window.location.href='index';
                        }else{
                            console.log(result.msg);
                            alert(result.msg);
                            location.reload();
                        };
                    },
                    error: function(){
                        alert("无法登录，请刷新再试");
                        $("#fuckme").attr("disabled", "false");
                        $("#fuckme").html("尝试再次登录");
                    }
                });
            };
            //验证表单
            function cheeck_form(){
                var user = document.getElementById("username").value.length;
                var pass = document.getElementById("passwd").value.length;
                var passwd = $("#passwd").val();
                if(user != 0 && pass != 0){
                    $("#passwd").val(md5(passwd));
                    $("#username").attr("class", "form-control");
                    $("#passwd").attr("class", "form-control");
                    document.getElementById("username").readOnly=true;
                    document.getElementById("passwd").readOnly=true;
                    $("#fuckme").attr("disabled", "disabled");
                    $("#fuckme").html("请稍后...");
                    login();
                }else{
                    $("#username").addClass("is-invalid");
                    $("#passwd").addClass("is-invalid");
                }
                
            }
                //验证代码
            var handlerEmbed = function (captchaObj) {
                $("#submit").click(function (e) {
                    var validate = captchaObj.getValidate();
                    if (!validate) {
                        $("#notice")[0].style = "";
                        setTimeout(function () {
                            $("#notice")[0].style = "display:none;";
                        }, 2000);
                        e.preventDefault();
                    }
                });
                // 将验证码加到id为captcha的元素里，同时会有三个input的值：geetest_challenge, geetest_validate, geetest_seccode
                captchaObj.appendTo("#embed-captcha");
                captchaObj.onReady(function () {
                    $("#wait")[0].style = "display:none;";
                });
                //验证成功后操作
                captchaObj.onSuccess(function () {
                	//cheek();
                	document.getElementById("fuckme").disabled=false;
                	document.getElementById("smscode").readOnly=true;
                	//document.getElementById("tips").style='display:none';
                	//$("#geekcheck")[0].style = "display:;";
                // 出错啦，可以提醒用户稍后进行重试
                // error 包含error_code、msg
            });
                // 更多接口参考：http://www.geetest.com/install/sections/idx-client-sdk.html
            };
            $.ajax({
                // 获取id，challenge，success（是否启用failback）
                url: "../../gt/web/StartCaptchaServlet.php?t=" + (new Date()).getTime(), // 加随机数防止缓存
                type: "get",
                dataType: "json",
                success: function (data) {
                    console.log(data);
                    // 使用initGeetest接口
                    // 参数1：配置参数
                    // 参数2：回调，回调的第一个参数验证码对象，之后可以使用它做appendTo之类的事件
                    initGeetest({
                        gt: data.gt,
                        challenge: data.challenge,
                        new_captcha: data.new_captcha,
                        product: "float", // 产品形式，包括：float，embed，popup。注意只对PC版验证码有效
                        offline: !data.success, // 表示用户后台检测极验服务器是否宕机，一般不需要关注
                        width: '100%',
                        lang: 'zh-cn',
                        // 更多配置参数请参见：http://www.geetest.com/install/sections/idx-client-sdk.html#config
                    }, handlerEmbed);
                }
            });
            </script>
</body>