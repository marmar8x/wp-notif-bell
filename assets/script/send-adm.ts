// @ts-ignore
const $: Function = jQuery;

// @ts-ignore
const adminCore = WpnbAdm;

// @ts-ignore
const dataObj: { ajax_url: string, security: string } = wpnb_data_obj;

type NotifReceiver = {
    name: string;
    data: string;
}

type Notif = {
    sender:     string;
    title:      string;
    text:       string;
    format:     string;
    date:       string;
    tags:       Array<string>;
    receivers:  Array<NotifReceiver>;
}

// list of all ids of main elements
const ids = {
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
}

// list of all classes of main elements
const cls = {
    res_loop: '.wpnb-res-loop',
    res_close: '.wpnb-tp-close',
    res_count: '.wpnb-dt-res-count',
    tags_count: '.wpnb-dt-tags-count',
    date_status: '.wpnb-dt-date-st'
}

// set receiver name to string
const setReceiverNameString = () => {
    $(ids.res_name_slc).hide();
    $(ids.res_name_str).fadeIn();
}

// set receiver name to select menu
const setReceiverNameSelect = () => {
    $(ids.res_name_slc).fadeIn();
    $(ids.res_name_str).hide();
}

// set a option in res selective menu
const setReceiverNameAsSelect = (name: string, set_placeholder: boolean = true) => {
    $(ids.res_name_slc).find(`option[value="${name}"]`)?.prop('selected', true);

    if (set_placeholder) {
        manageResDataPlaceholder(name);
    }
}

// set res data placeholder
const setReceiverDataPlaceholder = (plc: string) => {
    $(ids.res_data).attr('placeholder', plc);
}

// set receiver data
const setReceiverData = (data: string) => {
    $(ids.res_data).val(data);
}

// manage receiver data placeholder value
const manageResDataPlaceholder = (value: string) => {
    const placeholder = $(ids.res_name_slc).find(`option[value="${value}"]`)?.data('wpnb-text');

    setReceiverDataPlaceholder(placeholder ? placeholder : '');
}

// detect easy access data
const detectEzAccess = (e: Event): [string, string] | null => {
    const order = $(e.target).find('option:selected')?.data('wpnb-ez');

    if (!order) {
        return null;
    }
    
    return order.split(':', 2);
}

// event for when receiver select menu value changing
$(ids.res_name_slc).on('change', (e: Event) => {
    let value: string = $(e.target).find('option:selected').val() as string;

    setReceiverData('');

    // user roles is selected
    if (value === 'user-role') {
        $(ids.res_data).attr('list', 'wpnb_send_usr_roles');
    } else {
        $(ids.res_data).removeAttr('list');
    }

    // manage eazy access ones
    const ezDetect = detectEzAccess(e);

    if (ezDetect !== null) {
        value = ezDetect[0];

        setReceiverNameAsSelect(value);
        setReceiverData(ezDetect[1]);
    }

    if (value === 'custom') {
        setReceiverNameString();
        setReceiverDataPlaceholder( $(e.target).find('option[value="custom"]')?.data('wpnb-text') || '' );
    }

    // set placehodler for that one
    manageResDataPlaceholder(value);
});

// when the receiver name as string double clicked
$(ids.res_name_str).on('dblclick', () => {
    setReceiverNameSelect();
    setReceiverNameAsSelect('user-id');
});

// when the receiver custom name input changing
$(ids.res_name_str).on('change input', () => {
    const value = $(ids.res_name_str).val();

    $(ids.res_name_str).val( adminCore.slugify(value) );
});

// for notif tags list renew and manage
$(ids.notif_tags).on('change input', () => {
    const value: string      = $(ids.notif_tags).val();
    const cleanValue: string = adminCore.slugify(value, ',').replace(/\,+/g, ',');

    // clean text input
    $(ids.notif_tags).val( cleanValue as string );

    // clean the list of tags
    let tags: Array<string> = cleanValue.split(',');
    tags = tags.filter((t: string): boolean => t !== '');

    // unique items
    tags = tags.filter((item, pos, ary) => {
        return !pos || item != ary[pos - 1];
    });

    // update the count
    let countValue: string = tags.length === 0 ? '[Optional]' : `[${tags.length}]`;
    $(cls.tags_count).html( countValue );
});

