// @ts-ignore
const $: Function = jQuery;

// @ts-ignore
const adminCore = WpnbAdm;

// @ts-ignore
const hash = Hash;

// list of settings tab ref
const ref = {
    nav_tab_wap: '.wpnb-tab-wrapper',
    nav_tab: '.wpnb-tab-wrapper .nav-tab',
    tab_error: '.wpnb-tabs .wpnb-tab-error',
    tab_content: '.wpnb-tabs .tab-content',
    nav_tab_active_cls: 'nav-tab-active',
    tab_error_nav: '.wpnb-tab-error-aps'
}

interface QueryChangeData {
    from: object;
    to: object;
    str: object;
}

// active tab function
const _activateTab = (tab: string): void => {
    // set a style for active tab btn
    $(ref.nav_tab).removeClass(ref.nav_tab_active_cls);
    $(ref.nav_tab + `[data-wpnb-tab-set="${tab}"]`).addClass(ref.nav_tab_active_cls);

    // show active tab and hide others
    const target_id  = ref.tab_content + `[data-wpnb-tab="${tab}"]`;
    const target_tab = $(target_id);

    // remove error tab
    $(ref.tab_error_nav).remove();

    // hide all other contents
    $(ref.tab_content).removeAttr('data-wpnb-tab-active');

    // not found error :)
    if (0 === target_tab.length) {
        $(ref.nav_tab).removeClass(ref.nav_tab_active_cls);
        $(ref.tab_error).attr('data-wpnb-tab-active', '');

        $(ref.nav_tab_wap).append(`
            <button type="button" class="nav-tab wpnb-tab-error-aps" style="color: #d63031;">
                Error
            </buttn>
        `);

        return;
    }

    // show the fk tab
    target_tab.attr('data-wpnb-tab-active', '');
}

// init hash functions
const initHashListener = (): void => {
    // look for tab when page loaded
    if (hash.q.have('tab')) {
        const tab: string = hash.q.get('tab');
        _activateTab(tab);
    }

    // listen to query change
    hash.on('change.query', ({ to }: QueryChangeData) => {
        if ('tab' in to) {
            const { tab } = to;

            _activateTab(tab as string);
        }
    });
}

// event for page load
window.addEventListener('load', () => {
    initHashListener();
    initSettingsTab();
});

// all about settings tab list
const initSettingsTab = (): void => {
    // when clicks on tab buttons
    $(ref.nav_tab).click(function () {
        // @ts-ignore
        const tab = $(this).data('wpnb-tab-set') ?? '';

        if ('' !== tab) {
            hash.q.set('tab', tab);
        }
    });
}