<?php
/**
 * Partial: admin send page
 * send menu content for wpnb
 * 
 * @since    0.9.0
 */

// If this file is called directly, abort.
defined('WPINC') || die;

use Irmmr\WpNotifBell\Admin\Statics;
use Irmmr\WpNotifBell\Helpers\Date;
use Irmmr\WpNotifBell\Notif\Collector;

// check direction
$_is_rtl = is_rtl();

// list of all wordpress user roles
$_usr_roles = array_keys( wp_roles()->get_names() );

// get text formats (display)
$_txt_formats = wpnb_get_text_formats();

// add 'recommanded' to pure-text name
if (isset($_txt_formats['pure-text'])) {
    $_txt_formats['pure-text'] .= ' (' . __('recommanded', 'wp-notif-bell') . ')';
}

// check for edit action
$_is_edit = isset($_GET['action']) && isset($_GET['key']) && $_GET['action'] === 'edit';
$_key     = $_GET['key'] ?? '';

// get target notif if edit enabled
if ($_is_edit) {
    $_collector = (new Collector)
        ->config([ 'use_textmagic' => false ])
        ->target_by_key($_GET['key']);

    $_collector->select()->limit(0, 1);

    $_fetch = $_collector->get();
    $notif = !empty($_fetch) ? get_object_vars($_fetch[0]) : null;
} else {
    $notif = null;
}

// create edit url
$_edit_url = add_query_arg([
    'action' => 'edit',
    'key'    => '{key}'
], menu_page_url('wpnb-send', false));

/**
 * get notif field data if edit enabled
 * 
 * @access  private
 * @since   0.9.0
 * @param   string  $key
 * @param   string  $type
 * @param   bool    $esc
 * @return  mixed
 */
$_get_field_data = function (string $key, string $type = 'string', bool $esc = true) use ($notif)
{
    if (is_null($notif) || !array_key_exists($key, $notif)) {
        $value  = null;
    } else {
        $value  = $notif[$key];
    }

    if (gettype($value) !== $type) {
        settype($value, $type);
    }

    return esc_attr($value);
};

// get text editor type
$_text_editor = wpnb_get_setting('admin.ui.text_editor', 'auto');

// get visual text editor
$_visual_text_editor = wpnb_get_setting('admin.ui.visual_text_editor', 'wp');

// get content value for editing
$_text_content = $_get_field_data('content');

// get editor base
$_editor_base = wpnb_get_setting('admin.ui.editor_base', 'visual');

?>

