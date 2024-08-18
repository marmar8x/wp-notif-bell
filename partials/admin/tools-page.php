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

/**
 * create tab url for tab btn
 * 
 * @param   string  $tab
 * @return  string
 */
function _make_tab_url(string $tab): string
{
    return add_query_arg('tab', $tab);
}

// page notice messages
$_messages = [];

// active tab
$_tab = $_GET['tab'] ?? 'db-update';

// list of tabs
$_tabs = [
    'db-update' => [
        'name' => __('Database Updater', 'wp-notif-bell'),
    ]
];

// handle process with nonce
if (isset($_GET['wpnb-process']) && isset($_GET['nonce'])) {
    $_process = $_GET['wpnb-process'];
    $_nonce   = $_GET['nonce'];

    if (wp_verify_nonce($_nonce, 'prc:' . $_process)) {
        if ($_process === 'up-db' && !Db::is_last_version()) {
            Db::update_tables();

            $_messages[] = Notice::render(__('The database has been successfully updated.', 'wp-notif-bell'), Notice::SUCCESS);
        }
    } else {
        $_messages[] = Notice::render(__('The submitted request is not valid.', 'wp-notif-bell'), Notice::ERROR);
    }
}

?>

<div class="wpnb-ad-box">
    <div class="wpnb-ad-header">
        <h3 class="wpnb-ad-header-title">
            🔔 <?php esc_html_e('WP Notif Bell', 'wp-notif-bell'); ?>
            <span class="wpnb-ad-header-tab rnd org"><?php esc_html_e('Tools', 'wp-notif-bell'); ?></span>
        </h3>

        <p class="wpnb-ad-header-text">
            <?php esc_html_e('Tools needed', 'wp-notif-bell'); ?>
        </p>
    </div>

    <?php foreach ($_messages as $_msg): ?>
        <?php echo $_msg; ?>
    <?php endforeach; ?>

    <div class="wpnb-w-100" style="line-height: 1.7rem;font-size: 1.0rem;">
        <h2 class="nav-tab-wrapper" style="border-bottom: 0; margin-bottom: -21px; padding-left: 10px; padding-right: 10px; ">
            <?php foreach ($_tabs as $tb => $data): ?>
                <a href="<?php echo esc_url(_make_tab_url($tb)); ?>" class="nav-tab<?php echo $tb === $_tab ? ' nav-tab-active' : ''; ?>"><?php echo esc_html($data['name']); ?></a>
            <?php endforeach; ?>
        </h2>

        <?php if ($_tab === 'db-update'): ?>
        <div class="tab-content wpnb-ad-l-box wpnb-mx-0">
            <p>
                <?php esc_html_e('This tool helps you keep the tables and information related to this plugin up to date.', 'wp-notif-bell'); ?>
            </p>
            <?php esc_html_e('Last version', 'wp-notif-bell'); ?>: <b class="wpnb-txt-primary"><?php echo esc_html(Db::LATEST_VERSION); ?></b> <br />
            <?php esc_html_e('Your version', 'wp-notif-bell'); ?>: <b><?php echo esc_html(Db::get_version()); ?></b> <br />

            <?php if (Db::is_last_version()): ?>
                <span class="wpnb-txt-success"><?php esc_html_e('The database is up to date.', 'wp-notif-bell'); ?></span>
            <?php else: ?>
                <span class="wpnb-txt-danger"><?php esc_html_e('The database needs to be updated.', 'wp-notif-bell'); ?></span>
                <a href="<?php echo esc_url( add_query_arg([ 'wpnb-process' => 'up-db', 'nonce' => wp_create_nonce('prc:up-db') ]) ); ?>"><?php esc_html_e('Update', 'wp-notif-bell'); ?></a>
            <?php endif; ?>
        </div>
        <?php else: ?>
        <div class="tab-content wpnb-ad-l-box wpnb-mx-0">
            <?php esc_html_e('The desired tab does not exist.', 'wp-notif-bell'); ?>
        </div>
        <?php endif; ?>
    </div>

</div>
