/**
 * Created by luffy on 16/1/28.
 *  当页面ready的时候，执行回调:
 */
Zepto(function($){
    var init = {
        section : '#js_register_section',
        submitBtn : $('#js_submit'),
        loading :false,

        initParams : function () {
            var params = {
                job_id:$.trim($("input[name=job_id]").val()),
                mobile:$.trim($("input[name=mobile]").val()),
                area_id:$.trim($("select[name=area_id]").val()),
                type_id:$.trim($("select[name=type_id]").val()),
                desc:$.trim($("textarea[name=desc]").val()),
            };

            var status = $.checkInputVal({val:params.job_id,type:'job_id',onChecked:function(val,state,hint){
                if(state <= 0){
                    $.showToast(hint,false);
                }
            }
            });
            if(status<=0){
                return false;
            }
            var status = $.checkInputVal({val:params.mobile,type:'mobile',onChecked:function(val,state,hint){
                    if(state <= 0){
                        $.showToast(hint,false);
                    }
                }
            });
            if(status<=0){
                return false;
            }

            if(!params.area_id){
                $.showToast('请选择办公区域',false);
                return false;
            }
            if(!params.type_id){
                $.showToast('请选择需求服务',false);
                return false;
            }

            var status = $.checkInputVal({val:params.desc,type:'desc',onChecked:function(val,state,hint){
                if(state <= 0){
                    $.showToast(hint,false);
                }
            }
            });
            if(status<=0){
                return false;
            }

            return params;
        },
        initResult : function (data) {
            $.locationUrl(data.url);
        },
        initBtnEvent : function () {
            init.submitBtn.unbind().bind('click',function () {
                if(init.loading){
                    return false;
                }

                var params = init.initParams();
                if(!params){
                    return false;
                }
                init.loading = true;
                $.wpost('/order/create',params,function (data) {
                    init.initResult(data);
                    init.loading = false;
                },function () {
                    init.loading = false;
                })
            });
        },
        run : function () {
            init.initBtnEvent();
        }
    };
    init.run();
});