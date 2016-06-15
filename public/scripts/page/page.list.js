/**
 * Created by luffy on 16/1/28.
 *  当页面ready的时候，执行回调:
 */
Zepto(function($){

    $('.weui_navbar_item').unbind().bind('click',function () {
        var type = $(this).data('type');
        $.locationUrl('/order/list/'+type,true);
    });
});