<div class="wpnb-ad-box">
    <div class="wpnb-ad-header">
        <h3 class="wpnb-ad-header-title">
            🔔 <?php _e('WP Notif Bell', 'wp-notif-bell'); ?>
            <span class="wpnb-ad-header-tab rnd org"><?php $_is_edit ? _e('Edit', 'wp-notif-bell') : _e('Send', 'wp-notif-bell'); ?></span>
        </h3>

        <p class="wpnb-ad-header-text">
            <?php $_is_edit ? _e('Editing notification', 'wp-notif-bell') : _e('Sending notification', 'wp-notif-bell'); ?>
        </p>
    </div>

    <?php if ($_key): ?>
        <input type="hidden" id="wpnb_edit_key" value="<?php echo $_key; ?>">
    <?php else: ?>
        <input type="hidden" id="wpnb_edit_url" value="<?php echo $_edit_url; ?>">
    <?php endif; ?>

    <?php if (is_null($notif) && $_is_edit): ?>
    <div class="wpnb-w-100">
        <?php _e('There is no notification with the key you entered.', 'wp-notif-bell'); ?>
        <a href="<?php menu_page_url('wpnb-list'); ?>"><?php _e('Back to list', 'wp-notif-bell'); ?></a>
    </div>
    <?php else: ?>
    <div class="wpnb-ad-l-box">
        <div class="pack:no-gutters cell:100">
            <div class="tier content:center" id="wpnb_send_area">
                <div class="cell:100">
                    <div class="wpnb-input-row">
                        <label for="wpnb_send_title">
                            <span class="wpnb-dashicon wpnb-txt-primary dashicons dashicons-heading"></span>
                            <?php _e('Notification title', 'wp-notif-bell'); ?>
                            <i class="wpnb-txt-danger">*</i>
                        </label>
                        <input value="<?php echo $_get_field_data('title'); ?>" class="wpnb-input-txt" type="text" id="wpnb_send_title" />
                    </div>
                </div>

                <div class="cell:100 cell-sm:50">
                    <div class="wpnb-input-row">
                        <label for="wpnb_send_sender">
                            <span class="wpnb-dashicon wpnb-txt-primary dashicons dashicons-admin-users"></span>
                            <?php _e('Notification sender', 'wp-notif-bell'); ?> <small>[a-z0-9-]</small> <i class="wpnb-txt-danger">*</i>
                        </label>
                        <input value="<?php echo !empty($_get_field_data('sender')) ? $_get_field_data('sender') : 'manual-' . get_current_user_id(); ?>" class="wpnb-input-txt" type="text" id="wpnb_send_sender" style="direction: ltr;" />
                    </div>

                    <div class="wpnb-input-row">
                        <label for="wpnb_send_tags">
                            <span class="wpnb-dashicon wpnb-txt-primary dashicons dashicons-tag"></span>
                            <?php _e('Notification tags', 'wp-notif-bell'); ?>
                            <small>[a-z0-9-]</small> <small class="wpnb-dt-tags-count wpnb-txt-danger">[<?php _e('Optional', 'wp-notif-bell'); ?>]</small>
                        </label>
                        <input value="<?php echo $_get_field_data('tags'); ?>" class="wpnb-input-txt" type="text" id="wpnb_send_tags" placeholder="<?php _e('Separate tags with commas (,)', 'wp-notif-bell'); ?>" style="direction: ltr;" />
                    </div>
                </div>

                <div class="cell:100 cell-sm:50">
                    <div class="wpnb-input-row">
                        <label for="wpnb_send_text_type">
                            <span class="wpnb-dashicon wpnb-txt-primary dashicons dashicons-editor-code"></span>
                            <?php _e('Text format', 'wp-notif-bell'); ?>
                            <i class="wpnb-txt-danger">*</i>
                        </label>
                        <select id="wpnb_send_text_type" class="wpnb-input-slc">
                            <?php foreach ($_txt_formats as $format => $name): ?>
                                <?php $_selected = $_get_field_data('format') === $format ? 'selected' : ''; ?>
                                <option value="<?php echo $format; ?>"<?php echo $_selected; ?>><?php echo $name; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="wpnb-input-row">
                        <label for="wpnb_send_date">
                            <span class="wpnb-dashicon wpnb-txt-primary dashicons dashicons-admin-users"></span>
                            <?php _e('Notification send date', 'wp-notif-bell'); ?> <small>(yyyy-mm-dd HH:ii:ss)</small> <small class="wpnb-dt-date-st wpnb-txt-danger"><?php _e('Empty: Auto fill', 'wp-notif-bell'); ?></small>
                        </label>
                        <input value="<?php echo $_get_field_data('sent_at'); ?>" class="wpnb-input-txt" type="text" id="wpnb_send_date" placeholder="Example: <?php echo Date::by_format('Y-m-d H:i:s'); ?> (<?php _e('Leave it blank for current time', 'wp-notif-bell'); ?>)" style="direction: ltr;" />
                    </div>
                </div>

                <div class="cell:100 wpnb-res-block">
                    <div class="tier content:center items:center">
                        <div class="cell:100">
                            <div class="wpnb-input-row">
                                <label for="wpnb_send_text">
                                    <span class="wpnb-dashicon wpnb-txt-primary dashicons dashicons-admin-comments"></span>
                                    <?php _e('Notification text', 'wp-notif-bell'); ?>
                                    <i class="wpnb-txt-danger">*</i>
                                </label>

                                <!-- main value -->
                                <input type="hidden" id="wpnb_text_content" value="<?php echo $_text_content; ?>" data-wpnb-visual-editor="<?php echo $_visual_text_editor; ?>" data-wpnb-editor="<?php echo $_text_editor; ?>" data-wpnb-editor-base="<?php echo $_editor_base; ?>">
                                <input type="hidden" id="wpnb_raw_text_content" value="<?php echo $_text_content; ?>">

                                <?php if ($_text_editor === 'auto'): ?>
                                    <?php if ($_editor_base === 'visual'): ?>
                                    <div id="wpnb_visual_editor">
                                        <?php if ($_visual_text_editor === 'wp'): ?>
                                            <?php wp_editor($_text_content, 'wpnb_mce_editor', ['media_buttons' => false]); ?>
                                        <?php else: ?>
                                            <style id="wpnb_inner_style_send_page">
                                                .ql-container .ql-editor *, .ql-container .ql-blank::before {
                                                    font-size: 16px;
                                                }

                                                .ql-container, .ql-toolbar {
                                                    border: 1px solid #8c8f94 !important;
                                                }

                                                .ql-toolbar, .ql-formats {
                                                    position: relative !important;
                                                    z-index: 999 !important;
                                                }
                                            </style>

                                            <div id="wpnb_quill_editor"></div>
                                        <?php endif; ?>
                                    </div>
                                    <?php else: ?>
                                        <div id="wpnb_code_editor">
                                            <div id="wpnb_ed_html_cm" style="direction: ltr !important;"></div>
                                        </div>
                                    <?php endif; ?>

                                    <style>
                                        .cm-editor {
                                            font-size: 12pt;
                                            border: 1px #8c8f94 solid;
                                        } 
                                    </style>    

                                    <!-- Markdown editor -->
                                    <div id="wpnb_area_cm_md" style="display: none;">
                                        <div id="wpnb_ed_md_cm"></div>
                                    </div>
                                <?php endif; ?>

                                <!-- simple/pure -->
                                <div id="wpnb_simple_editor" style="display: none;">
                                    <textarea id="wpnb_simple_text" rows="5" class="wpnb-txt-editor wpnb-input-txa" spellcheck="false" placeholder="<?php _e('Notif text ...', 'wp-notif-bell'); ?>"><?php echo $_text_content; ?></textarea>
                                </div>
                            </div>
                        </div>

                        <div class="cell:50">
                            <h4>Text Magic</h4>
                            <?php _e('You can use text magic to insert dynamic and variable text. Text magic is entered in the form of an open and closed bracket []. You can use registered variables or user-provided information.', 'wp-notif-bell'); ?>
                            <ul>
                                <li>
                                    <b style="color:red;">[variable]</b> <?php _e('get a variable data', 'wp-notif-bell'); ?>
                                </li>
                                <li>
                                    <b style="color:red;">[user:key]</b> <?php echo sprintf( __('get user data from %s', 'wp-notif-bell'), 'WP_User' ); ?>
                                </li>
                                <li>
                                    <b style="color:red;">[user-meta:name]</b> <?php _e('get user meta value', 'wp-notif-bell'); ?>
                                </li>
                                <li>
                                    <b style="color:red;">[opt:name]</b> <?php _e('get an option', 'wp-notif-bell'); ?>
                                </li>
                                <li>
                                    <b style="color:red;">[date:format|now]</b> <?php _e('get date', 'wp-notif-bell'); ?>
                                </li>
                                <li>
                                    <b style="color:red;">[date-i18n:format|now]</b> <?php _e('get i18n date', 'wp-notif-bell'); ?>
                                </li>
                                <li>
                                <b style="color:red;">[data:key]</b> <?php _e('get data value from `Notif Data`', 'wp-notif-bell'); ?>
                                </li>
                            </ul>
                        </div>
                        <div class="cell:50">
                            <h4><?php _e('Preview', 'wp-notif-bell'); ?> <small>(<?php _e('renew every 5 seconds', 'wp-notif-bell'); ?>)</small></h4>
                            <iframe class="wpnb-preview-fr" id="wpnb_preview_frame"></iframe>
                        </div>
                    </div>
                </div>

                <div class="cell:100 wpnb-res-block">
                    <div class="tier content:center items:center">
                        <div class="cell:30">
                            <h3 class="wpnb-top-title">
                                <span class="wpnb-dashicon wpnb-txt-primary dashicons dashicons-buddicons-pm"></span>
                                <?php _e('Receivers', 'wp-notif-bell'); ?>
                                <small class="wpnb-dt-res-count"></small>
                                <i class="wpnb-txt-danger">*</i>
                            </h3>
                            <span><?php _e('List of notification recipients', 'wp-notif-bell'); ?></span>
                        </div>

                        <div class="cell:30">
                            <div class="wpnb-input-row">
                                <select id="wpnb_send_res_form_name_slc" class="wpnb-input-slc">
                                    <optgroup label="<?php _e('Defaults', 'wp-notif-bell'); ?>">
                                        <option value="user-id" data-wpnb-text="<?php _e("The user's ID (exa: 3724) [int]", 'wp-notif-bell'); ?>" selected><?php _e('User ID (User unique ID)', 'wp-notif-bell'); ?></option>
                                        <option value="user-name" data-wpnb-text="<?php _e("The user's Login (exa: nova) [str]", 'wp-notif-bell'); ?>"><?php _e('User Name (User Login)', 'wp-notif-bell'); ?></option>
                                        <option value="user-mail" data-wpnb-text="<?php _e("The user's Mail (exa: h@my.com) [e-mail]", 'wp-notif-bell'); ?>"><?php _e('User Mail (Email address)', 'wp-notif-bell'); ?></option>
                                        <option value="user-role" data-wpnb-text="<?php _e("The user's Role [wp-role]", 'wp-notif-bell'); ?>"><?php _e('User Role (Roles and Capabilities)', 'wp-notif-bell'); ?></option>
                                        <option value="command" data-wpnb-text="<?php _e("The command name (exa: all) [str]", 'wp-notif-bell'); ?>"><?php _e('Command (An order for groups)', 'wp-notif-bell'); ?></option>
                                    </optgroup>

                                    <?php if (!empty(Statics::$rec_list)): ?>
                                    <optgroup label="<?php _e('Also', 'wp-notif-bell'); ?>">
                                        <?php wpnb_render_rec_list(); ?>
                                    </optgroup>
                                    <?php endif; ?>

                                    <?php if (!empty(Statics::$eza_list)): ?>
                                    <optgroup label="<?php _e('Easy access', 'wp-notif-bell'); ?>">
                                        <?php wpnb_render_eza_list(); ?>
                                    </optgroup>
                                    <?php endif; ?>

                                    <option value="custom" data-wpnb-text="<?php _e("An arbitrary value ...", 'wp-notif-bell'); ?>"><?php _e('Custom', 'wp-notif-bell'); ?> *</option>
                                </select>
                                
                                <input style="display: none;" class="wpnb-input-txt" type="text" id="wpnb_send_res_form_name_str" placeholder="<?php _e('Custom name (Double click to return)', 'wp-notif-bell'); ?>" />
                            </div>
                        </div>

                        <div class="cell:30">
                            <div class="wpnb-input-row">
                                <input class="wpnb-input-txt" type="text" id="wpnb_send_res_form_data" />

                                <!-- Roles -->
                                <datalist id="wpnb_send_usr_roles">
                                    <?php foreach ($_usr_roles as $role): ?>
                                        <option value="<?php echo $role; ?>">
                                    <?php endforeach; ?>
                                </datalist>  
                            </div>
                        </div>

                        <div class="cell:10">
                            <button class="button button-primary" id="wpnb_res_add_btn">
                            <?php _e('Add', 'wp-notif-bell'); ?>
                            </button>
                        </div>
                    </div>

                    <div class="wpnb-w-100 wpnb-space-top-2" id="wpnb_res_alert_none">
                        <div class="wpnb-no-value-block">
                            <span class="wpnb-title wpnb-red">
                                <?php _e('No recipient!', 'wp-notif-bell'); ?>
                            </span>
                            <small class="wpnb-txt">
                                <?php _e('Use "Add" button to add some', 'wp-notif-bell'); ?>
                            </small>
                        </div>
                    </div>

                    <div class="tier content:center wpnb-space-top-2" id="wpnb_receivers_area" data-wpnb-value="<?php echo $_get_field_data('recipients', 'string'); ?>">
                        <!-- Auto fill by JS -->
                    </div>

                </div>

                <div class="cell:100">
                    <button class="button button-primary" id="wpnb_act_send" data-wpnb-action="<?php echo $_is_edit ? 'edit' : 'send' ; ?>">
                        <?php $_is_edit ? _e('Edit notification', 'wp-notif-bell') : _e('Send notification', 'wp-notif-bell'); ?>
                    </button>

                    <div id="wpnb_response_area"></div>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>
