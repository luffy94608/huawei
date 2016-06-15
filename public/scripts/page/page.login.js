/**
 * Created by luffy on 16/1/28.
 */

(function($){
    var init = {
        submitBtn : $('#js_submit'),
        loading :false,

        initParams : function () {
            var params = {
                email:$.trim($("input[name=email]").val()),
                password:$.trim($("input[name=password]").val()),
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
                $.wpost('/auth/login',params,function (data) {
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
})(Zepto);