<?php
/**
 * Created by PhpStorm.
 * User: rinal
 * Date: 3/16/16
 * Time: 5:46 PM
 */

namespace App\Tools\Pay;

use App\Models\OrderDetailTypeEnum;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Omnipay\Omnipay;
use App\Models\ApiResult;
use App\Http\Controllers\Api;
use App\Models\OrderDetail;

class WechatCtl
{
    public function getWechatSign($outTradeNo, $totalFee, $body){
        $config = Config::get('wechat')[env('APP_ENV')];
        $gateway    = Omnipay::create('WechatPay_App');
        $gateway->setAppId($config['app_id']);
        $gateway->setMchId($config['mch_id']);
        $gateway->setApiKey($config['api_key']);
        $gateway->setNotifyUrl($config['notify_url']);

        $order = [
            'body'              => $body,
            'out_trade_no'      => $outTradeNo,
            'total_fee'         => $totalFee, //=0.01
            'spbill_create_ip'  => '101.200.228.122',
            'fee_type'          => 'CNY',
            'time_start'        => Carbon::now()->addSecond(-10)->format('YmdHis'),
            'time_expire'        => Carbon::now()->addSecond(291)->format('YmdHis')
        ];

        $request  = $gateway->purchase($order);
        $response = $request->send();


        OrderDetail::create([
            'id' => uniqid(),
            'type' => OrderDetailTypeEnum::WechatSend,
            'order_id' => $outTradeNo,
            'trade_no' => '',
            'request_body' => json_encode($request->getData()),
            'response_body' => json_encode($response->getAppOrderData())
        ]);

        if($response->isSuccessful()){
            return $response->getAppOrderData(); //For WechatPay_App
        }
        else{
            return 'error';
        }
    }

    public function wechatQuery($outTradeNo)
    {
        $config = Config::get('wechat')['APP_ENV'];
        $gateway = Omnipay::create('WechatPay');
        $gateway->setAppId($config['app_id']);
        $gateway->setMchId($config['mch_id']);
        $gateway->setApiKey($config['api_key']);
        $response = $gateway->query([
            'out_trade_no' => $outTradeNo, //The wechat trade no
        ])->send();
        if($response->isSuccessful())
        {
            return $outTradeNo;
        }
        return false;
    }

    # 微信回调
    public function wechatNotification($result){
        $config = Config::get('wechat')['APP_ENV'];
        $gateway    = Omnipay::create('WechatPay');
        $gateway->setAppId($config['app_id']);
        $gateway->setMchId($config['mch_id']);
        $gateway->setApiKey($config['api_key']);

        $response = $gateway->completePurchase([
            'request_params' => file_get_contents('php://input')
        ])->send();

        if ($response->isPaid()) {
            //pay success
            return $response->getData()['out_trade_no'];;
        }else{
            return $response->getData()['out_trade_no'];;
        }
    }

    public function wechatRefund($order, $tradeNo, $refundFee = 0){
        $config = Config::get('wechat')['APP_ENV'];
        $gateway = Omnipay::create('WechatPay');
        $gateway->setAppId($config['app_id']);
        $gateway->setMchId($config['mch_id']);
        $gateway->setApiKey($config['api_key']);
        $gateway->setCertPath($config['cert_path']);
        $gateway->setKeyPath($config['key_path']);

        if($refundFee == 0)
        {
            $refundFee = $order->amount;
        }

        $requestBody = [
            'transaction_id' => $tradeNo, //The wechat trade no
            'out_refund_no' => uniqid(),
            'total_fee' => $order->amount * 100, //=0.01
            'refund_fee' => $refundFee * 100, //=0.01
            'op_user_id' => $gateway->getMchId()
        ];

        $request = $gateway->refund();
        $request->setTransactionId($requestBody['transaction_id']);
        $request->setOutRefundNo($requestBody['out_refund_no']);
        $request->setTotalFee($requestBody['total_fee']);
        $request->setRefundFee($requestBody['refund_fee']);
        $request->setOpUserId($requestBody['op_user_id']);
        $request->setOutTradeNo($order->id);

        $response = $request->sendData($request->getData());

        OrderDetail::create([
            'id' => uniqid(),
            'type' => OrderDetailTypeEnum::WechatRefund,
            'order_id' => $order->id,
            'trade_no' => $tradeNo,
            'request_body' => json_encode($requestBody),
            'response_body' => json_encode($response->getData())
        ]);

        return $response->isSuccessful();


    }
}