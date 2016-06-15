<?php
/**
 * Created by PhpStorm.
 * User: jet
 * Date: 16/4/20
 * Time: 上午10:59
 */

namespace App\Helper;


use App\Models\Enums\ContainerEnum;

class WeiboUtil
{
    public static function getWeiboContainerId($page)
    {
        $appId = \Config::get('weibo.app_id');
        return $appId.ContainerEnum::ContainerSep.$page;
    }

    public static function getWeiboContainerScheme($page)
    {
        $appId = \Config::get('weibo.app_id');
        return 'sinaweibo://cardlist?containerid='.$appId.ContainerEnum::ContainerSep.$page;
    }
}