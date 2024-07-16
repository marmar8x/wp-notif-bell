(function () {
    'use strict';

    var $ = jQuery;
    var adminCore = WpnbAdm;
    var dataObj = wpnb_data_obj;
    var ids = {
        res_name_str: '#wpnb_send_res_form_name_str',
        res_name_slc: '#wpnb_send_res_form_name_slc',
        res_data: '#wpnb_send_res_form_data',
        res_area: '#wpnb_receivers_area',
        res_add_btn: '#wpnb_res_add_btn',
        res_none_alert: '#wpnb_res_alert_none',
        send_btn: '#wpnb_act_send',
        notif_title: '#wpnb_send_title',
        notif_text: '#wpnb_send_text',
        notif_sender: '#wpnb_send_sender',
        notif_text_type: '#wpnb_send_text_type',
        notif_date: '#wpnb_send_date',
        notif_tags: '#wpnb_send_tags',
        notif_area: '#wpnb_send_area',
        edit_key: '#wpnb_edit_key',
        resp_area: '#wpnb_response_area',
        edit_url: '#wpnb_edit_url'
    };
    var cls = {
        res_loop: '.wpnb-res-loop',
        res_close: '.wpnb-tp-close',
        res_count: '.wpnb-dt-res-count',
        tags_count: '.wpnb-dt-tags-count',
        date_status: '.wpnb-dt-date-st'
    };
    var setReceiverNameString = function () {
        $(ids.res_name_slc).hide();
        $(ids.res_name_str).fadeIn();
    };
    var setReceiverNameSelect = function () {
        $(ids.res_name_slc).fadeIn();
        $(ids.res_name_str).hide();
    };
    var setReceiverNameAsSelect = function (name, set_placeholder) {
        if ( set_placeholder === void 0 ) set_placeholder = true;

        var _a;
        (_a = $(ids.res_name_slc).find(("option[value=\"" + name + "\"]"))) === null || _a === void 0 ? void 0 : _a.prop('selected', true);
        if (set_placeholder) {
            manageResDataPlaceholder(name);
        }
    };
    var setReceiverDataPlaceholder = function (plc) {
        $(ids.res_data).attr('placeholder', plc);
    };
    var setReceiverData = function (data) {
        $(ids.res_data).val(data);
    };
    var manageResDataPlaceholder = function (value) {
        var _a;
        var placeholder = (_a = $(ids.res_name_slc).find(("option[value=\"" + value + "\"]"))) === null || _a === void 0 ? void 0 : _a.data('wpnb-text');
        setReceiverDataPlaceholder(placeholder ? placeholder : '');
    };
    var detectEzAccess = function (e) {
        var _a;
        var order = (_a = $(e.target).find('option:selected')) === null || _a === void 0 ? void 0 : _a.data('wpnb-ez');
        if (!order) {
            return null;
        }
        return order.split(':', 2);
    };
    $(ids.res_name_slc).on('change', function (e) {
        var _a;
        var value = $(e.target).find('option:selected').val();
        setReceiverData('');
        if (value === 'user-role') {
            $(ids.res_data).attr('list', 'wpnb_send_usr_roles');
        }
        else {
            $(ids.res_data).removeAttr('list');
        }
        var ezDetect = detectEzAccess(e);
        if (ezDetect !== null) {
            value = ezDetect[0];
            setReceiverNameAsSelect(value);
            setReceiverData(ezDetect[1]);
        }
        if (value === 'custom') {
            setReceiverNameString();
            setReceiverDataPlaceholder(((_a = $(e.target).find('option[value="custom"]')) === null || _a === void 0 ? void 0 : _a.data('wpnb-text')) || '');
        }
        manageResDataPlaceholder(value);
    });
    $(ids.res_name_str).on('dblclick', function () {
        setReceiverNameSelect();
        setReceiverNameAsSelect('user-id');
    });
    $(ids.res_name_str).on('change input', function () {
        var value = $(ids.res_name_str).val();
        $(ids.res_name_str).val(adminCore.slugify(value));
    });
    $(ids.notif_tags).on('change input', function () {
        var value = $(ids.notif_tags).val();
        var cleanValue = adminCore.slugify(value, ',').replace(/\,+/g, ',');
        $(ids.notif_tags).val(cleanValue);
        var tags = cleanValue.split(',');
        tags = tags.filter(function (t) { return t !== ''; });
        tags = tags.filter(function (item, pos, ary) {
            return !pos || item != ary[pos - 1];
        });
        var countValue = tags.length === 0 ? '[Optional]' : ("[" + (tags.length) + "]");
        $(cls.tags_count).html(countValue);
    });
    var buildReceiverHtmlBlock = function (id, receiver) {
        return ("<div class=\"cell:100 cell-sm:50 cell-md:33 wpnb-res-loop\" data-wpnb-res-id=\"" + id + "\" data-wpnb-res-name=\"" + (receiver.name) + "\" data-wpnb-res-data=\"" + (receiver.data) + "\">\n        <div class=\"wpnb-ad-res-box\">\n            <div class=\"wpnb-hide-more wpnb-w-100\">\n                <span class=\"wpnb-tp-title\">" + (receiver.name) + "</span>\n                <span class=\"wpnb-tp-text\">\n                    " + (receiver.data) + "\n                </span>\n            </div>\n            \n            <span style=\"flex-grow: 1; text-align: end;\">\n                <button class=\"wpnb-tp-close\" data-wpnb-res-close-id=\"" + id + "\">\n                    &times;\n                </button>\n            </span>\n        </div>\n    </div>");
    };
    var setReceiversEmptyError = function (status) { return $(ids.res_none_alert).toggle(status); };
    var parseReceiverName = function (name) {
        if (name === 'custom') {
            return $(ids.res_name_str).val();
        }
        return name;
    };
    var addLogError = function (content) {
        alert('Error: ' + content);
    };
    var cleanReceiver = function (receiver) {
        return receiver;
    };
    var addReceiver = function (receiver) {
        receiver = cleanReceiver(receiver);
        var id = adminCore.randomStr(12);
        var html = buildReceiverHtmlBlock(id, receiver);
        var error = null;
        if (receiver.name === '') {
            error = "Without any name for receiver?";
        }
        else if (!/^[a-zA-Z0-9-]+$/.test(receiver.name)) {
            error = "Receiver name is unvalid! Use (a-zA-Z0-9) and \"-\" for enter receiver name.";
        }
        if (receiver.data === '') {
            error = "Please fill the data field first.";
        }
        if (error !== null) {
            addLogError(error);
            return;
        }
        $(ids.res_area).append(html);
        $(ids.res_data).val('');
    };
    var getReceiversCount = function () {
        return document.querySelectorAll(cls.res_loop).length;
    };
    var removeReceiver = function (id) {
        var _a;
        (_a = $(("[data-wpnb-res-id=\"" + id + "\"]"))) === null || _a === void 0 ? void 0 : _a.remove();
    };
    $(ids.res_add_btn).on('click', function () {
        var name = parseReceiverName($(ids.res_name_slc).val());
        var data = $(ids.res_data).val();
        if (hasReceiver(name, data)) {
            errorHandle('The receiver is duplicate!');
            return;
        }
        var receiver = {
            name: name,
            data: data
        };
        addReceiver(receiver);
    });
    $(ids.res_area).on('DOMNodeInserted DOMNodeRemoved', function (e) {
        var type = e.type;
        var target = e.target;
        if ($(target).hasClass(cls.res_loop.slice(1))) {
            $(target).find(cls.res_close).on('click', function (ev) {
                removeReceiver($(ev.target).data('wpnb-res-close-id'));
            });
            var count = getReceiversCount();
            var print = type === 'DOMNodeInserted' ? count : count - 1;
            $(cls.res_count).text(print === 0 ? '' : ("(" + print + ")"));
            setReceiversEmptyError(print === 0);
        }
    });
    $(ids.notif_date).on('input change', function (e) {
        var target = e.target;
        $(target).val($(target).val().slice(0, 19));
        var val = $(target).val();
        if (val === '') {
            $(cls.date_status).html('Empty: Auto fill');
        }
        else if (!isDateFormatValid($(target).val())) {
            $(cls.date_status).html('Incorrect');
        }
        else {
            $(cls.date_status).html('');
        }
    });
    $(ids.notif_sender).on('input change', function (e) {
        $(e.target).val(adminCore.slugify($(e.target).val()));
    });
    window.addEventListener('load', function () {
        var _a;
        setReceiverNameAsSelect('user-id');
        var changed = [ids.notif_date, ids.notif_sender, ids.notif_tags];
        var event = new Event('change');
        for (var element of changed) {
            (_a = document.querySelector(element)) === null || _a === void 0 ? void 0 : _a.dispatchEvent(event);
        }
        loadReceiversFromValue();
    });
    var loadReceiversFromValue = function () {
        var data = $(ids.res_area).data('wpnb-value');
        if (!Array.isArray(data) || data.length === 0) {
            return;
        }
        for (var res of data) {
            var receiver = { name: res.name, data: res.data };
            addReceiver(receiver);
        }
    };
    var getNotifTitle = function () { return $(ids.notif_title).val(); };
    var getNotifText = function () { return $(ids.notif_text).val(); };
    var getNotifTextType = function () { return $(ids.notif_text_type).val(); };
    var getNotifTags = function () { return $(ids.notif_tags).val(); };
    var getNotifDate = function () { return $(ids.notif_date).val(); };
    var getNotifSender = function () { return $(ids.notif_sender).val(); };
    var resetAllFields = function () {
        var inputes = [
            ids.notif_title,
            ids.notif_date,
            ids.notif_sender,
            ids.notif_text,
            ids.notif_tags
        ];
        for (var i of inputes) {
            $(i).val('');
        }
        $(ids.notif_text_type).val($(ids.notif_text_type).find("option:first").val());
        $(ids.res_area).find(cls.res_loop).remove();
    };
    $(ids.send_btn).click(function (e) {
        if (isSending) {
            errorHandle('Please wait until the notif is sent.');
            return;
        }
        var action = $(e.target).data('wpnb-action');
        var act = 'send';
        if (action && action === 'edit' && !!document.querySelector(ids.edit_key)) {
            act = 'edit';
        }
        isSending = true;
        var notif_sender = getNotifSender();
        var notif_title = getNotifTitle();
        var notif_text = getNotifText();
        var notif_format = getNotifTextType();
        var notif_date = getNotifDate();
        var notif_tags = getNotifTags();
        var notif = {
            sender: notif_sender,
            title: notif_title,
            text: notif_text,
            format: notif_format,
            date: notif_date,
            tags: notif_tags.split(',').filter(function (i) { return i !== ''; }),
            receivers: getReceivers()
        };
        var error = [];
        if (notif.sender === '') {
            error.push('Notif sender is required!');
        }
        if (notif.title === '') {
            error.push('Notif title is required!');
        }
        if (notif.text === '') {
            error.push('Can not send notif without content!');
        }
        if (notif.format === '') {
            error.push('Please select notif content format!');
        }
        if (notif.receivers.length === 0) {
            error.push('You can not send notif without any receiver.');
        }
        if (notif.date !== '' && !isDateFormatValid(notif.date)) {
            error.push('Notif date must entered as yyyy-mm-dd hh:ii:ss or just leave it blank for auto fill.');
        }
        if (error.length !== 0) {
            isSending = false;
            var error_string = error.map(function (e) { return '- ' + e; }).join('\n');
            errorHandle('Please fix the following error(s):\n' + error_string);
            return;
        }
        if (act === 'send') {
            sendViaAjax(notif);
        }
        else {
            editViaAjax(notif);
        }
        isSending = false;
    });
    var setBtnStatus = function (enable) {
        var e = $(ids.send_btn);
        if (enable) {
            e.removeAttr('disabled');
        }
        else {
            e.attr('disabled', 'disabled');
        }
    };
    var editViaAjax = function (notif) {
        var ajax_url = dataObj.ajax_url;
        var security = dataObj.security;
        var key = $(ids.edit_key).val();
        resultMsgTrying(false);
        setBtnStatus(false);
        $.ajax({
            type: "post",
            dataType: "json",
            url: ajax_url,
            data: {
                notif: notif,
                action: 'wpnb_edit_notif',
                security: security,
                key: key
            },
            success: function (res) {
                if (res.success) {
                    resultMsgHandle(res.data.status, res, res.data.errors, res.data.data);
                    return;
                }
                resultMsgHandle('None', res);
            },
            error: function (res) {
                resultMsgHandle('Error', res);
            }
        }).done(function () {
            setBtnStatus(true);
        });
    };
    var sendViaAjax = function (notif) {
        var ajax_url = dataObj.ajax_url;
        var security = dataObj.security;
        resultMsgTrying(true);
        setBtnStatus(false);
        $.ajax({
            type: "post",
            dataType: "json",
            url: ajax_url,
            data: {
                notif: notif,
                action: 'wpnb_send_notif',
                security: security
            },
            success: function (res) {
                if (res.success) {
                    if (res.data.status === 'sent') {
                        resetAllFields();
                    }
                    resultMsgHandle(res.data.status, res, res.data.errors, res.data.data);
                    return;
                }
                resultMsgHandle('None', res);
            },
            error: function (res) {
                resultMsgHandle('Error', res);
            }
        }).done(function () {
            setBtnStatus(true);
        });
    };
    var resultMsgTrying = function (is_send) {
        var msg = is_send ? 'Sending notification ...' : 'Updating notification ...';
        $(ids.resp_area).html(("\n        <div class=\"wpnb-msg-sent wpnb-w-100 wpnb-alert wpnb-alert-bg-primary wpnb-mt-4\">\n            " + msg + "\n        </div>\n    "));
    };
    var resultMsgHandle = function (result, res, errors, data) {
        if ( res === void 0 ) res = {};
        if ( errors === void 0 ) errors = [];
        if ( data === void 0 ) data = [];

        var _a;
        var e = $(ids.resp_area);
        var is_edit = $(ids.send_btn).data('wpnb-action') === 'edit';
        var color = result === 'sent' || result === 'updated' ? 'success' : 'danger';
        var value = '';
        var inner = '';
        if (is_edit) {
            value = result === 'updated' ? 'Result: The notification was updated.' : 'Result: An error occurred while updating.';
        }
        else {
            value = result === 'sent' ? 'Result: The notification was sent.' : 'Result: An error occurred while sending.';
        }
        if (result === 'sent') {
            var edit_url = (_a = $(ids.edit_url)) === null || _a === void 0 ? void 0 : _a.val().replace('{key}', data['key']);
            inner += "\n            <a href=\"" + edit_url + "\">\n                Edit notif\n            </a>\n        ";
        }
        var msg = "\n        <div class=\"wpnb-msg-sent wpnb-w-100 wpnb-alert wpnb-alert-bg-" + color + " wpnb-mt-4\">\n            " + value + "\n            " + inner + "\n        </div>\n    ";
        if (result !== 'None') {
            msg += "\n            <div class=\"wpnb-msg-sent wpnb-w-100 wpnb-alert wpnb-alert-cl-black wpnb-mt-4\">\n                Sender/Updater result: " + result + " <br />\n                Data: " + (JSON.stringify(data)) + " <br />\n                Errors: " + (JSON.stringify(errors)) + "\n            </div>\n        ";
        }
        else {
            msg += "\n            <div class=\"wpnb-msg-sent wpnb-w-100 wpnb-alert wpnb-alert-cl-black wpnb-mt-4\">\n                Error code: " + (res.data.error) + " <br />\n                Error msg: " + (res.data.msg) + " <br />\n            </div>\n        ";
        }
        e.html(msg);
    };
    var isSending = false;
    var errorHandle = function (msg) {
        alert(msg);
    };
    var getReceivers = function () {
        var e = $(cls.res_loop);
        var fetch = [];
        e.each(function (i) {
            var res = {
                'name': e.eq(i).data('wpnb-res-name'),
                'data': e.eq(i).data('wpnb-res-data'),
            };
            fetch.push(res);
        });
        return fetch;
    };
    var isDateFormatValid = function (date) {
        var reg = /[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1]) (2[0-3]|[01][0-9]):[0-5][0-9]:[0-5][0-9]/;
        return !!reg.exec(date);
    };
    var hasReceiver = function (name, data) {
        var receivers = getReceivers();
        for (var r of receivers) {
            if (r.name === name && r.data === data) {
                return true;
            }
        }
        return false;
    };

})();
//# sourceMappingURL=send-adm.js.map
