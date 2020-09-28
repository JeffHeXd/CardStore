<?php
$trade = $_REQUEST['trade'];
$mobile = $_REQUEST['mobile'];
?>
<!DOCTYPE html>
<html lang="zh-CN">
    <head>
        <meta charset="utf-8">
        <link rel="icon" type="image/png" href="../../favicon.png" /> 
        <meta name="keywords" content="联动云企业用车登记,共享汽车,联动云企业优惠,联动云95折企业优惠,联动云优惠用车" /> 
        <meta name="description" content="联动云企业用车,自助加入企业用户，享受用车充值95折优惠" /> 
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
        <title>Jstore - 卡密查询</title>
        <!--此处加载CSS文件-->
        <link href="../../src/css/all.min.css" rel="stylesheet" type="text/css" /> 
        <link href="../../src/css/sb-admin.css" rel="stylesheet" />
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.4.1/dist/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous" />
        <!--此处加载必要的JS文件-->
        <script src="https://cdn.jsdelivr.net/npm/jquery@3.4.1/dist/jquery.slim.min.js" integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.4.1/dist/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>
        <script src="https://cdn.bootcdn.net/ajax/libs/jquery/3.5.1/jquery.js"></script>
        <script src="https://cdn.bootcdn.net/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.4.1/dist/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>
        <script src="https://cdn.bootcdn.net/ajax/libs/jquery.qrcode/1.0/jquery.qrcode.min.js"></script>
        <script src="../gt/static/gt.js"></script>
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
                    <p class="cnfont" style="text-align:center">只能查询当日购买的卡密</p>
                    
                    <form action="public/coupon_query" method="POST" id="form1">
                        <div class="form-group">
                            <div class="form-label-group" id="trade_num">
                                <input type="text" name="tradeno" id="tradeno" class="form-control" required="required">
                                <label for="inputTrade">订单编号(5f开头)：</label>
                                <div class="invalid-feedback">请输入正确的订单号</div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="form-label-group">
                                <input type="text" name="mobile" id="mobile" class="form-control" value="<?php echo $mobile;?>" required="required">
                                <label for="inputMobile">手机号码(购买时预留)：</label>
                                <div class="invalid-feedback">请输入正确的手机号码</div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div id="embed-captcha" class="small"></div>
                            <p id="wait" class="cnfont" style="text-align:center;font-size:13px">正在加载验证码，若长时间未加载请刷新页面</p>
                        </div>
                        <button type="button" id="fuckme" class="btn btn-primary btn-block" disabled="disabled "onclick="cheeck_form()">提交订单</button>
                    </form>
                    
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
            //订单号校验框
            window.onload=function(){
                var trade_status = '<?php echo $trade; ?>';
                if(trade_status==''){
                    $("#trade_num").css('display','none');
                }else{
                    $("#tradeno").attr("readonly","readonly");
                    $("#tradeno").val(trade_status);
                }
            }
            //查询订单
            function query(){
                $("#form1").submit();
            }
            //提交校验
            function cheeck_form(){
                var mobile = $("#mobile").val();
                if((/^1[3|4|5|7|8]\d{9}$/.test(mobile))){
                    console.log("正则通过");
                    $("#mobile").attr("readonly","readonly");
                    $("#mobile").attr("class", "form-control");
                    query();
                }else{
                    $("#mobile").addClass("is-invalid");
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
                url: "../gt/web/StartCaptchaServlet.php?t=" + (new Date()).getTime(), // 加随机数防止缓存
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
</html>