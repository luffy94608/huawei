/**
 * Created by luffy on 16/1/28.
 *  当页面ready的时候，执行回调:
 */
Zepto(function($){
    var init = {
        remindBtn : $('#js_remind'),
        remarkBtn : $('#js_submit'),
        orderId : $('.js_order_id').data('id'),
        loading :false,
        /**
         * 打分事件
         */
        initRemarkStarEvent : function () {
            var opts = {
                container : '.star-group',
                hintMap : ['很差','差','一般','好','很好'],
            };
            var obj = $(opts.container);
            var hint = obj.find('.hint');

            var score = parseInt(obj.data('score'));
            if (score > 0) {
                for (var i =0 ;i<score;i++){
                    obj.find('.star').eq(i).addClass('active');
                }
                obj.find('.hint').html(opts.hintMap[score-1]);
            }

            obj.find('.star').unbind().bind('click',function () {
                if(score > 0){
                    return false;
                }
                $(this).addClass('active').siblings().removeClass('active');
                var index = $(this).index();
                for (var i =0 ;i<index;i++){
                    obj.find('.star').eq(i).addClass('active');
                }
                obj.find('.hint').html(opts.hintMap[index]);
                obj.data('score',index+1);
            });
        },

        initBtnEvent : function () {
            /**
             * 催单
             */
            init.remindBtn.unbind().bind('click',function () {
                var $this = $(this);
                if(init.loading){
                    return false;
                }
                var secs = $this.data('next-remind-seconds');
                var time = $this.data('time');
                var nextTime = (time+secs)*1000;
                if ((new Date()).getTime() < nextTime ){
                    return false;
                }

                init.loading = true;
                $.wpost('/order/remind',{id:init.orderId},function (data) {
                    $.showToast('催单成功',true);
                    $this.data('next-remind-seconds',data.next_remind_seconds);
                    $this.data('time',data.current_timestamp);
                    $this.addClass('weui_btn_disabled');
                    init.loading = false;
                },function () {
                    init.loading = false;
                })
            });

            /**
             * 评价
             */
            init.remarkBtn.unbind().bind('click',function () {
                if(init.loading){
                    return false;
                }
                var params = {
                    id:init.orderId,
                    score:$.trim($('.star-group').data('score')),
                    remark:$.trim($('textarea[name=remark]').val()),
                };

                if(params.score < 1){
                    $.showToast('请对服务进行打分',false);
                    return false;
                }

                var status = $.checkInputVal({val:params.remark,type:'remark',onChecked:function(val,state,hint){
                    if(state <= 0){
                        $.showToast(hint,false);
                    }
                }
                });
                if(status<=0){
                    return false;
                }


                init.loading = true;
                $.wpost('/order/remark',params,function (data) {
                    $.showToast('评价成功',true);
                    window.location.reload();
                    init.loading = false;
                },function () {
                    init.loading = false;
                })
            });
        },
        run : function () {
            init.initBtnEvent();
            init.initRemarkStarEvent();
        }
    };
    init.run();
    
});