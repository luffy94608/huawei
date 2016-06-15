<?php
/**
 * Created by PhpStorm.
 * User: Kevin
 * Date: 2/1/16
 * Time: 3:27 PM
 */

return array(
    'iOS'       => array(
        'environment'   => 'production',   //development or product，相应的使用的pem文件不同
        'certificate'   => '../certs/hgt-dev.pem',
        'accessId'      => '2200186537',
        'secretKey'     => 'e20d57494949bbcbf9efcae4064eb645'
    ),
    'Android'   => array(
        'environment'   => 'production',
        'accessId'      => '2100186536',
        'secretKey'     => 'ce693516aca34c8417598ab32d5d2f7e'
    )
);