// build a receiver html block to render
const buildReceiverHtmlBlock = (id: string, receiver: NotifReceiver) => {
    return `<div class="cell:100 cell-sm:50 cell-md:33 wpnb-res-loop" data-wpnb-res-id="${id}" data-wpnb-res-name="${receiver.name}" data-wpnb-res-data="${receiver.data}">
        <div class="wpnb-ad-res-box">
            <div class="wpnb-hide-more wpnb-w-100">
                <span class="wpnb-tp-title">${receiver.name}</span>
                <span class="wpnb-tp-text">
                    ${receiver.data}
                </span>
            </div>
            
            <span style="flex-grow: 1; text-align: end;">
                <button class="wpnb-tp-close" data-wpnb-res-close-id="${id}">
                    &times;
                </button>
            </span>
        </div>
    </div>`;
}

// empty block for no receiver in the list
const setReceiversEmptyError = (status: boolean) => $(ids.res_none_alert).toggle(status);

// get receiver name + ...
const parseReceiverName = (name: string) => {
    if (name === 'custom') {
        return $(ids.res_name_str).val();
    }

    return name;
}

const addLogError = (content: string) => {
    alert('Error: ' + content);
}

const cleanReceiver = (receiver: NotifReceiver): NotifReceiver => {
    return receiver;
}

// add a receiver to list
const addReceiver = (receiver: NotifReceiver) => {
    receiver = cleanReceiver(receiver);

    const id: string    = adminCore.randomStr(12);
    const html: string  = buildReceiverHtmlBlock(id, receiver);

    // error handler before adding receiver
    let error = null;

    // check for name
    if (receiver.name === '') {
        error = `Without any name for receiver?`;
    } else if (!/^[a-zA-Z0-9-]+$/.test(receiver.name)) {
        error = `Receiver name is unvalid! Use (a-zA-Z0-9) and "-" for enter receiver name.`;
    }

    // check for data
    if (receiver.data === '') {
        error = `Please fill the data field first.`;
    }

    if (error !== null) {
        addLogError(error);

        return;
    }

    $(ids.res_area).append(html);
    $(ids.res_data).val('');
}

// get receivers count
const getReceiversCount = (): number => {
    return document.querySelectorAll(cls.res_loop).length as number;
}

// remove a receiver from list
const removeReceiver = (id: string) => {
    $(`[data-wpnb-res-id="${id}"]`)?.remove();
}

$(ids.res_add_btn).on('click', () => {
    const name = parseReceiverName( $(ids.res_name_slc).val() );
    const data = $(ids.res_data).val();

    if (hasReceiver(name, data)) {
        errorHandle('The receiver is duplicate!');

        return;
    }

    const receiver: NotifReceiver = {
        name,
        data
    }

    addReceiver(receiver);
});

$(ids.res_area).on('DOMNodeInserted DOMNodeRemoved', (e: Event) => {
    const { type, target } = e;
    
    if ($(target).hasClass(cls.res_loop.slice(1))) {
        // add remove button click event
        $(target).find(cls.res_close).on('click', (ev: Event) => {
            removeReceiver( $(ev.target).data('wpnb-res-close-id') );
        });

        // update receiver count
        const count: number = getReceiversCount();
        const print: any = type === 'DOMNodeInserted' ? count : count - 1;

        $(cls.res_count).text( print === 0 ? '' : `(${print})` );

        // show or hide empty block alert
        setReceiversEmptyError(print === 0);
    }
});

$(ids.notif_date).on('input change', (e: Event) => {
    const { target } = e;

    $(target).val( $(target).val().slice(0, 19) );

    const val = $(target).val();

    if (val === '') {
        $(cls.date_status).html('Empty: Auto fill');
    } else if (!isDateFormatValid( $(target).val() )) {
        $(cls.date_status).html('Incorrect');
    } else {
        $(cls.date_status).html('');
    }
});

