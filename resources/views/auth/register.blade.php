@extends('layouts.default')
@section('title', '注册')
@section('content')
    <div class="hd" id="js_register_section">
        <h1 class="page-title">注册</h1>
        {{--<h1 class="page-desc">创建服务订单</h1>--}}

        <div class="bd">
            <div class="weui_cells_title">帐号信息</div>
            <div class="weui_cells weui_cells_form">
                <div class="weui_cell ">
                    <div class="weui_cell_hd"><label for="" class="weui_label">电子邮箱</label></div>
                    <div class="weui_cell_bd weui_cell_primary">
                        <input class="weui_input" type="text" name="email" value="" placeholder="请输入有效的电子邮箱">
                    </div>
                    <div class="weui_cell_ft">
                        <i class="weui_icon_warn"></i>
                    </div>
                </div>
                <div class="weui_cell ">
                    <div class="weui_cell_hd"><label for="" class="weui_label">登录密码</label></div>
                    <div class="weui_cell_bd weui_cell_primary">
                        <input class="weui_input" type="password" name="password" value="" placeholder="请输入密码">
                    </div>
                    <div class="weui_cell_ft">
                        <i class="weui_icon_warn"></i>
                    </div>
                </div>
                <div class="weui_cell ">
                    <div class="weui_cell_hd"><label for="" class="weui_label">确认密码</label></div>
                    <div class="weui_cell_bd weui_cell_primary">
                        <input class="weui_input" type="password" name="password_confirmation" value="" placeholder="请确认密码">
                    </div>
                    <div class="weui_cell_ft">
                        <i class="weui_icon_warn"></i>
                    </div>
                </div>

            </div>

            <div class="weui_cells_title">个人信息</div>
            <div class="weui_cells weui_cells_form">
                <div class="weui_cell">
                    <div class="weui_cell_hd"><label class="weui_label">真实姓名</label></div>
                    <div class="weui_cell_bd weui_cell_primary">
                        <input class="weui_input" type="text" name="name" placeholder="请输入真实姓名">
                    </div>
                </div>
                <div class="weui_cell">
                    <div class="weui_cell_hd"><label class="weui_label">联系电话</label></div>
                    <div class="weui_cell_bd weui_cell_primary">
                        <input class="weui_input" type="text" name="mobile" placeholder="请输入联系电话">
                    </div>
                </div>
                <div class="weui_cell">
                    <div class="weui_cell_hd"><label class="weui_label">员工工号</label></div>
                    <div class="weui_cell_bd weui_cell_primary">
                        <input class="weui_input" type="text" name="job_id" placeholder="请输入员工工号">
                    </div>
                </div>

            </div>

            <div class="weui_cells_tips">请输入有效的信息以便及时与您联系，为您提供优质服务。</div>
            <div class="weui_btn_area">
                <a class="weui_btn weui_btn_primary" href="javascript:void(0);" id="js_submit">保存</a>
            </div>

        </div>
    </div>

@stop
