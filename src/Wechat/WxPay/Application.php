<?php

namespace Wechat\WxPay;


use Wechat\WxPay\Kernel\ServiceContainer;
use Wechat\WxPay\Kernel\WxPayException;

class Application extends ServiceContainer
{
    public $base_uri = 'https://api.mch.weixin.qq.com/';

    /**
     * 统一下单
     * https://pay.weixin.qq.com/wiki/doc/api/native.php?chapter=9_1
     * @param $valus
     * @param int $timeOut
     * @return array
     * @throws Kernel\WxPayException
     */
    public function unifiedOrder($values, $timeOut = 6)
    {
        $url = $this->base_uri . "pay/unifiedorder";
        $valus_array = $values;
        $valus_array['appid'] = $this->config['appid'];//公众账号ID
        $valus_array['mch_id'] = $this->config['mch_id'];//商户号
        $valus_array['spbill_create_ip'] = $_SERVER['REMOTE_ADDR'];//终端ip
        $valus_array['nonce_str'] = $this->getNonceStr();
        $this->values = $valus_array;
        $this->SetSign();

        $xml = $this->ToXml();
        $response = $this->postXmlCurl($xml, $url, false, $timeOut);
        $result = $this->Init($response);
        return $result;
    }

    /**
     * 查询订单
     * https://pay.weixin.qq.com/wiki/doc/api/native.php?chapter=9_2
     * @param $values transaction_id  out_trade_no　二选一
     * @param int $timeOut
     * @return mixed
     * @throws Kernel\WxPayException
     */
    public function orderQuery($values, $timeOut = 6)
    {
        // 订单查询接口中，out_trade_no、transaction_id至少填一个！
        $url = $this->base_uri . "pay/orderquery";
        $valus_array = $values;
        $valus_array['appid'] = $this->config['appid'];//公众账号ID
        $valus_array['mch_id'] = $this->config['mch_id'];//商户号
        $valus_array['nonce_str'] = $this->getNonceStr();
        $this->values = $valus_array;
        $this->SetSign();
        $xml = $this->ToXml();
        $response = $this->postXmlCurl($xml, $url, false, $timeOut);
        $result = $this->Init($response);
        return $result;
    }

    /**
     * 关闭订单
     * https://pay.weixin.qq.com/wiki/doc/api/native.php?chapter=9_3
     * @param $values out_trade_no
     * @param int $timeOut
     * @return mixed
     * @throws Kernel\WxPayException
     */
    public function closeOrder($values, $timeOut = 6)
    {
        $url = $this->base_uri . "pay/closeorder";
        $valus_array = $values;
        $valus_array['appid'] = $this->config['appid'];//公众账号ID
        $valus_array['mch_id'] = $this->config['mch_id'];//商户号
        $valus_array['nonce_str'] = $this->getNonceStr();
        $this->values = $valus_array;
        $this->SetSign();
        $xml = $this->ToXml();
        $response = $this->postXmlCurl($xml, $url, false, $timeOut);
        $result = $this->Init($response);
        return $result;
    }

    /**
     * 申请退款
     * https://pay.weixin.qq.com/wiki/doc/api/native.php?chapter=9_4
     * @param $values
     * @param int $timeOut
     * @return mixed
     * @throws Kernel\WxPayException
     */
    public function refund($values, $timeOut = 6)
    {
        $url = $this->base_uri . "secapi/pay/refund";
        $valus_array = $values;
        $valus_array['appid'] = $this->config['appid'];//公众账号ID
        $valus_array['mch_id'] = $this->config['mch_id'];//商户号
        $valus_array['nonce_str'] = $this->getNonceStr();
        $this->values = $valus_array;
        $this->SetSign();
        $xml = $this->ToXml();
        $response = $this->postXmlCurl($xml, $url, true, $timeOut);
        $result = $this->Init($response);
        return $result;
    }

    /**
     * 查询退款
     * https://pay.weixin.qq.com/wiki/doc/api/native.php?chapter=9_5
     * @param $values transaction_id out_trade_no out_refund_no refund_id　４选1
     * @param int $timeOut
     * @return mixed
     * @throws Kernel\WxPayException
     */
    public function refundQuery($values, $timeOut = 6)
    {
        $url = $this->base_uri . "pay/refundquery";
        $valus_array = $values;
        $valus_array['appid'] = $this->config['appid'];//公众账号ID
        $valus_array['mch_id'] = $this->config['mch_id'];//商户号
        $valus_array['nonce_str'] = $this->getNonceStr();
        $this->values = $valus_array;
        $this->SetSign();
        $xml = $this->ToXml();
        $response = $this->postXmlCurl($xml, $url, false, $timeOut);
        $result = $this->Init($response);
        return $result;
    }

    /**
     * 下载对账单
     * https://pay.weixin.qq.com/wiki/doc/api/native.php?chapter=9_6
     * @param $values
     * @param int $timeOut
     * @return mixed|string
     * @throws Kernel\WxPayException
     */
    public function downloadBill($values, $timeOut = 6)
    {
        $url = $this->base_uri . "pay/downloadbill";
        $valus_array = $values;
        $valus_array['appid'] = $this->config['appid'];//公众账号ID
        $valus_array['mch_id'] = $this->config['mch_id'];//商户号
        $valus_array['nonce_str'] = $this->getNonceStr();
        $this->values = $valus_array;
        $this->SetSign();
        $xml = $this->ToXml();
        $response = $this->postXmlCurl($xml, $url, false, $timeOut);
        if (substr($response, 0, 5) == "<xml>") {
            return '';
        }
        return $response;
    }


