<?php

/**
 * Created by PhpStorm.
 * User: rinal
 * Date: 3/16/16
 * Time: 5:43 PM
 */
namespace App\Tools\Pay;

use App\Models\OrderDetail;
use App\Models\OrderDetailTypeEnum;
use Carbon\Carbon;
use Illuminate\Support\Facades\Config;
use App\Http\Requests;
use Illuminate\Support\Facades\Log;
use Omnipay\Omnipay;
use App\Http\Controllers\Api;
use DOMDocument;

class AlipayCtl
{
# 获取支付宝签名
    public function getAlipaySign($outTradeNo, $totalFee, $subject, $body)
    {
        $itBpay = '290';
        $gateway = Omnipay::create('Alipay_MobileExpress');
        $this->setGateway($gateway);
        $options = [
            'id' => uniqid(),
            'out_trade_no' => $outTradeNo,
            'subject' => $subject,
            'total_fee' => $totalFee,
            'body' => $body,
            'it_b_pay' => $itBpay,
        ];
        $request = $gateway->purchase($options);


        $response = $request->send()->getOrderString();

        OrderDetail::create([
            'id' => uniqid(),
            'type' => OrderDetailTypeEnum::AliPaySend,
            'order_id' => $outTradeNo,
            'trade_no' => '',
            'request_body' => json_encode($request->getData()),
            'response_body' => $response
        ]);

        return $response;
    }


    # 设置账号信息
    private function setGateway($gateway)
    {
        $config = Config::get('alipay')[env('APP_ENV')];

        $gateway->setPartner($config['partner_id']);
        $gateway->setKey($config['key']);
        $gateway->setSellerEmail($config['seller_email']);
        $gateway->setNotifyUrl($config['notify_url']);
        $gateway->setPrivateKey($config['private_key']);
    }

    # 查询订单
    public function alipayQuery($result)
    {
        $gateway = Omnipay::create('Alipay_MobileExpress');
        $this->setGateway($gateway);

        $options['request_params'] = $result;
        $options['sign_type'] = 'MD5';
        $response = $gateway->completePurchase($options)->send();
        $outTradeNo = $result['out_trade_no'];
        if ($response->isSuccessful() && $response->isTradeStatusOk()) { //

            return $outTradeNo;
        } else {
            Log::info($response->getData());
            return $outTradeNo;
        }
    }

    public function alipayRefund($order, $batchNo, $refundFee = 0)
    {
        if($refundFee == 0)
        {
            $refundFee = $order->amount;
        }
        $gateway = Omnipay::create('Alipay_Refund');
        $this->setGateway($gateway);
        $requestBody = [
            'batch_no' => $batchNo,
            'detail_data' => sprintf('%s^%s^refund', $batchNo, $refundFee)
        ];
        
        $response = $gateway->request($requestBody)->send();

        OrderDetail::create([
            'id' => uniqid(),
            'type' => OrderDetailTypeEnum::AliPayRefund,
            'order_id' => $order->id,
            'trade_no' => $batchNo,
            'request_body' => json_encode($response->getRequest()->getData()),
            'response_body' => json_encode($response->getData())
        ]);

        return $response->isSuccessful();
    }

//    public function alipayRefund($order, $batchNo, $refundFee = 0)
//    {
//        $service = 'refund_fastpay_by_platform_nopwd';
//        $url = 'https://mapi.alipay.com/gateway.do';
//
//        $config = Config::get('alipay')[env('APP_ENV')];
//        if($refundFee == 0)
//        {
//            $refundFee = $order->amount;
//        }
//        $requestBody = [
//            '_input_charset' => 'utf-8',
//            'batch_no' => $batchNo,
//            'batch_num' => 1,
//            'detail_data' => sprintf('%s^%s^refund', $batchNo, $refundFee),
//            'notify_url' => $config['notify_url'],
//            'partner' => $config['partner_id'],
//            'refund_date' => '2016-03-25 16:27:34',//Carbon::now()->format('Y-m-d H:i:s'),
//            'seller_email' => $config['seller_email'],
//            'service' => $service,
//        ];
//        var_dump('--------------------------');
//        $data = $this->array2String($requestBody);
//        $sign = $this->signWithMD5($data, $config['key']);
//        $requestBody['sign_type'] = 'MD5';
//        $requestBody['sign'] = $sign;
//
//        return $requestBody;
//        $ch = curl_init($url);
//        curl_setopt($ch, CURLOPT_POST, true);
//        curl_setopt($ch, CURLOPT_POSTFIELDS, $requestBody);
//        curl_setopt($ch, CURLOPT_HEADER, 'Content-Type=application/x-www-form-urlencoded&User-Agent=Mozilla/5.0 (Macintosh; Intel Mac OS X 10_10_4) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/45.0.2454.99 Safari/537.36');
//        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
//
//        $result = curl_exec($ch);
//        curl_close($ch);
//        if (FALSE === $result)
//            throw new \Exception(curl_error($ch), curl_errno($ch));
//
//        OrderDetail::create([
//            'id' => uniqid(),
//            'type' => OrderDetailTypeEnum::AliPayRefund,
//            'order_id' => $order->id,
//            'trade_no' => $batchNo,
//            'request_body' => json_encode($requestBody),
//            'response_body' => (string)$result
//        ]);
//
//        return $result;
//    }
//
//    private function array2String($data)
//    {
//        $str = http_build_query($data);
//        $str = urldecode($str);
//
//        return $str;
//    }
//
//    public static function signWithMD5($data, $privateKey)
//    {
//        $d = $data . $privateKey;
//        var_dump($d);
//        $sign = md5($d);
//        var_dump($sign);
//        return $sign;
//    }
}