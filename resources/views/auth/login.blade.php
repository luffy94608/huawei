@extends('layouts.default')
@section('title', '登录')
@section('content')
    <div class="hd">
        <h1 class="page-title">登录</h1>
        {{--<h1 class="page-desc">创建服务订单</h1>--}}
        <div class="bd">
            <div class="weui_cells weui_cells_form">
                <div class="weui_cell ">
                    <div class="weui_cell_hd"><label for="" class="weui_label">电子邮箱</label></div>
                    <div class="weui_cell_bd weui_cell_primary">
                        <input class="weui_input" type="text" name="email"  value="" placeholder="请输入电子邮箱">
                    </div>
                    <div class="weui_cell_ft">
                        <i class="weui_icon_warn"></i>
                    </div>
                </div>
                <div class="weui_cell">
                    <div class="weui_cell_hd"><label class="weui_label">登录密码</label></div>
                    <div class="weui_cell_bd weui_cell_primary">
                        <input class="weui_input" type="password" name="password"  value="" placeholder="请输入登录密码">
                    </div>
                </div>
            </div>

            <div class="weui_cells_tips">
                <a href="/auth/register" class="weui_btn weui_btn_mini weui_btn_plain_primary fl  margin-top-15">立即注册</a>
                <a href="/auth/email" class="weui_btn weui_btn_mini weui_btn_plain_primary fr ">忘记密码?</a>
            </div>

            <br><br><br>

            <div class="weui_btn_area">
                <a class="weui_btn weui_btn_primary" href="javascript:void(0)" id="js_submit">登录</a>
            </div>
        </div>
    </div>

@stop
