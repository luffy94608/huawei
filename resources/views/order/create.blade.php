@extends('layouts.default')
@section('title', '我的服务')
@section('content')
    <div class="hd">
        <h1 class="page-title">创建服务订单</h1>
        <h1 class="page-desc">您的满意是我们的动力</h1>
        <div class="bd">
            <div class="weui_cells weui_cells_form">

                <div class="weui_cell ">
                    <div class="weui_cell_hd"><label for="" class="weui_label">员工工号</label></div>
                    <div class="weui_cell_bd weui_cell_primary">
                        <input class="weui_input" type="text" name="job_id"  placeholder="请输入工号">
                    </div>
                    <div class="weui_cell_ft">
                        <i class="weui_icon_warn"></i>
                    </div>
                </div>
                <div class="weui_cell">
                    <div class="weui_cell_hd"><label class="weui_label">联系电话</label></div>
                    <div class="weui_cell_bd weui_cell_primary">
                        <input class="weui_input" type="text" name="mobile"  placeholder="请输入联系电话">
                    </div>
                </div>
                <div class="weui_cell weui_cell_select weui_select_after ">
                    <div class="weui_cell_hd">
                        <label for="" class="weui_label">办公区域</label>
                    </div>
                    <div class="weui_cell_bd weui_cell_primary">
                        <select class="weui_select" name="area_id">
                            {!! $areaOptionsHtml !!}
                        </select>
                    </div>
                </div>
                <div class="weui_cell weui_cell_select weui_select_after">
                    <div class="weui_cell_hd">
                        <label for="" class="weui_label">需求服务</label>
                    </div>
                    <div class="weui_cell_bd weui_cell_primary">
                        <select class="weui_select" name="type_id">
                            {!! $typeOptionsHtml !!}
                        </select>
                    </div>
                </div>

            </div>

            <div class="weui_cells_title">需求描述</div>
            <div class="weui_cells weui_cells_form">
                <div class="weui_cell">
                    <div class="weui_cell_bd weui_cell_primary">
                        <textarea class="weui_textarea" name="desc" placeholder="请输入需求描述(最少10个字)" rows="3"></textarea>
                        {{--<div class="weui_textarea_counter"><span>0</span>/200</div>--}}
                    </div>
                </div>
            </div>
            <div class="weui_cells_tips">客户人员会尽快处理您的服务请求，您可在微信号首页“我的服务”中查看订单状态。</div>
            <div class="weui_btn_area">
                <a class="weui_btn weui_btn_primary"  id="js_submit">创建</a>
            </div>

        </div>
    </div>

@stop
