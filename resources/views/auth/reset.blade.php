@extends('layouts.default')
@section('title', '重置密码')
@section('content')
        <input type="hidden" name="token" value="{{ $token }}">
        <div class="hd">
            <h1 class="page-title">重置密码</h1>
            {{--<h1 class="page-desc">创建服务订单</h1>--}}
            <div class="bd">
                <div class="weui_cells weui_cells_form">
                    <div class="weui_cell ">
                        <div class="weui_cell_hd"><label for="" class="weui_label">电子邮箱</label></div>
                        <div class="weui_cell_bd weui_cell_primary">
                            <input class="weui_input" type="email" name="email" value="{{ $email }}" disabled>
                        </div>
                        <div class="weui_cell_ft">
                            <i class="weui_icon_warn"></i>
                        </div>
                    </div>

                    <div class="weui_cell ">
                        <div class="weui_cell_hd"><label for="" class="weui_label">新密码</label></div>
                        <div class="weui_cell_bd weui_cell_primary">
                            <input class="weui_input" type="password" name="password" value="" placeholder="请输入新密码">
                        </div>
                        <div class="weui_cell_ft">
                            <i class="weui_icon_warn"></i>
                        </div>
                    </div>
                    <div class="weui_cell">
                        <div class="weui_cell_hd"><label class="weui_label">确认密码</label></div>
                        <div class="weui_cell_bd weui_cell_primary">
                            <input class="weui_input" type="password" name="password_confirmation" value="" placeholder="请确认新密码">
                        </div>
                    </div>
                </div>

                <br><br>

                <div class="weui_btn_area">
                    <button class="weui_btn weui_btn_primary"  id="js_submit">保存</button>
                </div>
            </div>
        </div>
@stop
