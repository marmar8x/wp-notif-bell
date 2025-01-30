(function () {
    'use strict';

    var $ = jQuery;
    var cmPack = window.CM;
    var adminCore = WpnbAdm;
    var markdown = marked;
    var quill = Quill;
    var dataObj = wpnb_data_obj;
    var i18nText = wpnb_texts;
    var getI18nText = function (key) {
        var _a;
        return (_a = i18nText[key]) !== null && _a !== void 0 ? _a : '';
    };
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
        edit_url: '#wpnb_edit_url',
        preview_fr: '#wpnb_preview_frame',
        simple_editor: '#wpnb_simple_text',
        text_content: '#wpnb_text_content',
        quill_editor: '#wpnb_quill_editor',
        simple_editor_area: '#wpnb_simple_editor',
        mce_editor: '#wpnb_mce_editor',
        cm_md_ed: '#wpnb_ed_md_cm',
        cm_html_ed: '#wpnb_ed_html_cm',
        area_cm_md: '#wpnb_area_cm_md',
        area_code_ed: '#wpnb_code_editor',
        area_visual_ed: '#wpnb_visual_editor',
        raw_content: '#wpnb_raw_text_content'
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
                'data': e.eq(i).data('wpnb-res-data')
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
            if (r.name == name && r.data == data) {
                return true;
            }
        }
        return false;
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
        var countValue = tags.length === 0 ? ("[" + (getI18nText('optional')) + "]") : ("[" + (tags.length) + "]");
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
    var cleanReceiver = function (receiver) {
        return receiver;
    };
    var addReceiver = function (receiver) {
        receiver = cleanReceiver(receiver);
        var id = adminCore.randomStr(12);
        var html = buildReceiverHtmlBlock(id, receiver);
        var error = null;
        if (receiver.name === '') {
            error = getI18nText('err.res-name');
        }
        else if (!/^[a-zA-Z0-9-]+$/.test(receiver.name)) {
            error = getI18nText('err.res-name-fr');
        }
        if (receiver.data === '') {
            error = getI18nText('err.res-data');
        }
        if (error !== null) {
            errorHandle(error);
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
            errorHandle(getI18nText('err.res-name-dup'));
            return;
        }
        var receiver = {
            name: name,
            data: data
        };
        addReceiver(receiver);
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
            $(cls.date_status).html(getI18nText('emp-auto'));
        }
        else if (!isDateFormatValid($(target).val())) {
            $(cls.date_status).html(getI18nText('incorrect'));
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
        loadPreview();
        loadReceiversFromValue();
        loadTextEditor();
        initEditorsChange();
        setEnableEditorByFormat(getNotifFormat());
        window.setInterval(function () {
            loadPreview();
        }, 5000);
    });
    var editor_type = '';
    var editor_visual = '';
    var editor_base = '';
    var quill_editor = null;
    var editor_now = 'simple';
    var setEnableEditor = function (editor) {
        editor_now = editor;
        if (editor !== 'simple') {
            $(ids.simple_editor_area).hide();
        }
        else {
            $(ids.simple_editor_area).show();
        }
        if (editor === 'html') {
            $(ids.area_code_ed).show();
        }
        else {
            $(ids.area_code_ed).hide();
        }
        if (editor === 'markdown') {
            $(ids.area_cm_md).show();
        }
        else {
            $(ids.area_cm_md).hide();
        }
        if (editor === 'quill' || editor === 'wp') {
            $(ids.area_visual_ed).show();
        }
        else {
            $(ids.area_visual_ed).hide();
        }
        syncEditors();
    };
    var loadTextEditor = function () {
        var _a;
        var ei = $(ids.text_content);
        var value = getTextStatic();
        editor_visual = ei.data('wpnb-visual-editor');
        editor_type = ei.data('wpnb-editor');
        editor_base = ei.data('wpnb-editor-base');
        if (editor_type === 'simple') {
            editor_now = 'simple';
            return;
        }
        if (editor_base === 'visual' && editor_visual === 'quill') {
            var toolbarOptions = [
                ['bold', 'italic', 'underline', 'strike'],
                ['blockquote'],
                ['link', 'image', 'video'],
                [{ 'list': 'ordered' }, { 'list': 'bullet' }, { 'list': 'check' }],
                [{ 'size': ['small', false, 'large', 'huge'] }],
                [{ 'header': [1, 2, 3, 4, 5, 6, false] }],
                [{ 'color': [] }, { 'background': [] }],
                ['clean']
            ];
            quill_editor = new quill(ids.quill_editor, {
                theme: (_a = dataObj.quill.theme) !== null && _a !== void 0 ? _a : 'snow',
                modules: {
                    toolbar: toolbarOptions
                },
                placeholder: getI18nText('notif.text-pl'),
            });
            if (value) {
                quill_editor.setContents(value);
            }
            if (dataObj.dir === 'rtl') {
                quill_editor.format('direction', 'rtl');
                quill_editor.format('align', 'right');
            }
            editor_now = 'quill';
        }
        initMarkdownEditor(value);
        if (editor_base === 'code') {
            initHtmlEditor(value);
        }
    };
    var initEditorsChange = function () {
        $(ids.simple_editor).on('change input', function () {
            saveTextStatic($(ids.simple_editor).val());
        });
        if (editor_type === 'auto') {
            if (editor_base === 'visual') {
                if (editor_visual === 'quill') {
                    quill_editor.on('text-change', function () {
                        saveTextStatic(quill_editor.root.innerHTML);
                    });
                }
                else {
                    var edt = tinyMCE.get(ids.mce_editor.slice(1));
                    edt === null || edt === void 0 ? void 0 : edt.on('change', function (e) {
                        var dd = edt.getContent()
                            .replace(/(&nbsp;)*/g, "");
                        saveTextStatic(dd);
                    });
                }
            }
        }
    };
    var syncEditors = function () {
        var _a;
        var content = getTextStatic();
        if (editor_now === 'simple') {
            $(ids.simple_editor).val(content);
        }
        else if (editor_now === 'quill') {
            quill_editor.root.innerHTML = content;
        }
        else if (editor_now === 'wp') {
            (_a = tinyMCE.get(ids.mce_editor.slice(1))) === null || _a === void 0 ? void 0 : _a.setContent(content);
        }
        else if (editor_now === 'markdown') {
            MarkdownView.dispatch({
                changes: {
                    from: 0, to: MarkdownView.state.doc.length,
                    insert: content
                }
            });
        }
        else if (editor_now === 'html') {
            HtmlView.dispatch({
                changes: {
                    from: 0, to: HtmlView.state.doc.length,
                    insert: content
                }
            });
        }
    };
    $(ids.text_content).on('change input', function (e) {
        loadPreview();
    });
    $(ids.notif_text_type).on('change', function (e) {
        loadPreview();
        var format = getNotifFormat();
        setEnableEditorByFormat(format);
    });
    var setEnableEditorByFormat = function (format) {
        if (editor_type === 'auto') {
            if (format === 'markdown') {
                setEnableEditor('markdown');
            }
            else if (format === 'html') {
                setEnableEditor(editor_base === 'code' ? 'html' : editor_visual === 'quill' ? 'quill' : 'wp');
            }
            else {
                setEnableEditor('simple');
            }
        }
        else {
            setEnableEditor('simple');
        }
    };
    var saveTextStatic = function (text, raw) {
        if ( raw === void 0 ) raw = null;

        var _a, _b;
        $(ids.text_content).val(text);
        (_a = document.querySelector(ids.text_content)) === null || _a === void 0 ? void 0 : _a.dispatchEvent(new Event('change'));
        $(ids.raw_content).val(raw !== null ? raw : text);
        (_b = document.querySelector(ids.raw_content)) === null || _b === void 0 ? void 0 : _b.dispatchEvent(new Event('change'));
    };
    var getTextStatic = function (raw) {
        if ( raw === void 0 ) raw = true;

        return $(raw ? ids.raw_content : ids.text_content).val();
    };
    var stripHtml = function (html) {
        return html.replace(/<[^>]*>?/gm, '');
    };
    var loadPreview = function () {
        var eid = ids.preview_fr;
        var el = document.querySelector(eid);
        if (!el || !el.contentDocument) {
            $(eid).hide();
            return;
        }
        var format = getNotifFormat();
        var frame_doc = el.contentDocument;
        var data = getTextStatic();
        frame_doc.open();
        if (format === 'markdown') {
            data = markdown.parse(data);
        }
        else if (format === 'text') {
            data = stripHtml(data).replace(/\n/g, '<br />');
        }
        else if (format === 'pure-text') {
            data = stripHtml(data);
        }
        if (format === 'markdown' || format === 'html') {
            data = filterXSS(data);
        }
        data = data.replace(/\[[a-zA-Z0-9\:\.\_\-]*\]/g, '<code style="color:red;">$&</code>');
        frame_doc.writeln(data);
        frame_doc.close();
    };
    var getNotifTitle = function () { return $(ids.notif_title).val(); };
    var getNotifText = function () { return getTextStatic(false); };
    var getNotifTextType = function () { return $(ids.notif_text_type).val(); };
    var getNotifTags = function () { return $(ids.notif_tags).val(); };
    var getNotifDate = function () { return $(ids.notif_date).val(); };
    var getNotifSender = function () { return $(ids.notif_sender).val(); };
    var getNotifFormat = function () { return $(ids.notif_text_type).val(); };
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
        saveTextStatic('');
        syncEditors();
    };
    var MarkdownView = null;
    var HtmlView = null;
    var initMarkdownEditor = function (value) {
        var ref = cmPack["@codemirror/view"];
        var EditorView = ref.EditorView;
        var keymap = ref.keymap;
        var lineNumbers = ref.lineNumbers;
        var placeholder = ref.placeholder;
        ref.ViewUpdate;
        var ref$1 = cmPack["@codemirror/state"];
        var EditorState = ref$1.EditorState;
        var ref$2 = cmPack["@codemirror/commands"];
        var defaultKeymap = ref$2.defaultKeymap;
        var ref$3 = cmPack["@codemirror/language"];
        var syntaxHighlighting = ref$3.syntaxHighlighting;
        var defaultHighlightStyle = ref$3.defaultHighlightStyle;
        var bracketMatching = ref$3.bracketMatching;
        var ref$4 = cmPack["@codemirror/lang-markdown"];
        var markdown = ref$4.markdown;
        MarkdownView = new EditorView({
            state: EditorState.create({
                doc: value,
                extensions: [
                    keymap.of(defaultKeymap),
                    lineNumbers(),
                    placeholder(getI18nText('editor.md-pl')),
                    bracketMatching(),
                    markdown(),
                    syntaxHighlighting(defaultHighlightStyle),
                    EditorView.updateListener.of(function (v) {
                        if (v.docChanged) {
                            saveTextStatic(v.state.doc.toString());
                        }
                    })
                ]
            }),
            parent: document.querySelector(ids.cm_md_ed)
        });
    };
    var initHtmlEditor = function (value) {
        var ref = cmPack["@codemirror/view"];
        var EditorView = ref.EditorView;
        var keymap = ref.keymap;
        var lineNumbers = ref.lineNumbers;
        var placeholder = ref.placeholder;
        var ref$1 = cmPack["@codemirror/state"];
        var EditorState = ref$1.EditorState;
        var ref$2 = cmPack["@codemirror/commands"];
        var defaultKeymap = ref$2.defaultKeymap;
        var ref$3 = cmPack["@codemirror/language"];
        var syntaxHighlighting = ref$3.syntaxHighlighting;
        var defaultHighlightStyle = ref$3.defaultHighlightStyle;
        var bracketMatching = ref$3.bracketMatching;
        var ref$4 = cmPack["@codemirror/lang-html"];
        var html = ref$4.html;
        HtmlView = new EditorView({
            state: EditorState.create({
                doc: value,
                extensions: [
                    keymap.of(defaultKeymap),
                    lineNumbers(),
                    placeholder(getI18nText('editor.ht-pl')),
                    bracketMatching(),
                    html(),
                    syntaxHighlighting(defaultHighlightStyle),
                    EditorView.updateListener.of(function (v) {
                        if (v.docChanged) {
                            saveTextStatic(v.state.doc.toString());
                        }
                    })
                ]
            }),
            parent: document.querySelector(ids.cm_html_ed)
        });
    };
    $(ids.send_btn).click(function (e) {
        if (isSending) {
            errorHandle(getI18nText('err.n-sending'));
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
            error.push(getI18nText('err.n-sender-req'));
        }
        if (notif.title === '') {
            error.push(getI18nText('err.n-title-req'));
        }
        if (notif.text === '') {
            error.push(getI18nText('err.n-text-req'));
        }
        if (notif.format === '') {
            error.push(getI18nText('err.n-format-req'));
        }
        if (notif.receivers.length === 0) {
            error.push(getI18nText('err.n-res-req'));
        }
        if (notif.date !== '' && !isDateFormatValid(notif.date)) {
            error.push(getI18nText('err.n-date'));
        }
        if (error.length !== 0) {
            isSending = false;
            var error_string = error.map(function (e) { return '- ' + e; }).join('\n');
            errorHandle(getI18nText('err.fix-msg') + '\n' + error_string);
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
        var msg = is_send ? getI18nText('msg.sending') : getI18nText('msg.updating');
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
            value = result === 'updated' ? getI18nText('res.updated') : getI18nText('res.update-f');
        }
        else {
            value = result === 'sent' ? getI18nText('res.sent') : getI18nText('res.send-f');
        }
        if (result === 'sent') {
            var edit_url = (_a = $(ids.edit_url)) === null || _a === void 0 ? void 0 : _a.val().replace('{key}', data['key']);
            inner += "\n            <a href=\"" + edit_url + "\">\n                " + (getI18nText('btn.n-edit')) + "\n            </a>\n        ";
        }
        var msg = "\n        <div class=\"wpnb-msg-sent wpnb-w-100 wpnb-alert wpnb-alert-bg-" + color + " wpnb-mt-4\">\n            " + value + "\n            " + inner + "\n        </div>\n    ";
        if (result !== 'None') {
            msg += "\n            <div class=\"wpnb-msg-sent wpnb-w-100 wpnb-alert wpnb-alert-cl-black wpnb-mt-4\">\n                " + (getI18nText('su.result')) + " " + result + " <br />\n                " + (getI18nText('data')) + " " + (JSON.stringify(data)) + " <br />\n                " + (getI18nText('errors')) + " " + (JSON.stringify(errors)) + "\n            </div>\n        ";
        }
        else {
            msg += "\n            <div class=\"wpnb-msg-sent wpnb-w-100 wpnb-alert wpnb-alert-cl-black wpnb-mt-4\">\n                " + (getI18nText('err.code')) + " " + (res.data.error) + " <br />\n                " + (getI18nText('err.msg')) + " " + (res.data.msg) + " <br />\n            </div>\n        ";
        }
        e.html(msg);
    };

})();
//# sourceMappingURL=send-adm.js.map
