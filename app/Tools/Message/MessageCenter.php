<?php
/**
 * Created by PhpStorm.
 * User: rinal
 * Date: 3/10/16
 * Time: 7:03 PM
 */


namespace App\Tools\Message;

use App\Models\Driver;
use App\Models\MessageEnum;
use App\Models\NotificationTypeEnum;
use App\Models\User;
use Illuminate\Support\Facades\Config;
use Log;
use PhpAmqpLib\Connection\AMQPConnection;
use PhpAmqpLib\Message\AMQPMessage;

class MessageCenter
{

    public static function sendMessageAction(array $ids, $mess, $type)
    {
        if ($type == MessageEnum::SMS) {
            return MessageCenter::sendSMS($ids, $mess);
        } else {
            return MessageCenter::sendNotification($ids, $mess);
        }

    }

    public static function sendNotification(array $ids, $mess)
    {
        $config = Config::get('rabbitmq')[env('APP_ENV')];
        $base = $config['base'];
        set_time_limit(0);
        if ($mess['type'] == NotificationTypeEnum::DriverSys) {
            $object = Driver::whereIn('_id', $ids)->get();
        } else {
            $object = User::whereIn('_id', $ids)->get();
        }


        $conn = new AMQPConnection($base['host'], $base['port'], $base['login'], $base['password'], $base['vhost']);
        if (!$conn) {
            Log::info('rabbitmq 连接错误');
            return false;
        }
        $exchangeName = $config['message']['exchange_name'];
        $queueName = $exchangeName;
        $routeKey = $exchangeName;
        $key = 'user_ids';
        $key_1 = '_id';

        $ch = $conn->channel();

        $ch->queue_bind($queueName, $exchangeName);


        $send_arr = [];
        foreach ($object as $u) {
            array_push($send_arr, $u->id);
            if (count($send_arr) == 10) {
                $msg_body = json_encode([$key => $send_arr, 'mess' => $mess]);

                Log::info($msg_body);
                $msg = new AMQPMessage($msg_body, array('content_type' => 'text/plain', 'delivery_mode' => 2));
                $ch->basic_publish($msg, $exchangeName);
                $send_arr = [];
            }
        }
        if (count($send_arr) > 0) {
            $msg_body = json_encode([$key => $send_arr, 'mess' => $mess]);
            Log::info($msg_body);
            $msg = new AMQPMessage($msg_body, array('content_type' => 'text/plain', 'delivery_mode' => 2));
            $ch->basic_publish($msg, $exchangeName);
        }

        $ch->close();
        $conn->close();

        return true;
    }


    public static function sendSMS($mobiles, $mess)
    {
        $config = Config::get('rabbitmq')[env('APP_ENV')];
        $base = $config['base'];
        set_time_limit(0);

        $conn = new AMQPConnection($base['host'], $base['port'], $base['login'], $base['password'], $base['vhost']);
        if (!$conn) {
            Log::info('rabbitmq 连接错误');
            return false;
        }
        $exchangeName = $config['sms']['exchange_name'];

        $queueName = $exchangeName;
        $key = 'mobiles';
        $routeKey = $exchangeName;
        $ch = $conn->channel();

        $ch->queue_bind($queueName, $exchangeName);


        $send_arr = [];
        foreach ($mobiles as $u) {
            array_push($send_arr, $u);
            if (count($send_arr) == 10) {
                $msg_body = json_encode([$key => $send_arr, 'mess' => $mess]);

                Log::info($msg_body);

                $msg = new AMQPMessage($msg_body, array('content_type' => 'text/plain', 'delivery_mode' => 2));
                $ch->basic_publish($msg, $exchangeName);
                $send_arr = [];
            }
        }
        if (count($send_arr) > 0) {
            $msg_body = json_encode([$key => $send_arr, 'mess' => $mess]);
            Log::info($msg_body);

            $msg = new AMQPMessage($msg_body, array('content_type' => 'text/plain', 'delivery_mode' => 2));
            $ch->basic_publish($msg, $exchangeName);
        }

        $ch->close();
        $conn->close();

        return true;
    }

}