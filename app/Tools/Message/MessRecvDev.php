<?php
/**
 * Created by PhpStorm.
 * User: rinal
 * Date: 3/18/16
 * Time: 3:39 PM
 */


include 'XingeAPI.php';

$config = include __DIR__ . '/../../../config/rabbitmq.php';
$configXG = include __DIR__ . '/../../../config/xinge.php';
$config = $config['local'];
$exchangeName = $config['message']['exchange_name'];
$queueName = $config['message']['queue_name'];
$routeKey = $config['message']['route_key'];

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
$Android_AccessID = $configXG['Android']['accessId'];
$Android_SecretKey = $configXG['Android']['secretKey'];
$IOS_AccessID = $configXG['iOS']['accessId'];
$IOS_SecretKey = $configXG['iOS']['secretKey'];
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


function callback($envelope, $queue)
{
    try{
        $msg = json_decode($envelope->getBody());
        var_dump($envelope->getBody());
        $uids = $msg->user_ids;
        if(!$uids)
        {
            var_dump('error: '. $envelope->getBody());
            $queue->ack($envelope->getDeliveryTag());
            return;
        }
        $type = $msg->mess->type;
        $mess = $msg->mess->content;
        $extraInfo = $msg->mess->extra_info;
        try {
            global $connection_db;
            global $IOS_AccessID;
            global $Android_AccessID;
            global $IOS_SecretKey;
            global $Android_SecretKey;
            if (!$connection_db->connected) {
                var_dump('reconnected-------------------------------------------');
                $connection_db = new MongoClient($server = "mongodb://127.0.0.1:10001"); // 连接到 localhost:10001
            }
            $db = $connection_db->hgt_dev;

        } catch (Exception $e) {
            var_dump($e->getMessage());
            return;
        }
        $mess_list = [];
        foreach ($uids as $id) {

            $notification = [
                "type" => $type,
                "content" => $mess,
                "user_id" => $id,
                'updated_at' => new MongoDate(),
                'created_at' => new MongoDate()
            ];
            if ($extraInfo)
            {
                $notification['extra_info'] = $extraInfo;
            }
            array_push($mess_list, $notification);
            $token = $db->push_tokens->findOne(['uid' => $id]);
            if (!$token) {
                var_dump( '没有找到push_token' . $id);
                continue;
            }
            if($type != 11) //购票不发送通知
            {
                if ($token['platform'] == 'Android') {
                    $re = XingeApp::PushTokenAndroid($Android_AccessID, $Android_SecretKey, '时时出行', $mess, $token['token']);

                } else {
                    $re = XingeApp::PushTokenIos($IOS_AccessID, $IOS_SecretKey, $mess, $token['token'], XingeApp::IOSENV_DEV);
                }
                $notification['result'] = $re;
            }

        }
        $db->notifications->batchInsert($mess_list);
    }
    catch(\PhpSpec\Exception\Exception $e)
    {
        var_dump($e);
    }

    $queue->ack($envelope->getDeliveryTag());
}
