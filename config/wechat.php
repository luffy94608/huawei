<?php
/**
 * Created by PhpStorm.
 * User: Kevin
 * Date: 2/1/16
 * Time: 3:27 PM
 */

return [
    'local' => [
        "app_id"        => "wx3ea4821bd86b0ea4",
        "mch_id"        => "1240987502",
        "api_key"       => "490146c2fd3711e4934c34363bc67644",
        "notify_url"    => 'http://hgt-dev.hollo.cn/api/v1/pay/server_wechat_notify',
        "attach"        => 'hgt',
        'cert_path'     => '../certs/apiclient_cert.pem',
        'key_path'      => '../certs/apiclient_key.pem'
    ],
    'production' => [
        "app_id"        => "wx3ea4821bd86b0ea4",
        "mch_id"        => "1240987502",
        "api_key"       => "490146c2fd3711e4934c34363bc67644",
        "notify_url"    => 'http://hgt.hollo.cn/api/v1/pay/server_wechat_notify',
        "attach"        => 'hgt',
        'cert_path'     => '../certs/apiclient_cert.pem',
        'key_path'      => '../certs/apiclient_key.pem'
    ]
];