<?php
header('Content-type:text/html; charset=utf-8');
session_start();
// 首先判断Cookie是否有记住了用户信息
	if (isset($_COOKIE['username'])) {
		# 若记住了用户信息,则直接传给Session
		$_SESSION['username'] = $_COOKIE['username'];
		$_SESSION['islogin'] = 1;
	}
	if (isset($_SESSION['islogin'])) {
        $id = $_POST['id'];
        $total = $_POST['total'];
        #连接数据库查询信息
        $PDO = new PDO('mysql:host=localhost;dbname=数据库名', '账号', '密码');
        #根据ID和金额来查询订单信息
        $res = $PDO->query("SELECT * from `coupons` WHERE `id`='$id';");
        $res = $res->fetch(PDO::FETCH_ASSOC);
        $out_trade = $res['tradeno'];
        $res2 = $PDO->query("SELECT * from `order` WHERE `tradeno`='$out_trade';");
        $res2 = $res2->fetch(PDO::FETCH_ASSOC);
        $total = $res2['total'];
        #获取订单时间，不是当天一律禁止退款
        $order_time = $res['use_time'];
        $tomrrow = date("Y-m-d H:i:s",strtotime("$order_time+1day"));
        $today = date("Y-m-d H:i:s");
        if($tomrrow < $today){
            exit('{"code":"400","msg":"退款失败，超出订单最晚退款时间！"}');
        }
?>
<?php
header('Content-type:text/html; Charset=utf-8');
/*** 请填写以下配置信息 ***/
$appid = '';  //https://open.alipay.com 账户中心->密钥管理->开放平台密钥，填写添加了电脑网站支付的应用的APPID
$outTradeNo = $out_trade;     //订单支付时传入的商户订单号,和支付宝交易号不能同时为空。
$signType = 'RSA2';       //签名算法类型，支持RSA2和RSA，推荐使用RSA2
$refundAmount = $total;       ////需要退款的金额，该金额不能大于订单金额,单位为元，支持两位小数
//商户私钥，填写对应签名算法类型的私钥，如何生成密钥参考：https://docs.open.alipay.com/291/105971和https://docs.open.alipay.com/200/105310
$rsaPrivateKey='';
/*** 配置结束 ***/
$aliPay = new AlipayService();
$aliPay->setAppid($appid);
$aliPay->setRsaPrivateKey($rsaPrivateKey);
$aliPay->setTradeNo($tradeNo);
$aliPay->setOutTradeNo($outTradeNo);
$aliPay->setRefundAmount($refundAmount);
$result = $aliPay->doRefund();
$result = $result['alipay_trade_refund_response'];
if($result['code'] && $result['code']=='10000'){
    echo '{"code":"200","msg":"Refund_OK"}';
    $PDO->query("UPDATE `order` SET payment_status='3' WHERE tradeno='$outTradeNo';");
    $PDO->query("UPDATE coupons SET coupon_status='3' WHERE tradeno='$outTradeNo';");
}else{
    echo $result['msg'].' : '.$result['sub_msg'];
    echo $tradeNo;
}
?>
<?php
	}else {
		// 若没有登录
		exit("<script>alert('还没登录，即将为您跳转到登录页面');window.location.href='login';</script>");
	}
 ?>
<?php
class AlipayService
{
    protected $appId;
    protected $returnUrl;
    protected $notifyUrl;
    protected $charset;
    //私钥值
    protected $rsaPrivateKey;
    protected $outTradeNo;
    protected $tradeNo;
    protected $refundAmount;

    public function __construct()
    {
        $this->charset = 'utf-8';
    }
    public function setAppid($appid)
    {
        $this->appId = $appid;
    }
    public function setRsaPrivateKey($saPrivateKey)
    {
        $this->rsaPrivateKey = $saPrivateKey;
    }
    public function setOutTradeNo($outTradeNo)
    {
        $this->outTradeNo = $outTradeNo;
    }
    public function settradeNo($tradeNo)
    {
        $this->tradeNo = $tradeNo;
    }
    public function setRefundAmount($refundAmount)
    {
        $this->refundAmount = $refundAmount;
    }