    /**
     * https://pay.weixin.qq.com/wiki/doc/api/micropay.php?chapter=9_10
     * 提交刷卡支付
     * @param $values
     * @param int $timeOut
     * @return mixed
     * @throws Kernel\WxPayException
     */
    public function micropay($values, $timeOut = 10)
    {
        $url = $this->base_uri . "pay/micropay";
        $valus_array = $values;
        $valus_array['spbill_create_ip'] = $_SERVER['REMOTE_ADDR'];//终端ip
        $valus_array['appid'] = $this->config['appid'];//公众账号ID
        $valus_array['mch_id'] = $this->config['mch_id'];//商户号
        $valus_array['nonce_str'] = $this->getNonceStr();
        $this->values = $valus_array;
        $this->SetSign();
        $xml = $this->ToXml();
        $response = $this->postXmlCurl($xml, $url, false, $timeOut);
        $result = $this->Init($response);
        return $result;
    }


    /**
     * 撤销订单
     * https://pay.weixin.qq.com/wiki/doc/api/micropay.php?chapter=9_11&index=3
     * @param $values
     * @param int $timeOut
     * @return mixed
     * @throws Kernel\WxPayException
     */
    public function reverse($values, $timeOut = 6)
    {
        $url = $this->base_uri . "secapi/pay/reverse";
        $valus_array = $values;
        $valus_array['appid'] = $this->config['appid'];//公众账号ID
        $valus_array['mch_id'] = $this->config['mch_id'];//商户号
        $valus_array['nonce_str'] = $this->getNonceStr();
        $this->values = $valus_array;
        $this->SetSign();
        $xml = $this->ToXml();
        $response = $this->postXmlCurl($xml, $url, true, $timeOut);
        $result = $this->Init($response);
        return $result;
    }


    /**
     * 交易保障
     * https://pay.weixin.qq.com/wiki/doc/api/micropay.php?chapter=9_14&index=7
     * @param $values
     * @param int $timeOut
     * @return mixed
     * @throws Kernel\WxPayException
     */
    public function report($values, $timeOut = 1)
    {
        $url = $this->base_uri . "payitil/report";
        $valus_array = $values;
        $valus_array['appid'] = $this->config['appid'];//公众账号ID
        $valus_array['mch_id'] = $this->config['mch_id'];//商户号
        $valus_array['nonce_str'] = $this->getNonceStr();
        $valus_array['user_ip'] = $_SERVER['REMOTE_ADDR'];
        $this->values = $valus_array;
        $this->SetSign();
        $xml = $this->ToXml();
        $response = $this->postXmlCurl($xml, $url, false, $timeOut);
        $result = $this->Init($response);
        return $result;
    }

    /**
     * 转换短链接
     * https://pay.weixin.qq.com/wiki/doc/api/native.php?chapter=9_9
     * @param $values long_url
     * @param int $timeOut
     * @return mixed
     * @throws Kernel\WxPayException
     */
    public function shorturl($values, $timeOut = 6)
    {
        $url = $this->base_uri . "tools/shorturl";
        $valus_array = $values;
        $valus_array['appid'] = $this->config['appid'];//公众账号ID
        $valus_array['mch_id'] = $this->config['mch_id'];//商户号
        $valus_array['nonce_str'] = $this->getNonceStr();
        $this->values = $valus_array;
        $this->SetSign();
        $xml = $this->ToXml();
        $response = $this->postXmlCurl($xml, $url, false, $timeOut);
        $result = $this->Init($response);
        return $result;
    }

    /**
     * 支付结果通用通知
     */
    public function notify($xml = '')
    {
        if ($xml == '') {
            //获取通知的数据
            $xml = file_get_contents('php://input');
        }
        //如果返回成功则验证签名
        try {
            $result = $this->Init($xml);
            return $result;
        } catch (WxPayException $e) {
            $msg = $e->errorMessage();
            return false;
        }
    }

    /**
     *
     * 生成二维码规则,模式一生成支付二维码
     * appid、mchid、spbill_create_ip、nonce_str不需要填入
     * @param WxPayBizPayUrl $inputObj
     * @param int $timeOut
     * @throws WxPayException
     * @return 成功时返回，其他抛异常
     */
    public function bizpayurl($values, $timeOut = 6)
    {
        $valus_array = $values;
        $valus_array['appid'] = $this->config['appid'];//公众账号ID
        $valus_array['mch_id'] = $this->config['mch_id'];//商户号
        $valus_array['nonce_str'] = $this->getNonceStr();//随机字符串
        $valus_array['time_stamp'] = time();//时间戳
        $this->values = $valus_array;
        $this->SetSign();
        return $this->values;
    }


    public function GetPrePayUrl($productId)
    {
        $values = $this->bizpayurl(['product_id' => $productId]);
        $url = "weixin://wxpay/bizpayurl?" . $this->ToUrlParams3($values);
        return $url;
    }

}