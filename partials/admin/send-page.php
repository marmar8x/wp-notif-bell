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

?>

<div class="wpnb-ad-box">
    <div class="wpnb-ad-header">
        <h3 class="wpnb-ad-header-title">
            🔔 WP Notif Bell
            <span class="wpnb-ad-header-tab rnd org"><?php echo $_is_edit ? __('Edit', 'wp-notif-bell') : __('Send', 'wp-notif-bell'); ?></span>
        </h3>

        <p class="wpnb-ad-header-text">
            <?php echo $_is_edit ? __('Editing notification', 'wp-notif-bell') : __('Sending notification', 'wp-notif-bell'); ?>
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
                            Notification title
                            <i class="wpnb-txt-danger">*</i>
                        </label>
                        <input value="<?php echo $_get_field_data('title'); ?>" class="wpnb-input-txt" type="text" id="wpnb_send_title" />
                    </div>
                </div>

                <div class="cell:100">
                    <div class="wpnb-input-row">
                        <label for="wpnb_send_text">
                            <span class="wpnb-dashicon wpnb-txt-primary dashicons dashicons-admin-comments"></span>
                            Notification text
                            <small class="wpnb-txt-primary">[using TextMagic]</small>
                            <i class="wpnb-txt-danger">*</i>
                        </label>
                        <textarea id="wpnb_send_text" rows="5" class="wpnb-input-txa"><?php echo $_get_field_data('content'); ?></textarea>
                    </div>
                </div>

                <div class="cell:100 cell-sm:50">
                    <div class="wpnb-input-row">
                        <label for="wpnb_send_sender">
                            <span class="wpnb-dashicon wpnb-txt-primary dashicons dashicons-admin-users"></span>
                            Notification sender <small>[a-z0-9-]</small> <i class="wpnb-txt-danger">*</i>
                        </label>
                        <input value="<?php echo !empty($_get_field_data('sender')) ? $_get_field_data('sender') : 'manual-' . get_current_user_id(); ?>" class="wpnb-input-txt" type="text" id="wpnb_send_sender" />
                    </div>

                    <div class="wpnb-input-row">
                        <label for="wpnb_send_tags">
                            <span class="wpnb-dashicon wpnb-txt-primary dashicons dashicons-tag"></span>
                            Notification tags
                            <small>[a-z0-9-]</small> <small class="wpnb-dt-tags-count wpnb-txt-danger">[Optional]</small>
                        </label>
                        <input value="<?php echo $_get_field_data('tags'); ?>" class="wpnb-input-txt" type="text" id="wpnb_send_tags" placeholder="Separate tags with commas (,)" />
                    </div>
                </div>

                <div class="cell:100 cell-sm:50">
                    <div class="wpnb-input-row">
                        <label for="wpnb_send_text_type">
                            <span class="wpnb-dashicon wpnb-txt-primary dashicons dashicons-editor-code"></span>
                            Text format
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
                            Notification send date <small>(yyyy-mm-dd HH:ii:ss)</small> <small class="wpnb-dt-date-st wpnb-txt-danger">Empty: Auto fill</small>
                        </label>
                        <input value="<?php echo $_get_field_data('sent_at'); ?>" class="wpnb-input-txt" type="text" id="wpnb_send_date" placeholder="Example: <?= Date::by_format('Y-m-d H:i:s') ?> (Leave it blank for current time)" />
                    </div>
                </div>

                <div class="cell:100 wpnb-res-block">
                    <div class="tier content:center items:center">
                        <div class="cell:30">
                            <h3 class="wpnb-top-title">
                                <span class="wpnb-dashicon wpnb-txt-primary dashicons dashicons-buddicons-pm"></span>
                                Receivers
                                <small class="wpnb-dt-res-count"></small>
                                <i class="wpnb-txt-danger">*</i>
                            </h3>
                            <span>Bunch of group of people that recieve</span>
                        </div>

                        <div class="cell:30">
                            <div class="wpnb-input-row">
                                <select id="wpnb_send_res_form_name_slc" class="wpnb-input-slc">
                                    <optgroup label="Defaults">
                                        <option value="user-id" data-wpnb-text="<?php _e("The user's ID (exa: 3724) [int]", 'wp-notif-bell'); ?>" selected>User ID (User unique ID)</option>
                                        <option value="user-name" data-wpnb-text="<?php _e("The user's Login (exa: nova) [str]", 'wp-notif-bell'); ?>">User Name (User Login)</option>
                                        <option value="user-mail" data-wpnb-text="<?php _e("The user's Mail (exa: h@my.com) [e-mail]", 'wp-notif-bell'); ?>">User Mail (Email address)</option>
                                        <option value="user-role" data-wpnb-text="<?php _e("The user's Role [wp-role]", 'wp-notif-bell'); ?>">User Role (Roles and Capabilities)</option>
                                        <option value="command" data-wpnb-text="<?php _e("The command name (exa: all) [str]", 'wp-notif-bell'); ?>">Command (An order for groups)</option>
                                    </optgroup>

                                    <?php if (!empty(Statics::$rec_list)): ?>
                                    <optgroup label="Also">
                                        <?php wpnb_render_rec_list(); ?>
                                    </optgroup>
                                    <?php endif; ?>

                                    <?php if (!empty(Statics::$eza_list)): ?>
                                    <optgroup label="Easy access">
                                        <?php wpnb_render_eza_list(); ?>
                                    </optgroup>
                                    <?php endif; ?>

                                    <option value="custom" data-wpnb-text="<?php _e("An arbitrary value ...", 'wp-notif-bell'); ?>">Custom *</option>
                                </select>
                                
                                <input style="display: none;" class="wpnb-input-txt" type="text" id="wpnb_send_res_form_name_str" placeholder="Custom name (Double click to return)" />
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
                                Add
                            </button>
                        </div>
                    </div>

                    <div class="wpnb-w-100 wpnb-space-top-2" id="wpnb_res_alert_none">
                        <div class="wpnb-no-value-block">
                            <span class="wpnb-title wpnb-red">
                                Without any receiver, sending to hell!
                            </span>
                            <small class="wpnb-txt">
                                Use "Add" button to add some
                            </small>
                        </div>
                    </div>

                    <div class="tier content:center wpnb-space-top-2" id="wpnb_receivers_area" data-wpnb-value="<?php echo $_get_field_data('recipients', 'string'); ?>">
                        <!-- Auto fill by JS -->
                    </div>

                </div>

                <div class="cell:100">
                    <button class="button button-primary" id="wpnb_act_send" data-wpnb-action="<?php echo $_is_edit ? 'edit' : 'send' ; ?>">
                        <?php echo $_is_edit ? 'Edit notification' : 'Send notification'; ?>
                    </button>

                    <div id="wpnb_response_area"></div>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>
