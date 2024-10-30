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
    $_txt_formats['pure-text'] .= ' (' . __('recommanded', 'notif-bell') . ')';
}

// check for edit action
$_is_edit = isset($_GET['action']) && isset($_GET['key']) && $_GET['action'] === 'edit';
$_key     = sanitize_key($_GET['key'] ?? '');

// get target notif if edit enabled
if ($_is_edit) {
    $_collector = (new Collector)
        ->config([ 'use_textmagic' => false ])
        ->target_by_key($_key);

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
 * @return  mixed
 */
$_get_field_data = function (string $key, string $type = 'string') use ($notif)
{
    if (is_null($notif) || !array_key_exists($key, $notif)) {
        $value  = null;
    } else {
        $value  = $notif[$key];
    }

    // sanitize data: `content` is a html base data
    if ($key !== 'content') {
        $value = sanitize_text_field($value);
    }

    if (gettype($value) !== $type) {
        settype($value, $type);
    }

    return $value;
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
            🔔 <?php esc_html_e('WP Notif Bell', 'notif-bell'); ?>
            <span class="wpnb-ad-header-tab rnd org"><?php $_is_edit ? esc_html_e('Edit', 'notif-bell') : esc_html_e('Send', 'notif-bell'); ?></span>
        </h3>

        <p class="wpnb-ad-header-text">
            <?php $_is_edit ? esc_html_e('Editing notification', 'notif-bell') : esc_html_e('Sending notification', 'notif-bell'); ?>
        </p>
    </div>

    <?php if ($_key): ?>
        <input type="hidden" id="wpnb_edit_key" value="<?php echo esc_attr($_key); ?>">
    <?php else: ?>
        <input type="hidden" id="wpnb_edit_url" value="<?php echo esc_attr($_edit_url); ?>">
    <?php endif; ?>

    <?php if (is_null($notif) && $_is_edit): ?>
    <div class="wpnb-w-100">
        <?php esc_html_e('There is no notification with the key you entered.', 'notif-bell'); ?>
        <a href="<?php menu_page_url('wpnb-list'); ?>"><?php esc_html_e('Back to list', 'notif-bell'); ?></a>
    </div>
    <?php else: ?>
    <div class="wpnb-ad-l-box">
        <div class="pack:no-gutters cell:100">
            <div class="tier content:center" id="wpnb_send_area">
                <div class="cell:100">
                    <div class="wpnb-input-row">
                        <label for="wpnb_send_title">
                            <span class="wpnb-dashicon wpnb-txt-primary dashicons dashicons-heading"></span>
                            <?php esc_html_e('Notification title', 'notif-bell'); ?>
                            <i class="wpnb-txt-danger">*</i>
                        </label>
                        <input value="<?php echo esc_attr($_get_field_data('title')); ?>" class="wpnb-input-txt" type="text" id="wpnb_send_title" />
                    </div>
                </div>

                <div class="cell:100 cell-sm:50">
                    <div class="wpnb-input-row">
                        <label for="wpnb_send_sender">
                            <span class="wpnb-dashicon wpnb-txt-primary dashicons dashicons-admin-users"></span>
                            <?php esc_html_e('Notification sender', 'notif-bell'); ?> <small>[a-z0-9-]</small> <i class="wpnb-txt-danger">*</i>
                        </label>
                        <input value="<?php echo esc_attr( !empty($_get_field_data('sender')) ? $_get_field_data('sender') : 'manual-' . get_current_user_id() ); ?>" class="wpnb-input-txt" type="text" id="wpnb_send_sender" style="direction: ltr;" />
                    </div>

                    <div class="wpnb-input-row">
                        <label for="wpnb_send_tags">
                            <span class="wpnb-dashicon wpnb-txt-primary dashicons dashicons-tag"></span>
                            <?php esc_html_e('Notification tags', 'notif-bell'); ?>
                            <small>[a-z0-9-]</small> <small class="wpnb-dt-tags-count wpnb-txt-danger">[<?php esc_html_e('Optional', 'notif-bell'); ?>]</small>
                        </label>
                        <input value="<?php echo esc_attr($_get_field_data('tags')); ?>" class="wpnb-input-txt" type="text" id="wpnb_send_tags" placeholder="<?php esc_attr_e('Separate tags with commas (,)', 'notif-bell'); ?>" style="direction: ltr;" />
                    </div>
                </div>

                <div class="cell:100 cell-sm:50">
                    <div class="wpnb-input-row">
                        <label for="wpnb_send_text_type">
                            <span class="wpnb-dashicon wpnb-txt-primary dashicons dashicons-editor-code"></span>
                            <?php esc_html_e('Text format', 'notif-bell'); ?>
                            <i class="wpnb-txt-danger">*</i>
                        </label>
                        <select id="wpnb_send_text_type" class="wpnb-input-slc">
                            <?php foreach ($_txt_formats as $format => $name): ?>
                                <?php $_selected = $_get_field_data('format') === $format ? 'selected' : ''; ?>
                                <option value="<?php echo esc_attr($format); ?>"<?php echo esc_attr($_selected); ?>><?php echo esc_html($name); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="wpnb-input-row">
                        <label for="wpnb_send_date">
                            <span class="wpnb-dashicon wpnb-txt-primary dashicons dashicons-admin-users"></span>
                            <?php esc_html_e('Notification send date', 'notif-bell'); ?> <small>(yyyy-mm-dd HH:ii:ss)</small> <small class="wpnb-dt-date-st wpnb-txt-danger"><?php esc_html_e('Empty: Auto fill', 'notif-bell'); ?></small>
                        </label>
                        <input value="<?php echo esc_attr($_get_field_data('sent_at')); ?>" class="wpnb-input-txt" type="text" id="wpnb_send_date" placeholder="Example: <?php echo esc_html( Date::by_format('Y-m-d H:i:s') ); ?> (<?php esc_html_e('Leave it blank for current time', 'notif-bell'); ?>)" style="direction: ltr;" />
                    </div>
                </div>

                <div class="cell:100 wpnb-res-block">
                    <div class="tier content:center items:center">
                        <div class="cell:100">
                            <div class="wpnb-input-row">
                                <label for="wpnb_send_text">
                                    <span class="wpnb-dashicon wpnb-txt-primary dashicons dashicons-admin-comments"></span>
                                    <?php esc_html_e('Notification text', 'notif-bell'); ?>
                                    <i class="wpnb-txt-danger">*</i>
                                </label>

                                <!-- main value -->
                                <input type="hidden" id="wpnb_text_content" value="<?php echo esc_attr($_text_content); ?>" data-wpnb-visual-editor="<?php echo esc_attr($_visual_text_editor); ?>" data-wpnb-editor="<?php echo esc_attr($_text_editor); ?>" data-wpnb-editor-base="<?php echo esc_attr($_editor_base); ?>">
                                <input type="hidden" id="wpnb_raw_text_content" value="<?php echo esc_attr($_text_content); ?>">

                                <?php if ($_text_editor === 'auto'): ?>
                                    <?php if ($_editor_base === 'visual'): ?>
                                    <div id="wpnb_visual_editor">
                                        <?php if ($_visual_text_editor === 'wp'): ?>
                                            <?php wp_editor($_text_content, 'wpnb_mce_editor', ['media_buttons' => false]); ?>
                                        <?php else: ?>
                                            <div id="wpnb_quill_editor"></div>
                                        <?php endif; ?>
                                    </div>
                                    <?php else: ?>
                                        <div id="wpnb_code_editor">
                                            <div id="wpnb_ed_html_cm" style="direction: ltr !important;"></div>
                                        </div>
                                    <?php endif; ?> 

                                    <!-- Markdown editor -->
                                    <div id="wpnb_area_cm_md" style="display: none;">
                                        <div id="wpnb_ed_md_cm"></div>
                                    </div>
                                <?php endif; ?>

                                <!-- simple/pure -->
                                <div id="wpnb_simple_editor" style="display: none;">
                                    <textarea id="wpnb_simple_text" rows="5" class="wpnb-txt-editor wpnb-input-txa" spellcheck="false" placeholder="<?php esc_attr_e('Notif text ...', 'notif-bell'); ?>"><?php echo esc_html($_text_content); ?></textarea>
                                </div>
                            </div>
                        </div>

                        <div class="cell:100 cell-sm:50">
                            <h4>Text Magic</h4>
                            <?php esc_html_e('You can use text magic to insert dynamic and variable text. Text magic is entered in the form of an open and closed bracket []. You can use registered variables or user-provided information.', 'notif-bell'); ?>
                            <ul>
                                <li>
                                    <b style="color:red;">[variable]</b> <?php esc_html_e('get a variable data', 'notif-bell'); ?>
                                </li>
                                <li>
                                    <b style="color:red;">[user:key]</b> <?php /* translators: %s: class name */ echo esc_html( sprintf( __('get user data from %s', 'notif-bell'), 'WP_User' ) ); ?>
                                </li>
                                <li>
                                    <b style="color:red;">[user-meta:name]</b> <?php esc_html_e('get user meta value', 'notif-bell'); ?>
                                </li>
                                <li>
                                    <b style="color:red;">[opt:name]</b> <?php esc_html_e('get an option', 'notif-bell'); ?>
                                </li>
                                <li>
                                    <b style="color:red;">[date:format|now]</b> <?php esc_html_e('get date', 'notif-bell'); ?>
                                </li>
                                <li>
                                    <b style="color:red;">[date-i18n:format|now]</b> <?php esc_html_e('get i18n date', 'notif-bell'); ?>
                                </li>
                                <li>
                                <b style="color:red;">[data:key]</b> <?php esc_html_e('get data value from `Notif Data`', 'notif-bell'); ?>
                                </li>
                            </ul>
                        </div>
                        <div class="cell:100 cell-sm:50">
                            <h4><?php esc_html_e('Preview', 'notif-bell'); ?> <small>(<?php esc_html_e('renew every 5 seconds', 'notif-bell'); ?>)</small></h4>
                            <iframe class="wpnb-preview-fr" id="wpnb_preview_frame"></iframe>
                        </div>
                    </div>
                </div>

                <div class="cell:100 wpnb-res-block">
                    <div class="tier content:center items:center">
                        <div class="cell:100 cell-sm:30">
                            <h3 class="wpnb-top-title">
                                <span class="wpnb-dashicon wpnb-txt-primary dashicons dashicons-buddicons-pm"></span>
                                <?php esc_html_e('Receivers', 'notif-bell'); ?>
                                <small class="wpnb-dt-res-count"></small>
                                <i class="wpnb-txt-danger">*</i>
                            </h3>
                            <span><?php esc_html_e('List of notification recipients', 'notif-bell'); ?></span>
                        </div>

                        <div class="cell:100 cell-sm:30">
                            <div class="wpnb-input-row">
                                <select id="wpnb_send_res_form_name_slc" class="wpnb-input-slc">
                                    <optgroup label="<?php esc_attr_e('Defaults', 'notif-bell'); ?>">
                                        <option value="user-id" data-wpnb-text="<?php esc_attr_e("The user's ID (exa: 3724) [int]", 'notif-bell'); ?>" selected><?php esc_attr_e('User ID (User unique ID)', 'notif-bell'); ?></option>
                                        <option value="user-name" data-wpnb-text="<?php esc_attr_e("The user's Login (exa: nova) [str]", 'notif-bell'); ?>"><?php esc_attr_e('User Name (User Login)', 'notif-bell'); ?></option>
                                        <option value="user-mail" data-wpnb-text="<?php esc_attr_e("The user's Mail (exa: h@my.com) [e-mail]", 'notif-bell'); ?>"><?php esc_attr_e('User Mail (Email address)', 'notif-bell'); ?></option>
                                        <option value="user-role" data-wpnb-text="<?php esc_attr_e("The user's Role [wp-role]", 'notif-bell'); ?>"><?php esc_attr_e('User Role (Roles and Capabilities)', 'notif-bell'); ?></option>
                                        <option value="command" data-wpnb-text="<?php esc_attr_e("The command name (exa: all) [str]", 'notif-bell'); ?>"><?php esc_attr_e('Command (An order for groups)', 'notif-bell'); ?></option>
                                    </optgroup>

                                    <?php if (!empty(Statics::$rec_list)): ?>
                                    <optgroup label="<?php esc_attr_e('Also', 'notif-bell'); ?>">
                                        <?php wpnb_render_rec_list(); ?>
                                    </optgroup>
                                    <?php endif; ?>

                                    <?php if (!empty(Statics::$eza_list)): ?>
                                    <optgroup label="<?php esc_attr_e('Easy access', 'notif-bell'); ?>">
                                        <?php wpnb_render_eza_list(); ?>
                                    </optgroup>
                                    <?php endif; ?>

                                    <option value="custom" data-wpnb-text="<?php esc_attr_e("An arbitrary value ...", 'notif-bell'); ?>"><?php esc_html_e('Custom', 'notif-bell'); ?> *</option>
                                </select>
                                
                                <input style="display: none;" class="wpnb-input-txt" type="text" id="wpnb_send_res_form_name_str" placeholder="<?php esc_attr_e('Custom name (Double click to return)', 'notif-bell'); ?>" />
                            </div>
                        </div>

                        <div class="cell:100 cell-sm:30">
                            <div class="wpnb-input-row">
                                <input class="wpnb-input-txt" type="text" id="wpnb_send_res_form_data" />

                                <!-- Roles -->
                                <datalist id="wpnb_send_usr_roles">
                                    <?php foreach ($_usr_roles as $role): ?>
                                        <option value="<?php echo esc_html($role); ?>">
                                    <?php endforeach; ?>
                                </datalist>  
                            </div>
                        </div>

                        <div class="cell:100 cell-sm:10">
                            <button class="button button-primary" id="wpnb_res_add_btn">
                            <?php esc_html_e('Add', 'notif-bell'); ?>
                            </button>
                        </div>
                    </div>

                    <div class="wpnb-w-100 wpnb-space-top-2" id="wpnb_res_alert_none">
                        <div class="wpnb-no-value-block">
                            <span class="wpnb-title wpnb-red">
                                <?php esc_html_e('No recipient!', 'notif-bell'); ?>
                            </span>
                            <small class="wpnb-txt">
                                <?php esc_html_e('Use "Add" button to add some', 'notif-bell'); ?>
                            </small>
                        </div>
                    </div>

                    <div class="tier content:center wpnb-space-top-2" id="wpnb_receivers_area" data-wpnb-value="<?php echo esc_attr($_get_field_data('recipients', 'string')); ?>">
                        <!-- Auto fill by JS -->
                    </div>

                </div>

                <div class="cell:100">
                    <button class="button button-primary" id="wpnb_act_send" data-wpnb-action="<?php echo $_is_edit ? 'edit' : 'send' ; ?>">
                        <?php $_is_edit ? esc_html_e('Edit notification', 'notif-bell') : esc_html_e('Send notification', 'notif-bell'); ?>
                    </button>

                    <div id="wpnb_response_area"></div>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>
