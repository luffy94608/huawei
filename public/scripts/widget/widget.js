(function($){

    $.log = function(v){
        console.log(v);
    };

    $.locationUrl = function (url,status) {
        if (status) {
            window.location.replace(url);
        } else {
            window.location.href = url;
        }
    };

	$.fn.wclick = function(callback){ 
		$(this).click(function(e){ 
			if(false === callback.call(this, e))
				e.preventDefault();
		});
	};

    $.toastSuccess = function (str) {
        var toast = $('#js_toast_success');
        if (str == 'hide') {
            toast.hide();
            return;
        }
        toast.show();
    };

    $.loadingToast = function (str) {
       var toast = $('#js_loading_toast');
        if (str == 'hide') {
            toast.hide();
            return;
        }
        toast.show();
    };
    $.showToast = function(str,YesOrNo,callback){
        /**
         * 参数1传字符串
         * 参数2传布尔值 true是对勾样式，false是叉子样式。其他和不传无样式
         * 默认不允许传html
         */
        var st = {
            toastID:'toast-new',
            dur:2000
        }
        if( typeof str !== 'string' ){
            throw 'Invalid param of $.showToast';
            return;
        }

        if( $('#'+st.toastID).length == 0 ){
            var div = '<section id="'+st.toastID+'" class="gone">\
                <div class="toast-box">\
                </div>\
            </section>';
            $('body').append(div);
        }

        //如果允许传html就把这句删了
//        str  = str.getLegalStr();
        var $toast = $('#'+st.toastID);
        var $box = $toast.find('.toast-box');
        var init = function(){
            $box.empty().append('<p>');
        }
        var setPosition = function(){
            var h = $(window).height();
            var top = h/2 - 30;
            $toast.css('top',top+'px');
        }
        var toastFlash = function(time){
            $toast.show(300);
            setTimeout(function(){
                $toast.hide(300);
                if($.isFunction(callback)){
                    setTimeout(callback,300);
                }
            },time)
        }
        var checkSpecialStatus = function(){
            if( typeof YesOrNo == 'boolean' ){
                if( YesOrNo ){
                    $box.find('p').addClass('yea');
                }else{
                    $box.find('p').addClass('nay');
                }
            }
        }

        init();
        checkSpecialStatus();
        $box.find('p').append(str);
        setPosition();
        toastFlash(st.dur);
    };

    $.confirm=function(opts){
        var options={
            'title':'提示',
            'content':'内容',
            'cancelTitle':'取消',
            'confirmTitle':'确定',
            'textCenter':false,
            'hideCancelBtn':false,
            'hideSubmitBtn':false,
            'success':function(){},
            'cancel':function(){}
        };
        $.extend(options,opts);
        var modal=$('#js_dialog_confirm');
        var cancelBtn = modal.find('.js_cancel');
        var submitBtn = modal.find('.js_submit');

        if(options.textCenter){
            modal.removeClass('weui_dialog_confirm').addClass('weui_dialog_alert');
        }else{
            modal.removeClass('weui_dialog_alert').addClass('weui_dialog_confirm')
        }
        if(options.hideCancelBtn){
            cancelBtn.hide();
        }else{
            cancelBtn.show();
        }
        if(options.hideSubmitBtn){
            submitBtn.hide();
        }else{
            submitBtn.show();
        }
        modal.find('.js_title').html(options.title);
        modal.find('.js_content').html(options.content);
        cancelBtn.html(options.cancelTitle);
        submitBtn.html(options.confirmTitle);
        submitBtn.unbind().wclick(function(){
            if($.isFunction(options.success)){
                options.success();
            }
            modal.hide();
        });
        cancelBtn.unbind().wclick(function(){
            if($.isFunction(options.cancel)){
                options.cancel();
            }
            modal.hide();
        });
        modal.show();
    };

    $.wpost = function(url, data, callback,failback,withoutLoading){
        var loadingBody=$('body');
        if(data == null)
            data = {};
        data.request_type = 'ajax';

        if (!withoutLoading){
            $.loadingToast();
        }
        url += '?XDEBUG_SESSION_START=19192';

        $.log(">>>>>>>>>>>>>"+url+">>>>>>>>>>>>>>>");

        $.ajax({type: 'post',url: url,data: data,
            success: function(res){
                $.log('===========');
                $.log(res);
                if (!withoutLoading){
                    $.loadingToast('hide');
                }
                if(res.code == 0){
                    if($.isFunction(callback))
                        callback(res.data);
                }else{
                    if (parseInt(res.code) == -1){
                        $.showToast( res.msg,false);
                        if($.isFunction(failback)) {
                            failback(res);
                        }
                        return;
                    }

                    if($.isFunction(failback))
                        failback(res);
                    else
                        $.showToast( res.msg,false);
                }
            },
            error:function(request, textStatus, err){
                if (textStatus){
                    var extra = textStatus + '<br>status:'+ request.status + ' ' + request.statusText + '<br>';
                    $.showToast('服务器错误',false);
                } else {
                    $.showToast(err.description,false);
                }
                if($.isFunction(failback)) {
                    failback(res);
                }
                if (!withoutLoading){
                    $.loadingToast('hide');
                }
            },
            dataType: 'json'});
    };

    $.fn.uploadImage = function(action,data,func){
        $(this).next('input[type="file"]').remove();
        var input = $(' <input style="display:none;" type="file" name="file"/>');
        var _this = $(this);
        input.change(function(){
            if(/image/i.test(this.files[0].type)){
                var xhr = new XMLHttpRequest();
                _this.data("_sid",'_sid_'+Math.random());
                var p = _this.position();
                var _show = $('div></div>').css({"postion":"absolute",'top' : p.top,'left' : p.left,'opacity' : '0.5',"background-color" : '#CCC','-webkit-border-radius':"5px","padding":"5px",'z-index':_this.zIndex + 1}).attr("id",_this.data('_sid'));
                xhr.upload.onprogress = function(e) {
                    var ratio = e.loaded / e.total;
                    $("#" + _this.data("_sid")).html(ratio*100 + "%");
                };
                xhr.onload = function(){
                    if(this.status  == 200){
                        if(func){
                            func($.parseJSON(this.responseText).data);
                        }
                    }
                    $("#" + _this.data("_sid")).remove();
                };
                xhr.open("POST", action, true);
                var fd = new FormData();
                fd.append('image',this.files[0]);

                if (data){
                    for(var i in data){
                        fd.append(i, data[i]);
                    }
                }

                xhr.send(fd);
            }else{
                alert('请选择图片');
                $(this).click();
            }
        });
        $(this).after(input);
        input.click();
    };

    Date.prototype.Format = function(fmt){ //author: meizz
        var o = {
            "M+" : this.getMonth()+1,                 //月份
            "d+" : this.getDate(),                    //日
            "h+" : this.getHours(),                   //小时
            "m+" : this.getMinutes(),                 //分
            "s+" : this.getSeconds(),                 //秒
            "q+" : Math.floor((this.getMonth()+3)/3), //季度
            "S"  : this.getMilliseconds()             //毫秒
        };
        if(/(y+)/.test(fmt))
            fmt=fmt.replace(RegExp.$1, (this.getFullYear()+"").substr(4 - RegExp.$1.length));
        for(var k in o)
            if(new RegExp("("+ k +")").test(fmt))
                fmt = fmt.replace(RegExp.$1, (RegExp.$1.length==1) ? (o[k]) : (("00"+ o[k]).substr((""+ o[k]).length)));
        return fmt;
    };

    $.fn.Pager = function(opts) {
        var st = {
            url: null,
            currentPage : 0,
            protocol: null,
            listSize: 20,
            onPageInitialized:null,
            onCountChanged:null,
            wrapUpdateData:null
        };

        var $this = $(this);
        $.extend(st, opts);

        $this.currentIdx = function(){
            return st.currentPage;
        };

        $this.initTabPage = function(){
            var count = $this.find("#list_count").html();
            var pagerCount = parseInt((parseInt(count) + st.listSize-1) / st.listSize);
//	        if(count > st.listSize){
            var pi = $this.find("#pager_indicator").PagerIndicator({
                url: st.url?st.url:null,
                currentPage: st.currentPage,
                totalCount:count,
                totalPage: pagerCount,
                pageSize: 5,
                onPageChange: function(index){
                    st.currentPage = index;
//	        			$this.find("#list").html(res.html);
                    $this.updateList(index);
                    pi.invalidate();
                }
            });
//	        };
            if($.isFunction(st.onPageInitialized))
                st.onPageInitialized($this);
        };

        $this.setEmpty = function(html){
            $this.find("#list").empty().html(html);
        };
        $this.updateList = function(pageIndex){
            var data = {offset:pageIndex*st.listSize,length:st.listSize};
            if($.isFunction(st.wrapUpdateData))
                data = st.wrapUpdateData(pageIndex,data);
            $.wpost(st.protocol, data ,function(res){
                $this.find("#list_count").html(res.total);
                $this.find("#list").html(res.html);

                if($.isFunction(st.onCountChanged))
                    st.onCountChanged(res.total);
                $this.initTabPage(pageIndex);
            });
        };

        $this.initTabPage();

        return $this;
    };

    $.fn.PagerIndicator = function(opts) {
        var st = {
            url: null,
            totalCount: 0,
            totalPage : 1,
            currentPage : 0,
            pageSize : 3,
            updateWhenInit: false,
            showLast: true,
            onPageChange : null
        };

        var $this = $(this);
        var spanClass = '';
        var numberClass = '';
        var currentClass = "active";
        var BTN = $("<li class='paginate_button'><a href='javascript:void(0);'></a></li>");
        var SPAN = $("<li class='paginate_button' style='padding:0px 5px 0px 0px;'></li>");
        var firstString = '首页', lastString = '末页';
        var prevString = '上一页', nextString = '下一页';

        if (opts != st)
            $.extend(st, opts);

        st.totalPage = Math.max(1,st.totalPage);
        st.currentPage = Math
            .min(Math.max(st.currentPage, 0), st.totalPage - 1);
        if (st.url){
            if (st.url.indexOf('?') > 0){
                st.url = st.url + '&';
            } else {
                st.url = st.url + '?';
            }
        }

        var even = (st.pageSize & 1) ? 0 : 1;
        var half = (st.pageSize - 1) >> 1;

        var lower = Math.max(0, Math.min(st.currentPage + half,
            st.totalPage - 1)
        - st.pageSize + 1);
        var upper = Math.min(st.totalPage - 1, Math.max(st.currentPage - half,
            0)
        + st.pageSize - 1);

        var first = st.currentPage == 0, last = st.currentPage == st.totalPage - 1;
        var preIdx = Math.max(st.currentPage - 1, 0), nextIdx = Math.min(
            st.currentPage + 1, st.totalPage - 1);

        var ITEMS = [ {
            html : firstString,
            title : firstString,
            disabled : first,
            desIdx : 0,
            style : first ? spanClass: numberClass
        } ];
        ITEMS.push({
            html : prevString,
            title : prevString,
            disabled : first,
            desIdx : preIdx,
            style : first ? spanClass: numberClass
        });

        for ( var i = lower; i <= upper; i++) {
            var focused = i == st.currentPage;
            var s = focused ? currentClass : numberClass;
            ITEMS.push({
                html : i + 1,
                title : i + 1,
                style : s,
                disabled : focused,
                desIdx : i,
                isNum : true
            });
        }

        ITEMS.push({
            html : nextString,
            title : nextString,
            disabled : last,
            desIdx : nextIdx,
            style : last ? spanClass: numberClass
        });
        if(st.showLast)
            ITEMS.push({
                html : lastString,
                title : lastString,
                disabled : last,
                desIdx : st.totalPage - 1,
                style : last ? spanClass: numberClass
            });

        $this.empty();

        if (st.totalCount > 0){
            var pageInfo = $('<li class="paginate_button"><div class="page_info"></div></li>').find('.page_info').html('总数<span class="number">'+st.totalCount+'</span>,共<span class="number">'+st.totalPage+'</span>页');
            if (st.totalPage > 1){
                pageInfo.append('<input type="text" placeholder="GO" class="input_go">');
                pageInfo.find('input').keypress(function(e){
                    if (e.keyCode == 13){
                        var page = parseInt($(this).val(), 10);
                        if (st.url){
                            location.href = st.url + "page=" + page;
                        } else {
                            if (0 < page && page <= st.totalPage){
                                if ($.isFunction(st.onPageChange)) {
                                    st.onPageChange(page-1);
                                }
                            }
                        }
                    }
                }) ;
            }
            pageInfo.parent().appendTo($this);
        }
        if (st.totalPage > 1){
            $.each(ITEMS, function(i, data) {
                if (data.disabled && !data.isNum && false) {
                    SPAN.clone().appendTo($this).html(data.html).attr("title",
                        data.title).addClass(data.style);
                } else {
                    if (st.currentPage == data.desIdx){
                        var tmp=BTN.clone().appendTo($this);
                        tmp.addClass(data.style);
                        tmp.find('a').html(data.html).addClass(data.style);
                    } else {
                        if(st.url==null){
                            BTN.clone().appendTo($this).find('a').html(data.html).attr("title",
                                data.title).addClass(data.style).wclick(function() {
//									if (st.currentPage != data.desIdx) {
                                    st.currentPage = data.desIdx;
                                    if ($.isFunction(st.onPageChange)) {
                                        st.onPageChange(data.desIdx);
//                                        alert(data.desIdx);
                                    }
//									}
                                    return false;
                                });
                        }else{
                            BTN.clone().appendTo($this).find('a').html(data.html).attr("title",
                                data.title).addClass(data.style).attr("href", st.url + "page=" + (data.desIdx+1) ).attr("target", "_top");

                        }
                    }
                }
            });
        }

        $this.invalidate = function(){
            $this.PagerIndicator(st);
        };
        if (st.updateWhenInit && $.isFunction(st.onPageChange)) {
            st.onPageChange(st.currentPage);
            // alert(data.desIdx);
        }

        $this.currentPage = function(){
            return st.currentPage;
        };


        return $this;
    };


    $.getByteLength = function(val){
        if (!val) return 0;
        var len = 0;
        return val.length;
        for (var i=0; i < val.length; i++) {
            if (val.substr(i,1).match(/[^\x00-\xff]/ig) != null)
                len += 2;
            else
                len += 1;
        }

        return len;
    };
    // input verify
    $.hintFormatter = {
        empty:'{0}为必填项',
        tooShort:'{0}至少{1}个字',
        tooLong:'{0}最多{1}个字',
        bad:'{0}格式不对'
    };
    $.defaultVerifyConfig = {
        name:{
            min:2,
            max:16,
            banSpecial:true,
            title:'真实姓名',
            hasSpecial:function(val){
                var regular = /^[\u4E00-\u9FA5a-zA-Z\s]+$/;
                if (regular.test(val)){
                    var regularS = /^（）[！？。，《》{}【】“”·、：；‘’……]+$/;
                    return regularS.test(val);
                }
                return true;
            },
            test:function(val){
                return true;
            }
        },
        password:{
            min:6,
            max:60,
            title:'登录密码',
            test:function(val){
                return true;
            }
        },
        email:{
            min:-1,
            max:-1,
            title:'邮箱',
            test:function(val){
                var regEmail = /^[a-z0-9]+([._-]*[a-z0-9]+)*@([a-z0-9\-_]+([.\_\-][a-z0-9]+))+$/i;
//				var regEmail = /^((([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+(\.([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+)*)|((\x22)((((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(([\x01-\x08\x0b\x0c\x0e-\x1f\x7f]|\x21|[\x23-\x5b]|[\x5d-\x7e]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(\\([\x01-\x09\x0b\x0c\x0d-\x7f]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]))))*(((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(\x22)))@((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)+(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))$/i;
//				var regEmail = /^([\w-_]+(?:\.[\w-_]+)*)@((?:[a-z0-9]+(?:-[a-zA-Z0-9]+)*)+\.[a-z]{2,6})$/i;
                return regEmail.test(val);
            }
        },
        url:{
            min:-1,
            max:-1,
            title:'URL',
            test:function(val){
                var regUrl = /^(https?:\/\/)?(((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:)*@)?(((\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5]))|((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)+(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.?)(:\d*)?)(\/((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)+(\/(([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)*)*)?)?(\?((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)|[\uE000-\uF8FF]|\/|\?)*)?(\#((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)|\/|\?)*)?$/i;
                return regUrl.test(val);
            }
        },
        qq:{
            min:5,
            max:15,
            title:'QQ',
            test:function(val){
                var regQQ = /^[1-9][0-9]{4,}$/i;
                return regQQ.test(val);
            }
        },
        mobile:{
            min:-1,
            max:-1,
            title:'手机号',
            test:function(val){
                var regMobile = /^(\+|00)?[0-9\s\-]{3,20}$/;
                return regMobile.test(val);
            }
        },

        job_id:{
            min:1,
            max:50,
            title:'员工号码',
            test:function(val){
				return true;
            }
        },

        desc:{
            min:10,
            max:200,
            title:'需求描述',
            test:function(val){
				return true;
            }
        },
        remark:{
            min:10,
            max:200,
            title:'评价内容',
            test:function(val){
                return true;
            }
        }
    };

    // state 1:ok 0:empty -1:short -2:bad
    $.checkInputVal = function(opts){
        var st = {
            val:null,
            onChecked:function(value,state,hint){
            },
            type:null,
            showHint:false,
            required:true
        };
        st = $.extend(st,opts);
        st.onChecked = $.isFunction(st.onChecked) ? st.onChecked : function(value,state,hint){};
//		st.type = st.type || $(this).attr('checkType');

        var getErrorHint = function(error,config){
            if (error == 0){
                return $.hintFormatter.empty.format(config.title);
            } else if (error == -1){
                return $.hintFormatter.tooShort.format(config.title,parseInt(config.min ));
            } else if (error == -2){
                return $.hintFormatter.tooLong.format(config.title,parseInt(config.max));
            } else if (error == -3){
                return $.hintFormatter.bad.format(config.title);
            }

            return '';
        };

        var val = st.val;
        var val = $.trim(val);

        var config = $.defaultVerifyConfig[st.type];
        if (!val || val.length == 0){
            st.onChecked(val, 0, getErrorHint(0,config));
            return 0;
        }

        var length = $.getByteLength(val);
        if (config.min > 0 && length < config.min){
            st.onChecked(val, -1, getErrorHint(-1,config));
            return -1;
        }

        if (config.max > 0 && length > config.max){
            st.onChecked(val, -2, getErrorHint(-2,config));
            return -2;
        }

        if (config.banSpecial){
            if ($.isFunction(config.hasSpecial)){
                if (config.hasSpecial(val)){
                    st.onChecked(val, -4, '不能包含特殊字符');
                    return -4;
                }
            } else {
//				var regular= /['.,:;*?~`!@#$%^&+=)(<>{}]|\]|\[|\/|\\\|\"|\|/i;
                var regular = /^[\u4E00-\u9FA5a-zA-Z0-9\s\.\,\(\)\+#\-]+$/;
                if (!regular.test(val)){
                    st.onChecked(val, -4, '不能包含特殊字符');
                    return -4;
                } else {
                    var regularS = /^[（）！？。，《》{}【】“”·、：；‘’……]+$/;
                    if (regularS.test(val)){
                        st.onChecked(val, -4, '不能包含特殊字符');
                        return -4;
                    }
                }
            }
        }

//		var regular = /^([^\`\+\~\!\#\$\%\^\&\*\(\)\|\}\{\=\"\'\！\￥\……\（\）\——]*[\+\~\!\#\$\%\^\&\*\(\)\|\}\{\=\"\'\`\！\?\:\<\>\尠“\”\；\‘\‘\〈\ 〉\￥\……\（\）\——\｛\｝\【\】\\\/\;\：\？\《\》\。\，\、\[\]\,]+.*)$/;
        if (!config.test(val)){
            st.onChecked(val, -3, getErrorHint(-3,config));
            return -3;
        }
        st.onChecked(val, 1, '');
        return 1;
    };

})(Zepto);