$(ids.notif_sender).on('input change', (e: Event) => {
    $(e.target).val( adminCore.slugify($(e.target).val()) );
});

// when window loaded: init
window.addEventListener('load', () => {
    // set default res name to user-id
    setReceiverNameAsSelect('user-id');

    // run all change events for inputs
    const changed = [ids.notif_date, ids.notif_sender, ids.notif_tags];
    const event   = new Event('change');

    for (let element of changed) {
        document.querySelector(element)?.dispatchEvent(event);
    }

    // check for receivers
    loadReceiversFromValue();
});

const loadReceiversFromValue = () => {
    const data = $(ids.res_area).data('wpnb-value');

    if (!Array.isArray(data) || data.length === 0) {
        return;
    }

    for (const res of data) {
        const receiver: NotifReceiver = {name: res.name, data: res.data};
        addReceiver(receiver);
    }
}

// get notification title
const getNotifTitle     = (): string => $(ids.notif_title).val() as string;

// get notification text
const getNotifText      = (): string => $(ids.notif_text).val() as string;

// get notification text type (format)
const getNotifTextType  = (): string => $(ids.notif_text_type).val() as string;

// get notification text type (format)
const getNotifTags      = (): string => $(ids.notif_tags).val() as string;

// get notification send date
const getNotifDate      = (): string => $(ids.notif_date).val() as string;

// get notification sender
const getNotifSender    = (): string => $(ids.notif_sender).val() as string;

// reset fields
const resetAllFields = () => {
    const inputes = [
        ids.notif_title,
        ids.notif_date,
        ids.notif_sender,
        ids.notif_text,
        ids.notif_tags
    ];

    for (const i of inputes) {
        $(i).val('');
    }

    $(ids.notif_text_type).val( $(ids.notif_text_type).find("option:first").val() );

    $(ids.res_area).find(cls.res_loop).remove();
}

/**
 * Send process
 */
$(ids.send_btn).click((e: Event) => {
    // abort if prev proccess is still running
    if (isSending) {
        errorHandle('Please wait until the notif is sent.');

        return;
    }

    // check type
    const action = $(e.target).data('wpnb-action');
    let act      = 'send';

    if (action && action === 'edit' && !!document.querySelector(ids.edit_key)) {
        act = 'edit';
    }
    
    // trying to send
    isSending = true;

    let notif_sender    = getNotifSender();
    let notif_title     = getNotifTitle();
    let notif_text      = getNotifText();
    let notif_format    = getNotifTextType();
    let notif_date      = getNotifDate();
    let notif_tags      = getNotifTags();

    const notif: Notif = {
        sender:     notif_sender,
        title:      notif_title,
        text:       notif_text,
        format:     notif_format,
        date:       notif_date,
        tags:       notif_tags.split(',').filter(i => i !== ''),
        receivers:  getReceivers()
    }

    let error: string[] = [];

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
        const error_string: string = error.map(e => '- ' + e).join('\n');
        errorHandle('Please fix the following error(s):\n' + error_string);

        return;
    }

    if (act === 'send') {
        sendViaAjax(notif);
    } else {
        editViaAjax(notif);
    }

    isSending = false;
});

// send notification
const setBtnStatus = (enable: boolean) => {
    const e = $(ids.send_btn);

    if (enable) {
        e.removeAttr('disabled');
    } else {
        e.attr('disabled', 'disabled');
    }
}

// edit notif via ajax
const editViaAjax = (notif: Notif): void => {
    const { ajax_url, security } = dataObj;
    const key = $(ids.edit_key).val();

    resultMsgTrying(false);
    setBtnStatus(false);

    // @ts-ignore
    $.ajax({
        type: "post",
        dataType: "json",
        url: ajax_url,
        data: {
            notif,
            action: 'wpnb_edit_notif',
            security,
            key
        },
        success: (res: any) => {
            if (res.success) {
                resultMsgHandle(res.data.status, res, res.data.errors, res.data.data);
                return;
            }

            resultMsgHandle('None', res);
        },
        error: (res: any) => {
            resultMsgHandle('Error', res);
        }
    }).done(() => {
        setBtnStatus(true);
    });
}

