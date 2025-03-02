<?php
/**
 * Partial: admin tools page
 * admin menu content file
 * 
 * @since    0.9.0
 */

// If this file is called directly, abort.
defined('WPINC') || die;

use Irmmr\WpNotifBell\Admin\Utils\Notice;
use Irmmr\WpNotifBell\Db;
use Irmmr\WpNotifBell\Helpers\Esc;
use Irmmr\WpNotifBell\Helpers\Notif;
use Irmmr\WpNotifBell\Interfaces\UserInterface;
use Irmmr\WpNotifBell\User\Eye;

/**
 * create tab url for tab btn
 *
 * @since   0.9.0
 * @param   string  $tab
 * @return  string
 */
function mm8x_make_tab_url(string $tab): string
{
    return add_query_arg('tab', $tab, remove_query_arg(['wpnb-process', 'nonce']));
}

// page notice messages
$_messages = [];

// active tab
$_tab = sanitize_key($_GET['tab'] ?? 'db-update');

// list of tabs
$_tabs = [
    'db-update' => [
        'name' => __('Database Updater', 'notif-bell'),
    ],
    'reset' => [
        'name' => __('Reset', 'notif-bell'),
    ],
];

// after process
$_after_process = false;

// handle process with nonce
if (isset($_GET['wpnb-process']) && isset($_GET['nonce'])) {
    $_process = sanitize_key($_GET['wpnb-process']);
    $_nonce   = sanitize_key($_GET['nonce']);

    if (wp_verify_nonce($_nonce, 'prc:' . $_process)) {
        if ($_process === 'up-db' && !Db::is_last_version()) {
            Db::update_tables();

            $_messages[] = Notice::render(__('The database has been successfully updated.', 'notif-bell'), Notice::SUCCESS);
            $_after_process = true;

        } elseif ($_process === 'reset') {
            global $wpdb;

            // reset seen data
            $key_list   = sanitize_key( UserInterface::SEEN_META_KEY );
            $key_method = sanitize_key( UserInterface::SEEN_METHOD_KEY );
            $notifs_tb  = Db::get_table_name('notifs');

            if (empty($key_list) || empty($key_method) || empty($notifs_tb)) {
                $_messages[] = Notice::render(__('An issue has occurred! Please try again.', 'notif-bell'), Notice::ERROR);

            } elseif (Notif::get_max_id() === 0) {
                $_messages[] = Notice::render(__('There is absolutely nothing to reset!', 'notif-bell'), Notice::WARN);

            } else {
                $def_method = sanitize_key( strval( Eye::BY_COMMA ) );
                $wpdb->query("UPDATE {$wpdb->usermeta} SET `meta_value` = '' WHERE `meta_key` = '{$key_list}';");
                $wpdb->query("UPDATE {$wpdb->usermeta} SET `meta_value` = '{$def_method}' WHERE `meta_key` = '{$key_method}';");

                // reset notifications
                $wpdb->query("TRUNCATE {$notifs_tb};");

                $_messages[] = Notice::render(__('Everything has been successfully reset.', 'notif-bell'), Notice::SUCCESS);
                $_after_process = true;
            }
        }
    } else {
        $_messages[] = Notice::render(__('The submitted request is not valid.', 'notif-bell'), Notice::ERROR);
    }
}

?>

