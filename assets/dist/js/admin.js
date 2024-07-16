(function (global, factory) {
    typeof exports === 'object' && typeof module !== 'undefined' ? module.exports = factory() :
    typeof define === 'function' && define.amd ? define(factory) :
    (global = typeof globalThis !== 'undefined' ? globalThis : global || self, global.WpnbAdm = factory());
})(this, (function () { 'use strict';

    var WpnbAdm = function WpnbAdm () {};

    WpnbAdm.slugify = function slugify (str, exc) {
            if ( exc === void 0 ) exc = '';

        return String(str)
            .normalize('NFKD')
            .replace(/[\u0300-\u036f]/g, '')
            .trim()
            .toLowerCase()
            .replace(new RegExp(("[^a-z0-9" + exc + " -]"), 'g'), '')
            .replace(/\s+/g, '-')
            .replace(/-+/g, '-');
    };
    WpnbAdm.randomStr = function randomStr (length) {
        var result = '';
        var characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
        var charactersLength = characters.length;
        var counter = 0;
        while (counter < length) {
            result += characters.charAt(Math.floor(Math.random() * charactersLength));
            counter += 1;
        }
        return result;
    };

    return WpnbAdm;

}));
//# sourceMappingURL=admin.js.map
