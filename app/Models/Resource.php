<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Resource extends Model
{
    protected $fillable = ['name','url','size','order_id'];
    /**
     * 获取关联
     */
    public function order()
    {
        return $this->belongsTo('App\Models\Order','order_id');
    }

}
