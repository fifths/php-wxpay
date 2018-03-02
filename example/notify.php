<?php
ini_set('date.timezone', 'Asia/Shanghai');
error_reporting(E_ERROR);

require_once __DIR__ . '/../vendor/autoload.php';
require_once 'log.php';

//初始化日志
$logHandler = new CLogFileHandler("logs/" . date('Y-m-d') . '.log');
$log = Log::Init($logHandler, 15);
$config = include('config.php');
$app = \Wechat\Factory::WxPay($config);
$result = $app->notify();

Log::DEBUG("begin");

Log::DEBUG("callback:" . json_encode($result, JSON_UNESCAPED_UNICODE));
if ($result != false) {

    $data['transaction_id'] = $result['transaction_id'];
    $result2=$app->orderQuery($data);
    Log::DEBUG("query :" . json_encode($result2, JSON_UNESCAPED_UNICODE));

    $rs['return_code'] = 'SUCCESS';
    $rs['return_msg'] = 'OK';
} else {
    $rs['return_code'] = 'SUCCESS';
    $rs['return_msg'] = 'FAIL';
}
Log::DEBUG("return:" . json_encode($rs));
echo $app->ToXml2($rs);
Log::DEBUG("end");

// https://pay.weixin.qq.com/wiki/doc/api/native.php?chapter=9_7

/*
支付完成后，微信会把相关支付结果和用户信息发送给商户，商户需要接收处理，并返回应答。

对后台通知交互时，如果微信收到商户的应答不是成功或超时，微信认为通知失败，微信会通过一定的策略定期重新发起通知，尽可能提高通知的成功率，但微信不保证通知最终能成功。 （通知频率为15/15/30/180/1800/1800/1800/1800/3600，单位：秒）

注意：同样的通知可能会多次发送给商户系统。商户系统必须能够正确处理重复的通知。
推荐的做法是，当收到通知进行处理时，首先检查对应业务数据的状态，判断该通知是否已经处理过，如果没有处理过再进行处理，如果处理过直接返回结果成功。在对业务数据进行状态检查和处理之前，要采用数据锁进行并发控制，以避免函数重入造成的数据混乱。

特别提醒：商户系统对于支付结果通知的内容一定要做签名验证,并校验返回的订单金额是否与商户侧的订单金额一致，防止数据泄漏导致出现“假通知”，造成资金损失。
技术人员可登进微信商户后台扫描加入接口报警群。
*/


