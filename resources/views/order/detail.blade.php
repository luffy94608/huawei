@extends('layouts.default')
@section('title', '订单详情')
@section('content')
    <div class="hd js_order_id" data-id="{{ $order->id }}">
        <h1 class="page-title">订单详情</h1>
        <h1 class="page-desc"> </h1>
        <div class="bd">

            {{--订单详情--}}
            <div class="weui_cells_title">订单编号：{{ $order->order_no }}</div>
            <div class="weui_cells_title">办公区域：{{ $order->area['name'] }}</div>
            <div class="weui_cells_title">需求服务：{{ $order->type['name'] }}</div>
            <div class="weui_cells_title">提交日期：{{ $order->created_at }}</div>
            <div class="weui_cells_title">需求描述：</div>
            <div class="weui_cells weui_cells_form">
                <div class="weui_cell">
                    <div class="weui_cell_bd weui_cell_primary">
                        <textarea class="weui_textarea" placeholder="请输入评价内容" rows="3" disabled>{{ $order->desc }}</textarea>
                        {{--<div class="weui_textarea_counter"><span>0</span>/200</div>--}}
                    </div>
                </div>
            </div>

            @if($order->resources)
                <div class="weui_cells weui_cells_form">
                    <div class="weui_cell">
                        <div class="weui_cell_bd weui_cell_primary">
                            <div class="weui_uploader">
                                <div class="weui_uploader_hd weui_cell">
                                    <div class="weui_cell_bd weui_cell_primary">附件</div>
                                    {{--<div class="weui_cell_ft">0/2</div>--}}
                                </div>
                                <div class="weui_panel_bd ">
                                    @foreach($order->resources as $resource)
                                        <a href="{{ $resource->url }}" class="weui_media_box weui_media_appmsg" style="position: relative">
                                            <div class="weui_media_hd">
                                                <img class="weui_media_appmsg_thumb" src="/images/icon-file@3x.png" alt="">
                                            </div>
                                            <div class="weui_media_bd">
                                                <h4 class="weui_media_title">{{ $resource->name }}</h4>
                                                <p class="weui_media_desc">{{ $resource->size }}</p>
                                            </div>
                                            {{--<div href="javascript:;" class="weui_progress_opr js_cancel" style="position: absolute;top: 35px;;right: 0;">--}}
                                                {{--<i class="weui_icon_cancel"></i>--}}
                                            {{--</div>--}}
                                        </a>
                                    @endforeach
                                </div>

                            </div>
                        </div>
                    </div>
                </div>

            @endif


            @if ($supplier)
                <br>
                <br>
                <h3 class="page-title yellow" style="font-size: 24px;">服务商信息</h3>
                <div class="bd">
                    <div class="weui_cells_title">服务商：{{ $supplier->name }}</div>
                    <div class="weui_cells_title">电话：{{ $supplier->mobile }}</div>
                </div>
                <br>
                <br>
            @endif

            @if ($feedback)
                <div class="weui_cells_title">订单进度说明：</div>
                <div class="weui_cells weui_cells_form">
                    <div class="weui_cell">
                        <div class="weui_cell_bd weui_cell_primary">
                            <textarea class="weui_textarea" placeholder="" rows="3" disabled>{{ $feedback->content }}</textarea>
                            {{--<div class="weui_textarea_counter"><span>0</span>/200</div>--}}
                        </div>
                    </div>
                </div>
            @endif



            @if ( $order->status == \App\Models\Enums\OrderEnum::ORDER_STATUS_WAITING_SEND )
                <div class="weui_cells_title text-center color-blue title-1">等待客服分单中</div>
            @elseif ($order->status == \App\Models\Enums\OrderEnum::ORDER_STATUS_ACCEPT)
                <div class="weui_cells_title text-center color-blue title-1">供应商已受理,请耐心等待服务</div>
            @elseif ($order->status == \App\Models\Enums\OrderEnum::ORDER_STATUS_ACCOMPLISH)
                <div class="weui_cells_title text-center color-blue title-1">服务已完成,请您评价本次服务</div>
            @elseif ($order->status == \App\Models\Enums\OrderEnum::ORDER_STATUS_REMARKED)
                <div class="weui_cells_title text-center color-blue title-1">订单已完成</div>
            @elseif ($order->status == \App\Models\Enums\OrderEnum::ORDER_STATUS_CLOSE)
                <div class="weui_cells_title text-center color-blue title-1">订单已关闭</div>
            @endif
        </div>




        <div class="bd">

            @if (in_array($order->status,[\App\Models\Enums\OrderEnum::ORDER_STATUS_ACCOMPLISH,\App\Models\Enums\OrderEnum::ORDER_STATUS_REMARKED]))
                {{--评价--}}
                <div class="weui_cells_title">评价描述：</div>
                <div class="star-group" data-score="{{ $order->score }}">
                    <span class="star"></span>
                    <span class="star"></span>
                    <span class="star"></span>
                    <span class="star"></span>
                    <span class="star"></span>
                    <span class="hint"></span>
                </div>
                <div class="weui_cells weui_cells_form">
                    <div class="weui_cell">
                        <div class="weui_cell_bd weui_cell_primary">
                            <textarea class="weui_textarea" {{ $order->score >0 ? 'disabled' : '' }} name="remark" placeholder="请输入评价内容" rows="4" >{{ $order->remark }}</textarea>
                        </div>
                    </div>
                </div>

                @if ($order->score == 0)
                    <div class="weui_btn_area">
                        <a class="weui_btn weui_btn_primary" href="javascript:void(0);" id="js_submit">提交</a>
                    </div>
                @endif

                {{--打赏--}}
                <div class="weui_cells_title">如果您对本次服务非常满意，您可以扫描下面的二维码对我们的服务人员进行打赏。</div>
                <div class="weui_cells weui_cells_form">
                    <div class="weui_cell text-center">
                        <img class="praise-qr-code" src="{{ $supplier->qr_code_url }}">
                    </div>
                </div>
            @endif
            @if (in_array($order->status,[\App\Models\Enums\OrderEnum::ORDER_STATUS_ACCEPT]))
                {{--催单--}}
                <div class="weui_btn_area">
                    {{--//weui_btn_disabled--}}
                    <a class="weui_btn weui_btn_primary {{ $next_remind_seconds>0 ? 'weui_btn_disabled' : '' }}" href="javascript:void(0);" data-time="{{ $current_timestamp }}" data-next-remind-seconds="{{ $next_remind_seconds }}" id="js_remind">催单</a>
                </div>
            @endif


        </div>
    </div>

@stop
