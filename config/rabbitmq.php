<?php
/**
 * Created by PhpStorm.
 * User: Kevin
 * Date: 2/1/16
 * Time: 3:27 PM
 */

return [
   'local' => [
       'base' => [
           'host' => '101.200.228.122',
           'port' => 5672,
           'vhost' => "/",
           'login' => 'hgt',
           'password' => 'hgt_rabbitmq',

           'exchange_name' => 'hgt'
       ],

       'message' => [
           'exchange_name' => 'message_dev',
           'queue_name'    => 'message_dev',
           'route_key'     => 'message_dev',
       ],

       'sms' => [
           'exchange_name' => 'sms_dev',
           'queue_name'    => 'sms_dev',
           'route_key'     => 'sms_dev',
       ]
   ],
   'production' => [
       'base' => [
           'host' => '101.200.228.122',
           'port' => 5672,
           'vhost' => "/",
           'login' => 'hgt',
           'password' => 'hgt_rabbitmq',

           'exchange_name' => 'hgt'
       ],

       'message' => [
           'exchange_name' => 'message',
           'queue_name'    => 'message',
           'route_key'     => 'message',
       ],

       'sms' => [
           'exchange_name' => 'sms',
           'queue_name'    => 'sms',
           'route_key'     => 'sms',
       ]
   ]
];