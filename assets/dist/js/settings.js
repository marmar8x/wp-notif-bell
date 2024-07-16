(function () {
    'use strict';

    var $ = jQuery;
    WpnbAdm;
    var hash = Hash;
    var ref = {
        nav_tab_wap: '.wpnb-tab-wrapper',
        nav_tab: '.wpnb-tab-wrapper .nav-tab',
        tab_error: '.wpnb-tabs .wpnb-tab-error',
        tab_content: '.wpnb-tabs .tab-content',
        nav_tab_active_cls: 'nav-tab-active',
        tab_error_nav: '.wpnb-tab-error-aps'
    };
    var _activateTab = function (tab) {
        $(ref.nav_tab).removeClass(ref.nav_tab_active_cls);
        $(ref.nav_tab + "[data-wpnb-tab-set=\"" + tab + "\"]").addClass(ref.nav_tab_active_cls);
        var target_id = ref.tab_content + "[data-wpnb-tab=\"" + tab + "\"]";
        var target_tab = $(target_id);
        $(ref.tab_error_nav).remove();
        $(ref.tab_content).removeAttr('data-wpnb-tab-active');
        if (0 === target_tab.length) {
            $(ref.nav_tab).removeClass(ref.nav_tab_active_cls);
            $(ref.tab_error).attr('data-wpnb-tab-active', '');
            $(ref.nav_tab_wap).append("\n            <button type=\"button\" class=\"nav-tab wpnb-tab-error-aps\" style=\"color: #d63031;\">\n                Error\n            </buttn>\n        ");
            return;
        }
        target_tab.attr('data-wpnb-tab-active', '');
    };
    var initHashListener = function () {
        if (hash.q.have('tab')) {
            var tab = hash.q.get('tab');
            _activateTab(tab);
        }
        hash.on('change.query', function (ref) {
            var to = ref.to;

            if ('tab' in to) {
                var tab = to.tab;
                _activateTab(tab);
            }
        });
    };
    window.addEventListener('load', function () {
        initHashListener();
        initSettingsTab();
    });
    var initSettingsTab = function () {
        $(ref.nav_tab).click(function () {
            var _a;
            var tab = (_a = $(this).data('wpnb-tab-set')) !== null && _a !== void 0 ? _a : '';
            if ('' !== tab) {
                hash.q.set('tab', tab);
            }
        });
    };

})();
//# sourceMappingURL=settings.js.map
