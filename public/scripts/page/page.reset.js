/**
 * Created by luffy on 16/1/28.
 */

(function($){
    var init = {
        submitBtn : $('#js_submit'),
        loading :false,

        initParams : function () {
            var params = {
                token:$.trim($("input[name=token]").val()),
                email:$.trim($("input[name=email]").val()),
                password:$.trim($("input[name=password]").val()),
                password_confirmation:$.trim($("input[name=password_confirmation]").val()),
            };

            var status = $.checkInputVal({val:params.email,type:'email',onChecked:function(val,state,hint){
                if(state <= 0){
                    $.showToast(hint,false);
                }
            }
            });
            if(status<=0){
                return false;
            }

            var status = $.checkInputVal({val:params.password,type:'password',onChecked:function(val,state,hint){
                if(state <= 0){
                    $.showToast(hint,false);
                }
            }
            });
            if(status<=0){
                return false;
            }
            if (params.password != params.password_confirmation) {
                $.showToast($.string.CONFIRM_PASSWORD_NOT_EQUAL,false);
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
                $.wpost('/auth/reset',params,function (data) {
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
})($);