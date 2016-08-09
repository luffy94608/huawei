(function ($) {
    String.prototype.format = function () {
        var args = arguments;
        return this.replace(/{(\d+)}/g, function (match, number) {
            return typeof args[number] != 'undefined' ? args[number] : match;
        });
    };

    $.string =  {
        // === info ===
        version: "1.0.0",
            auth: "web",

            // === common ===
            CONFIRM_PASSWORD_NOT_EQUAL : '两次密码不一致',


    };
})($);


