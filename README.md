## INSTALL

```
composer require fifths/php-wxpay
```

```php
$config = array(
    'appid' => '', // 公众账号ID
    'mch_id' => '', // 商户号
    'key' => '', // key设置路径：微信商户平台(pay.weixin.qq.com)-->账户设置-->API安全-->密钥设置
    'app_secret' => '',// 公众帐号secert（仅JSAPI支付的时候需要配置， 登录公众平台，进入开发者中心可设置），
    //=======【证书路径设置】=====================================
    /**
    * TODO：设置商户证书路径
    * 证书路径,注意应该填写绝对路径（仅退款、撤销订单时需要，可登录商户平台下载，
    * API证书下载地址：https://pay.weixin.qq.com/index.php/account/api_cert，下载之前需要安装商户操作证书）
    * @var path
    */
    'sslcer_path' => '',
    'sslkey_path' => '',
    'notify_url' => '', //异步通知url
);

$app = \Wechat\Factory::WxPay($config);
$data['transaction_id'] = 'xxxxxxxxxxxxx';
$result=$app->orderQuery($data);
echo json_encode($result);
```
