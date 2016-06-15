<?php
/**
 * Created by PhpStorm.
 * User: luffy
 * Date: 16/4/20
 * Time: 18:46
 */

namespace App\Http\Builders;

use App\Models\Enums\OrderEnum;
use Carbon\Carbon;

class OrderBuilder
{
    /**
     * select options
     * @param $list
     * @return string
     */
    public static function toBuildSelectOptionsHtml($list)
    {
        $html = "";
        if (count($list)) {
            foreach ($list as $v) {
                $id = $v['id'];
                $name = $v['name'];
                $html .= "
                   <option value='{$id}'>{$name}</option>
                ";
            }
        }
        return $html;
    }

    /**
     * 订单列表
     * @param $list
     * @return string
     */
    public static function toBuildListHtml($list)
    {
        $html = "
             <div class='weui_panel'>
                <div class='weui_media_box weui_media_text'>
                    <p class='weui_media_desc text-center height-90 line-height-90'>无订单信息</p>
                </div>
            </div>
        ";
        if (count($list)) {
            $html = "";
            foreach ($list as $v) {
                $id = $v['id'];
                $url = sprintf('/order/detail/%s',$id);
                $typeTitle = $v->type->name;
                $orderNo = $v['order_no'];
                $time = Carbon::createFromFormat('Y-m-d H:i:s',$v['created_at'])->format('Y-m-d H:i');
                $status = OrderEnum::getText($v['status']);

                $html .= "
                   <a href='{$url}' class='text-decoration-none'>
                        <div class='weui_panel'>
                            <div class='weui_media_box weui_media_text'>
                                <h4 class='weui_media_title color-black-dark'>需求内容：{$typeTitle}</h4>
                                <p class='weui_media_desc'>订单号：{$orderNo}</p>
                                <ul class='weui_media_info'>
                                    <li class='weui_media_info_meta display-inline-block'>提交日期：{$time}</li>
                                    <li class='weui_media_info_meta fr color-blue display-inline-block'>{$status}</li>
                                </ul>
                            </div>
                        </div>
                    </a>
           
                ";
            }
        }
        return $html;
    }


}