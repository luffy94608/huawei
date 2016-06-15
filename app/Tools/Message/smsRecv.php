<?php
/**
 * Created by PhpStorm.
 * User: rinal
 * Date: 3/17/16
 * Time: 4:50 PM
 */


$config = include __DIR__ . '/../../../config/rabbitmq.php';
$config = $config['production'];
$exchangeName = $config['sms']['exchange_name'];
$queueName = $config['sms']['queue_name'];
$routeKey = $config['sms']['route_key'];

$connection = new AMQPConnection(['host' => $config['base']['host']
, 'port' => $config['base']['port'], 'vhost' => $config['base']['vhost']
, 'login' => $config['base']['login'], 'password' => $config['base']['password']]);
$connection->connect() or die("Cannot connect to the broker!\n");
$channel = new AMQPChannel($connection);
$exchange = new AMQPExchange($channel);
$exchange->setName($exchangeName);
$exchange->setType(AMQP_EX_TYPE_DIRECT);
$queue = new AMQPQueue($channel);
$queue->setName($queueName);
$queue->setFlags(AMQP_DURABLE);
$queue->bind($exchangeName);
$connection_db = new MongoClient($server = "mongodb://127.0.0.1:10001"); // 连接到 localhost:10001
var_dump('[*] Waiting for messages. To exit press CTRL+C');
while (TRUE) {
    try {
        $queue->consume('callback');
        $channel->qos(0, 1);
    } catch (Exception $e) {
        $connection = new AMQPConnection(['host' => $config['base']['host']
        , 'port' => $config['base']['port'], 'vhost' => $config['base']['vhost']
        , 'login' => $config['base']['login'], 'password' => $config['base']['password']]);
        $connection->connect() or die("Cannot connect to the broker!\n");
        $channel = new AMQPChannel($connection);
        $exchange = new AMQPExchange($channel);
        $exchange->setName($exchangeName);
        $exchange->setType(AMQP_EX_TYPE_DIRECT);
        $queue = new AMQPQueue($channel);
        $queue->setName($queueName);
        $queue->setFlags(AMQP_DURABLE);
        $queue->bind($exchangeName);
    }

}

$connection_db->close();
$connection->disconnect();


/**
 * @param $envelope
 * @param $queue
 */
function callback($envelope, $queue)
{
    try
    {
        $msg = json_decode($envelope->getBody());
        var_dump($envelope->getBody());
        $mobiles = $msg->mobiles;
        if(!$mobiles)
        {
            var_dump('error: '. $envelope->getBody());
            $queue->ack($envelope->getDeliveryTag());
            return;
        }
        $mess = '[时时出行]' .$msg->mess;
        try {
            global $connection_db;
            if (!$connection_db->connected) {
                var_dump('reconnected-------------------------------------------');
                $connection_db = new MongoClient($server = "mongodb://127.0.0.1:10001");
            }
            $db = $connection_db->hgt;

        } catch (Exception $e) {
            var_dump($e->getMessage());
            return;
        }
        $options = [
            CURLOPT_RETURNTRANSFER=> true,
            CURLOPT_HEADER=>['content-type'=> 'multipart/form-data'],
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS =>
                [
                    'un' => 'bjcqz-1',
                    'pwd' => '0c0268',
                    'msg'=> mb_convert_encoding($mess, 'GB2312', 'auto')
                ]
        ];
        $mess_list = [];

        foreach ($mobiles as $mobile) {
            $ch = curl_init('http://si.800617.com:4400/SendLenSms.aspx');
            $options[CURLOPT_POSTFIELDS]['mobile'] = $mobile;
            curl_setopt_array($ch, $options);
            $result = curl_exec($ch);
            $headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
            $result = substr($result,$headerSize);
            $doc = new DOMDocument();
            $doc->loadXML($result);
            $result_status = $doc->getElementsByTagName('Result')->item(0)->nodeValue;
            $send_num = $doc->getElementsByTagName('SendNum')->item(0)->nodeValue;
            curl_close($ch);
            array_push($mess_list, [
                'mobile'=>$mobile,
                'message'=>$mess,
                'result_status'=>$result_status,
                'send_num'=>$send_num,
                'result'=>$result,
                'created_at'=>time()
            ]);
        }
        $db->sms_history->batchInsert($mess_list);
    }
    catch(\PhpSpec\Exception\Exception $e)
    {
        var_dump($e);
    }

    $queue->ack($envelope->getDeliveryTag());
}

//
//'''
//                1  = 发送成功
//                -1 = 用户名和密码参数为空或者参数含有非法字符
//                -2 = 手机号参数不正确
//                -3 = msg参数为空或长度小于0个字符
//                -4 = msg参数长度超过350个字符
//                -6 = 发送号码为黑名单用户
//                -8 = 下发内容中含有屏蔽词
//                -9 = 下发账户不存在
//                -10 = 下发账户已经停用
//                -11 = 下发账户无余额
//                -15 = MD5校验错误
//                -16 = IP服务器鉴权错误
//                -17 = 接口类型错误
//                -18 = 服务类型错误
//                -22 = 手机号达到当天发送限制
//                -23 = 同一手机号，相同内容达到当天发送限制
//                -24 =  模板不存在
//                -25 = 模板变量超长
//                -26 = 下发限制，该号码没有上行记录
//                -27 = 手机号不是白名单用户
//                -99 = 系统异常
//                长短信提交时，字符小于等于64时按1条计费；大于64时按照60字/条计费
////            '''
