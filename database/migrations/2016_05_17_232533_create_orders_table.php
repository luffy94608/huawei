<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->increments('id');
            $table->string('order_no',60)->unique();
            $table->integer('user_id',false,true);
            $table->integer('supplier_id',false,true);
            $table->integer('operator_id',false,true);//运营者id 
            $table->integer('type_id',false,true);
            $table->integer('area_id',false,true);
            $table->string('job_id',20);
            $table->string('mobile',20);
            $table->string('desc',200);
            $table->tinyInteger('score',false,true)->default(0);//评分
            $table->string('remark',200)->default('');//评价
            $table->tinyInteger('remind_num',false,true)->default(0);//催单次数
            $table->timestamp('remind_time');//催单时间
            $table->tinyInteger('status',false,true)->default(0);//订单状态 0 等待分单 1已经分单 待处理 2已完成 未评价 3已评价  4 关闭
            $table->timestamps();

            $table->foreign('type_id')->references('id')->on('types')->onDelete('cascade');;
            $table->foreign('area_id')->references('id')->on('areas')->onDelete('cascade');;
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');;
        });
 
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('orders', function($table) {
            $table->dropForeign(array('type_id','area_id','user_id'));
        });
        Schema::drop('orders');
    }
}
