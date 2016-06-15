<?php

namespace App\Models\Enums;


class OrderEnum
{
    /**
     * 催单间隔时间
     */
    const NextOrderRemindMinutes         = 60;

    /**
     * 0 需求提交(等待分单)  1供应商受理(已经分单)  2服务已完成  未评价 3已评价  4 关闭
     */
    const ORDER_STATUS_WAITING_SEND     = 0;
    const ORDER_STATUS_ACCEPT           = 1;
    const ORDER_STATUS_ACCOMPLISH       = 2;
    const ORDER_STATUS_REMARKED         = 3;
    const ORDER_STATUS_CLOSE            = 4;

    public static $textMap=array(

        self::ORDER_STATUS_WAITING_SEND     => '客服分单中',
        self::ORDER_STATUS_ACCEPT           => '供应商已受理',
        self::ORDER_STATUS_ACCOMPLISH       => '待评价',
        self::ORDER_STATUS_REMARKED         => '已完成',
        self::ORDER_STATUS_CLOSE            => '已关闭',

    );

    public static function getText($type)
    {
        $map=self::$textMap;
        $text=array_key_exists($type,$map)?$map[$type]:'';
        return $text;
    }
}
