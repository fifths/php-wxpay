<?php

error_reporting(E_ALL & ~E_NOTICE);

require_once __DIR__ . '/../vendor/autoload.php';

ini_set('date.timezone','Asia/Shanghai');

$config = include ('config.php');

$app = \Wechat\Factory::WxPay($config);

$url1 = $app->GetPrePayUrl("123456789");


$data = [
    'body' => 'test', // 必填 设置商品或支付单简要描述
    // 'attach' => 'test', // 设置附加数据，在查询API和支付通知中原样返回，该字段主要用于商户携带订单的自定义数据
    'out_trade_no' => $config['mch_id']. date("YmdHis"), //必填 设置商户系统内部的订单号,32个字符内、可包含字母, 其他说明见商户订单号
    'total_fee' => 1, //必填 设置订单总金额，只能为整数，详见支付金额
    // 'time_start' => date("YmdHis"), // 设置订单生成时间，格式为yyyyMMddHHmmss，如2009年12月25日9点10分10秒表示为20091225091010。其他详见时间规则
    // 'time_expire' => date("YmdHis", time() + 600), // 设置订单失效时间，格式为yyyyMMddHHmmss，如2009年12月27日9点10分10秒表示为20091227091010。其他详见时间规则
    // 'goods_tag' => 'test', // 设置商品标记，代金券或立减优惠功能的参数，说明详见代金券或立减优惠
    'notify_url' => 'http://47.100.184.3/wechat/example/notify.php', //必填 异步接收微信支付结果通知的回调地址，通知url必须为外网可访问的url，不能携带参数。
    'trade_type' => 'NATIVE', //必填 交易类型 设置取值如下：JSAPI，NATIVE，APP，详细说明见参数规定
    'product_id' => '123456789', // trade_type=NATIVE时（即扫码支付），此参数必传。此参数为二维码中包含的商品ID，商户自行定义。
];
$result = $app->unifiedOrder($data);

// var_dump($result);
$url2 = $result["code_url"];
?>
<html>
<head>
    <meta http-equiv="content-type" content="text/html;charset=utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <title>微信支付样例-退款</title>
</head>
<body>
<div style="margin-left: 10px;color:#556B2F;font-size:30px;font-weight: bolder;">扫描支付模式一</div>
<br/>
<img alt="模式一扫码支付" src="./qrcode.php?data=<?php echo urlencode($url1); ?>"
     style="width:150px;height:150px;"/>
<br/><br/><br/>
<div style="margin-left: 10px;color:#556B2F;font-size:30px;font-weight: bolder;">扫描支付模式二</div>
<br/>
<img alt="模式二扫码支付" src="./qrcode.php?data=<?php echo urlencode($url2); ?>"
     style="width:150px;height:150px;"/>

</body>
</html>
