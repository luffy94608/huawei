@extends('layouts.default')
@section('title', '订单列表')
@section('content')
    <div class="weui_tab">
        <div class="weui_navbar">
            <div class="weui_navbar_item {{ $type == 0 ? 'weui_bar_item_on' : '' }} " data-type="0">
                进行中
            </div>
            <div class="weui_navbar_item {{ $type == 1 ? 'weui_bar_item_on' : '' }} " data-type="1">
                已完成
            </div>
        </div>

        <div class="weui_tab_bd">
            {!! $listHtml !!}
        </div>

        <a href="/order/create" class="fixed-bottom border-radius-none width-p-100 weui_btn weui_btn_primary">新建服务</a>

    </div>

@stop