// send notif by ajax [main]
const sendViaAjax = (notif: Notif): void => {
    const { ajax_url, security } = dataObj;

    resultMsgTrying(true);
    setBtnStatus(false);

    // @ts-ignore
    $.ajax({
        type: "post",
        dataType: "json",
        url: ajax_url,
        data: {
            notif,
            action: 'wpnb_send_notif',
            security
        },
        success: (res: any) => {
            if (res.success) {

                if (res.data.status === 'sent') {
                    resetAllFields();
                }

                resultMsgHandle(res.data.status, res, res.data.errors, res.data.data);
                return;
            }

            resultMsgHandle('None', res);
        },
        error: (res: any) => {
            resultMsgHandle('Error', res);
        }
    }).done(() => {
        setBtnStatus(true);
    });
}

const resultMsgTrying = (is_send: boolean) => {
    const msg = is_send ? 'Sending notification ...' : 'Updating notification ...';
    
    $(ids.resp_area).html(`
        <div class="wpnb-msg-sent wpnb-w-100 wpnb-alert wpnb-alert-bg-primary wpnb-mt-4">
            ${msg}
        </div>
    `);
}

const resultMsgClear = () => {
    $(ids.resp_area).html('');
}

// show html message
const resultMsgHandle = (result: string, res: any = {}, errors: Array<any> = [], data: any = []) => {
    const e         = $(ids.resp_area);
    const is_edit   = $(ids.send_btn).data('wpnb-action') === 'edit';
    const color     = result === 'sent' || result === 'updated' ? 'success' : 'danger';
    let value       = '';
    let inner       = '';

    if (is_edit) {
        value = result === 'updated' ? 'Result: The notification was updated.' : 'Result: An error occurred while updating.';
    } else {
        value = result === 'sent' ? 'Result: The notification was sent.' : 'Result: An error occurred while sending.';
    }

    if (result === 'sent') {
        const edit_url = $(ids.edit_url)?.val().replace('{key}', data['key']);

        inner += `
            <a href="${edit_url}">
                Edit notif
            </a>
        `;
    }

    let msg = `
        <div class="wpnb-msg-sent wpnb-w-100 wpnb-alert wpnb-alert-bg-${color} wpnb-mt-4">
            ${value}
            ${inner}
        </div>
    `;

    if (result !== 'None') {
        msg += `
            <div class="wpnb-msg-sent wpnb-w-100 wpnb-alert wpnb-alert-cl-black wpnb-mt-4">
                Sender/Updater result: ${result} <br />
                Data: ${JSON.stringify(data)} <br />
                Errors: ${JSON.stringify(errors)}
            </div>
        `;
    } else {
        msg += `
            <div class="wpnb-msg-sent wpnb-w-100 wpnb-alert wpnb-alert-cl-black wpnb-mt-4">
                Error code: ${res.data.error} <br />
                Error msg: ${res.data.msg} <br />
            </div>
        `;
    }

    e.html(msg);
}

// trying to send notif right now?
let isSending: boolean = false;

// handle errors
const errorHandle = (msg: string) => {
    alert(msg);
}

// get all receivers from elements
const getReceivers = (): NotifReceiver[] => {
    const e                     = $(cls.res_loop);
    let fetch: NotifReceiver[]  = [];

    e.each((i: any) => {
        const res: NotifReceiver = {
            'name': e.eq(i).data('wpnb-res-name'),
            'data': e.eq(i).data('wpnb-res-data')
        };

        fetch.push(res);
    });

    return fetch;
}

const isDateFormatValid = (date: string): boolean => {
    // regexp for yyyy-mm-dd HH:MM:SS (m=i)
    const reg = /[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1]) (2[0-3]|[01][0-9]):[0-5][0-9]:[0-5][0-9]/;

    return !!reg.exec(date);
}

const hasReceiver = (name: string, data: string): boolean => {
    const receivers: NotifReceiver[] = getReceivers();

    for (const r of receivers) {
        if (r.name === name && r.data === data) {
            return true;
        }
    }

    return false;
}