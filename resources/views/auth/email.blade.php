@extends('layouts.default')
@section('title', '找回密码')
@section('content')
    <div class="hd">
        <h1 class="page-title">找回密码</h1>
        {{--<h1 class="page-desc">创建服务订单</h1>--}}
        <div class="bd">
            <div class="weui_cells weui_cells_form">
                <div class="weui_cell ">
                    <div class="weui_cell_hd"><label for="" class="weui_label">电子邮箱</label></div>
                    <div class="weui_cell_bd weui_cell_primary">
                        <input class="weui_input" type="email" name="email"  value="{{ old('email') }}" placeholder="请输入电子邮箱">
                    </div>
                    <div class="weui_cell_ft">
                        <i class="weui_icon_warn"></i>
                    </div>
                </div>

            </div>

            <br>

            <div class="weui_btn_area">
                <button class="weui_btn weui_btn_primary" id="js_submit">发送密码重置链接</button>
            </div>
        </div>
    </div>


@stop