<div class="wpnb-ad-box">
    <div class="wpnb-ad-header">
        <h3 class="wpnb-ad-header-title">
            🔔 <?php esc_html_e('WP Notif Bell', 'notif-bell'); ?>
            <span class="wpnb-ad-header-tab rnd org"><?php esc_html_e('Tools', 'notif-bell'); ?></span>
        </h3>

        <p class="wpnb-ad-header-text">
            <?php esc_html_e('Tools needed', 'notif-bell'); ?>
        </p>
    </div>

    <?php foreach ($_messages as $_msg): ?>
        <?php echo wp_kses( $_msg, Esc::get_allowed_html_notice() ); ?>
    <?php endforeach; ?>

    <div class="wpnb-w-100" style="line-height: 1.7rem;font-size: 1.0rem;">
        <h2 class="nav-tab-wrapper" style="border-bottom: 0; margin-bottom: -21px; padding-left: 10px; padding-right: 10px; ">
            <?php foreach ($_tabs as $tb => $data): ?>
                <a href="<?php echo esc_url( mm8x_make_tab_url($tb) ); ?>" class="nav-tab<?php echo $tb === $_tab ? ' nav-tab-active' : ''; ?>"><?php echo esc_html($data['name']); ?></a>
            <?php endforeach; ?>
        </h2>

        <?php if ($_tab === 'db-update'): ?>
        <div class="tab-content wpnb-ad-l-box wpnb-mx-0">
            <p>
                <?php esc_html_e('This tool helps you keep the tables and information related to this plugin up to date.', 'notif-bell'); ?>
            </p>
            <?php esc_html_e('Last version', 'notif-bell'); ?>: <b class="wpnb-txt-primary"><?php echo esc_html(Db::LATEST_VERSION); ?></b> <br />
            <?php esc_html_e('Your version', 'notif-bell'); ?>: <b><?php echo esc_html(Db::get_version()); ?></b> <br />

            <?php if (Db::is_last_version()): ?>
                <span class="wpnb-txt-success"><?php esc_html_e('The database is up to date.', 'notif-bell'); ?></span>
            <?php else: ?>
                <span class="wpnb-txt-danger"><?php esc_html_e('The database needs to be updated.', 'notif-bell'); ?></span>

                <?php if (!$_after_process): ?>
                    <a href="<?php echo esc_url( add_query_arg([ 'wpnb-process' => 'up-db', 'nonce' => wp_create_nonce('prc:up-db') ]) ); ?>"><?php esc_html_e('Update', 'notif-bell'); ?></a>
                <?php endif; ?>
            <?php endif; ?>
        </div>
        <?php elseif ($_tab === 'reset'): ?>
        <div class="tab-content wpnb-ad-l-box wpnb-mx-0">
            <p>
                <?php esc_html_e('This tool helps you reset all notification data and the user\'s seen status. It is recommended to start everything over after long-term use and a very high number of notifications (over 10,000).', 'notif-bell'); ?>
            </p>
            <?php $max_id = Notif::get_max_id(); ?>
            <?php esc_html_e('MAX ID', 'notif-bell'); ?>: <b class="wpnb-txt-primary"><?php echo esc_html( $max_id ); ?></b> <br />
            <?php esc_html_e('Notifs count', 'notif-bell'); ?>: <b class="wpnb-txt-primary"><?php echo esc_html( wpnb_collector()->get_count() ); ?></b> <br />

            <?php if ($max_id < 500*1000): ?>
                <span class="wpnb-txt-success"><?php esc_html_e('Everything looks good.', 'notif-bell'); ?></span>
            <?php else: ?>
                <span class="wpnb-txt-danger"><?php esc_html_e('It\'s better for the data to be reset.', 'notif-bell'); ?></span>
            <?php endif; ?>
            <br />
            <span class="wpnb-txt-danger"><?php esc_html_e('Please note: This action will delete all stored data related to notifications.', 'notif-bell'); ?></span>
            <?php if (!$_after_process): ?>
                <a href="<?php echo esc_url( add_query_arg([ 'wpnb-process' => 'reset', 'nonce' => wp_create_nonce('prc:reset') ]) ); ?>"><?php esc_html_e('Reset', 'notif-bell'); ?></a>
            <?php endif; ?>
        </div>
        <?php else: ?>
        <div class="tab-content wpnb-ad-l-box wpnb-mx-0">
            <?php esc_html_e('The desired tab does not exist.', 'notif-bell'); ?>
        </div>
        <?php endif; ?>
    </div>

</div>
