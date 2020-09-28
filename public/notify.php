<?php
/**订单预处理，防止重复入库**/
#现在时间
$now = date("Y-m-d H:i:s");
#获取商家端订单号
$trade = $_POST['out_trade_no'];
#获取订单付款时间
$payment_time = $_POST['gmt_payment'];
#获取支付宝端订单号
$alipay_trade = $_POST['trade_no'];
#获取支付宝端订单状态
$trade_status = $_POST['trade_status'];
#连接数据库
$PDO = new PDO('mysql:host=localhost;dbname=数据库名', '账号', '密码');
#查询订单是否支付完成
$check_order = $PDO->query("SELECT * from `order` WHERE `tradeno`='$trade' AND `payment_status`='1';");
$check_order = $check_order->fetch(PDO::FETCH_ASSOC);
$result_status = $check_order['payment_status'];
if($result_status == 1){exit("success");}
?>
<?php
header('Content-type:text/html; Charset=utf-8');
$alipayPublicKey='';
$aliPay = new AlipayService($alipayPublicKey);
$result = $aliPay->rsaCheck($_POST,$_POST['sign_type']);
if($result===true){
    echo 'success';
    if($trade_status == 'TRADE_SUCCESS'){
        //更改订单状态
        $PDO->query("UPDATE `order` SET `payment_time`='$payment_time',`alipay_tradeno`='$alipay_trade',`payment_status`='1' WHERE `tradeno`='$trade';");
        //更改卡密状态
        $PDO->query("UPDATE coupons SET coupon_status='1',use_time='$now',alipay_tradeno='$alipay_trade' WHERE tradeno='$trade';");
        //Server酱通知微信
        file_get_contents("");
        exit();
    }
    exit();
}
exit();
class AlipayService
{
    //支付宝公钥
    protected $alipayPublicKey;
    protected $charset;

    public function __construct($alipayPublicKey)
    {
        $this->charset = 'utf8';
        $this->alipayPublicKey=$alipayPublicKey;
    }

    /**
     *  验证签名
     **/
    public function rsaCheck($params) {
        $sign = $params['sign'];
        $signType = $params['sign_type'];
        unset($params['sign_type']);
        unset($params['sign']);
        return $this->verify($this->getSignContent($params), $sign, $signType);
    }

    function verify($data, $sign, $signType = 'RSA') {
        $pubKey= $this->alipayPublicKey;
        $res = "-----BEGIN PUBLIC KEY-----\n" .
            wordwrap($pubKey, 64, "\n", true) .
            "\n-----END PUBLIC KEY-----";
        ($res) or die('支付宝RSA公钥错误。请检查公钥文件格式是否正确');

        //调用openssl内置方法验签，返回bool值
        if ("RSA2" == $signType) {
            $result = (bool)openssl_verify($data, base64_decode($sign), $res, version_compare(PHP_VERSION,'5.4.0', '<') ? SHA256 : OPENSSL_ALGO_SHA256);
        } else {
            $result = (bool)openssl_verify($data, base64_decode($sign), $res);
        }
//        if(!$this->checkEmpty($this->alipayPublicKey)) {
//            //释放资源
//            openssl_free_key($res);
//        }
        return $result;
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
}
?>
