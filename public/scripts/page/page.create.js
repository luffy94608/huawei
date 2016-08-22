/**
 * Created by luffy on 16/1/28.
 *  当页面ready的时候，执行回调:
 */
(function($){
    var init = {
        section : '#js_register_section',
        submitBtn : $('#js_submit'),
        loading :false,

        getResourceIds : function () {
            var objs = $('.js_file_list>a');
            var ids = [];
            objs.each(function () {
              var id = $(this).data('id');
                ids.push(id);
            });
            return ids;
        },

        initParams : function () {
            var params = {
                job_id:$.trim($("input[name=job_id]").val()),
                mobile:$.trim($("input[name=mobile]").val()),
                area_id:$.trim($("select[name=area_id]").val()),
                type_id:$.trim($("select[name=type_id]").val()),
                desc:$.trim($("textarea[name=desc]").val()),
                resources:init.getResourceIds(),
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

            $('.js_upload_btn').uploadImage('/order/upload-file?XDEBUG_SESSION_START=12',{},function (data) {
                var html ='<a href="'+data.url+'" data-id="'+data.id+'" class="weui_media_box weui_media_appmsg" style="position: relative"> ' +
                '               <div class="weui_media_hd"> ' +
                    '               <img class="weui_media_appmsg_thumb" src="/images/icon-file@3x.png" alt=""> ' +
                    '           </div> ' +
                    '           <div class="weui_media_bd"> ' +
                    '               <h4 class="weui_media_title">'+data.name+'</h4> ' +
                    '               <p class="weui_media_desc">'+data.size+'</p> ' +
                    '           </div> ' +
                    '           <div href="javascript:;" class="weui_progress_opr js_cancel" style="position: absolute;top: 35px;;right: 0;"> ' +
                    '               <i class="weui_icon_cancel"></i> ' +
                    '           </div> ' +
                    '       </a>';
                $('.js_file_list').append(html);
            },function (radio) {
                radio = parseInt(radio);
                console.log(radio);
                var obj = $('.weui_uploader_status');
                var content = $('.weui_uploader_status_content',obj)
                if(radio>0 && radio<100){
                    obj.removeClass('gone');
                    content.html(radio)
                }else{
                    setTimeout(function () {
                        obj.addClass('gone');
                    },300);
                }

            });

            $(document).on('click','.js_cancel',function () {
                $(this).parents('a').remove();
            })
        },
        run : function () {
            init.initBtnEvent();
        }
    };
    init.run();
})($);