    /**
     * 退款
     * @return array
     */
    public function doRefund()
    {
        //请求参数
        $requestConfigs = array(
            'trade_no'=>$this->tradeNo,
            'out_trade_no'=>$this->outTradeNo,
            'refund_amount'=>$this->refundAmount,
        );
        $commonConfigs = array(
            //公共参数
            'app_id' => $this->appId,
            'method' => 'alipay.trade.refund',             //接口名称
            'format' => 'JSON',
            'charset'=>$this->charset,
            'sign_type'=>'RSA2',
            'timestamp'=>date('Y-m-d H:i:s'),
            'version'=>'1.0',
            'biz_content'=>json_encode($requestConfigs),
        );
        $commonConfigs["sign"] = $this->generateSign($commonConfigs, $commonConfigs['sign_type']);
        $result = $this->curlPost('https://openapi.alipay.com/gateway.do?charset='.$this->charset,$commonConfigs);
        $resultArr = json_decode($result,true);
        return $resultArr;
    }

    public function generateSign($params, $signType = "RSA") {
        return $this->sign($this->getSignContent($params), $signType);
    }

    protected function sign($data, $signType = "RSA") {
        $priKey=$this->rsaPrivateKey;
        $res = "-----BEGIN RSA PRIVATE KEY-----\n" .
            wordwrap($priKey, 64, "\n", true) .
            "\n-----END RSA PRIVATE KEY-----";
        ($res) or die('您使用的私钥格式错误，请检查RSA私钥配置');
        if ("RSA2" == $signType) {
            openssl_sign($data, $sign, $res, version_compare(PHP_VERSION,'5.4.0', '<') ? SHA256 : OPENSSL_ALGO_SHA256); //OPENSSL_ALGO_SHA256是php5.4.8以上版本才支持
        } else {
            openssl_sign($data, $sign, $res);
        }
        $sign = base64_encode($sign);
        return $sign;
    }

    /**
     * 校验$value是否非空
     *  if not set ,return true;
     *    if is null , return true;
     **/
    protected function checkEmpty($value) {
        if (!isset($value))
            return true;
        if ($value === null)
            return true;
        if (trim($value) === "")
            return true;

        return false;
    }

    public function getSignContent($params) {
        ksort($params);
        $stringToBeSigned = "";
        $i = 0;
        foreach ($params as $k => $v) {
            if (false === $this->checkEmpty($v) && "@" != substr($v, 0, 1)) {
                // 转换成目标字符集
                $v = $this->characet($v, $this->charset);
                if ($i == 0) {
                    $stringToBeSigned .= "$k" . "=" . "$v";
                } else {
                    $stringToBeSigned .= "&" . "$k" . "=" . "$v";
                }
                $i++;
            }
        }

        unset ($k, $v);
        return $stringToBeSigned;
    }

    /**
     * 转换字符集编码
     * @param $data
     * @param $targetCharset
     * @return string
     */
    function characet($data, $targetCharset) {
        if (!empty($data)) {
            $fileType = $this->charset;
            if (strcasecmp($fileType, $targetCharset) != 0) {
                $data = mb_convert_encoding($data, $targetCharset, $fileType);
                //$data = iconv($fileType, $targetCharset.'//IGNORE', $data);
            }
        }
        return $data;
    }

    public function curlPost($url = '', $postData = '', $options = array())
    {
        if (is_array($postData)) {
            $postData = http_build_query($postData);
        }
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30); //设置cURL允许执行的最长秒数
        if (!empty($options)) {
            curl_setopt_array($ch, $options);
        }
        //https请求 不验证证书和host
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        $data = curl_exec($ch);
        curl_close($ch);
        return $data;
    }
}
?>

