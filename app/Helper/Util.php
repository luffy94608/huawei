<?php
/**
 * Created by PhpStorm.
 * User: Kevin
 * Date: 2/23/16
 * Time: 10:12 AM
 */

namespace App\Helper;

use App\Models\Heart;
use App\Models\HeartTypeEnum;
use App\Models\MessageEnum;
use App\Models\Notification;
use App\Models\NotificationTypeEnum;
use App\Tools\Message\MessageCenter;
use Xinge;

class Util
{
    public static function sendNotification($toUserOrUsers, $type, $content, $jumpUrl=null, $objectId=null)
    {
        $toUsers = $toUserOrUsers;
        if (is_array($toUserOrUsers) || $toUserOrUsers instanceof \Traversable)
        {
            if (count($toUserOrUsers) == 0) return;
        }
        else
        {
            $toUsers = [$toUserOrUsers];
        }

        $msgBody = array(
            'type'      => $type,
            'content'   => $content
        );

        if ($jumpUrl)
        {
            $msgBody['extra_info'] = array(
                'act_url' => $jumpUrl,
            );
        }
        if ($objectId)
        {
            if (!array_key_exists('extra_info', $msgBody))
            {
                $msgBody['extra_info'] = array(
                  'id'  => $objectId
                );
            }
            else
            {
                $msgBody['extra_info']['id'] = $objectId;
            }
        }

        $uids = [];
        foreach($toUsers as $user)
        {
            Util::setHeartbeat($user, HeartTypeEnum::Notification);
            $uids[] = $user->id;
        }
        MessageCenter::sendMessageAction($uids, $msgBody, MessageEnum::Notification);
    }

    /**
     * Update heartbeat value
     *
     * @param $user
     * @param $typeOrTypes string or array of string
     * @param $setOrClear
     */
    private static function updateHeartbeat($user, $typeOrTypes, $setOrClear)
    {
        $heart = $user->heart;
        if (!$heart)
        {
            $heart = new Heart();
            $user->heart()->save($heart);
        }
        if (gettype($typeOrTypes) == 'array')
        {
            foreach((array)$typeOrTypes as $item)
            {
                $heart->$item = $setOrClear;
            }
        }
        elseif (gettype($typeOrTypes) == 'string')
        {
            $heart->$typeOrTypes = $setOrClear;
        }
        $heart->save();
    }

    /**
     * Set heartbeat value
     *
     * @param $user
     * @param $typeOrTypes  string or array of string
     */
    public static function setHeartbeat($user, $typeOrTypes)
    {
        Util::updateHeartbeat($user, $typeOrTypes, 1);
    }

    /**
     * Clear heartbeat value
     *
     * @param $user
     * @param $typeOrTypes string or array of string
     */
    public static function clearHeartbeat($user, $typeOrTypes)
    {
        Util::updateHeartbeat($user, $typeOrTypes, 0);
    }
} 