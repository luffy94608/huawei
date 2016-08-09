<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['order_no','user_id','type_id','area_id','job_id','mobile','desc'];

    /**
     * 获取关联区域
     */
    public function area()
    {
        return $this->belongsTo('App\Models\Area');
    }

    /**
     * 获取供应商
     */
    public function supplier()
    {
        return $this->belongsTo('App\Models\Supplier');
    }

    /**
     * 获取供应商
     */
    public function feedback()
    {
        return $this->hasMany('App\Models\Feedback','oid');
    }

    /**
     * 获取供应商
     */
    public function resources()
    {
        return $this->hasMany('App\Models\Resource','order_id');
    }


    /**
     * 获取关联服务
     */
    public function type()
    {
        return $this->belongsTo('App\Models\Type');
    }

    /**
     * 生成订单编号
     * @return mixed
     */
    public static function toCreateOrderNo()
    {
        $now = Carbon::now();
        $timestamp = $now->format('YmdHi');
        $uniqueId = uniqid();
//        $randNo = mt_rand(1000,9999);
        $randNo = '';
        $orderNo = sprintf('%s%s%s',$timestamp, $uniqueId, $randNo);
        return $orderNo;
    }
}
