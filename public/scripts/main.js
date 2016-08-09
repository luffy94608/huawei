(function () {
    //全局可以host

    var configData = document.global_config_data;
    var version = configData.version;
    requirejs.config({
        baseUrl: configData.resource_root + '/release/',
        urlArgs: 'v=' + version,
        waitSeconds: 0,
        paths: {
            //core js
            'jquery': '/bower_components/jquery/dist/jquery.min',
            'zepto': '/bower_components/zepto/zepto.min',
            'widget': 'widget/widget',
            'string': 'widget/string',
            'base': 'page/base',
            'page-reset':'page/page.reset',
            'page-login':'page/page.login',
            'page-email':'page/page.email',
            'page-register':'page/page.register',
            'page-create':'page/page.create',
            'page-list':'page/page.list',
            'page-detail':'page/page.detail',
        },
        // Use shim for plugins that does not support ADM
        shim: {
            'string': ['jquery'],
            'widget': ['jquery','string'],
            'base': ['widget'],
            'page-reset': ['base'],
            'page-email': ['base'],
            'page-login': ['base'],
            'page-register': ['base'],
            'page-create': ['base'],
            'page-list': ['base'],
            'page-detail': ['base'],
        }

    });

    var page = configData.page;

    var modules = [];
    if (page) {
        modules.push(page);
    }

    if (modules.length) {
        require(modules, function () {
        });
    }

})();
