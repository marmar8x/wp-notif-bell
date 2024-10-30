<?php
/**
 * Settings file
 * define some functions for handle settings form and fields
 * 
 * @since   0.9.0
 */

// If this file is called directly, abort.
defined('WPINC') || die;

use Irmmr\WpNotifBell\Helpers\Element;
use Irmmr\WpNotifBell\Helpers\Esc;
use Irmmr\WpNotifBell\Settings;

/**
 * @global
 * @since   0.9.0
 */
$wpnb_settings_sections = [];

/**
 * @global
 * @since   0.9.0
 */
$wpnb_settings_tabs = [];

/**
 * @global
 * @since   0.9.0
 */
$wpnb_settings_fields = [];

/**
 * @global
 * @since   0.9.0
 */
$wpnb_settings_msg    = [];

/**
 * [Admin]
 * print all settings messages
 * 
 * @since   0.9.0
 * @return  void
 */
function wpnb_settings_msg(): void
{
    global $wpnb_settings_msg;

    if (!isset($wpnb_settings_msg)) {
        $wpnb_settings_msg = [];
    }

    foreach ($wpnb_settings_msg as $msg) {
        echo wp_kses( $msg, Esc::get_allowed_html_notice() );
    }
}

/**
 * [Admin]
 * add settings message
 * 
 * @since   0.9.0
 * @param   string  $msg
 * @return  void
 */
function wpnb_add_settings_msg(string $msg): void
{
    global $wpnb_settings_msg;

    $wpnb_settings_msg[] = $msg;
}

/**
 * render settings field html
 * 
 * @since   0.9.0
 * @param   array   $args
 * @param   string  $value
 * @return  void
 */
function wpnb_render_settings_field(array $args, string $value = ''): void {
    $current_value = $value;
    $element_type  = $args['_type'] ?? '';
    $element_value = $args['_value'] ?? '';

    // escaping html
    // https://developer.wordpress.org/apis/security/escaping/
    $allowed_html = [];

    // select fields
    if ($element_type === 'select') {
        $element_options = $args['_options'] ?? [];
        $fetch_options   = '';

        foreach ($element_options as $option) {
            if ($current_value === $option['value']) {
                $option['selected'] = 'selected';
            }

            $fetch_options .= Element::create('option', $option['_value'], $option);

            // add esc allowed: option
            $allowed_html = array_merge($allowed_html, Esc::create_kses_allowed('option', $option));
        }

        $element_value = $fetch_options;
    }

    // setting current value to setting field
    if (!empty($current_value)) {
        if ($element_type === 'input') {
            $args['value'] = $current_value;
        } else if ($element_type === 'textarea') {
            $args['_value'] = $current_value;
        }
    }

    // add esc allowed: main element
    $allowed_html = array_merge($allowed_html, Esc::create_kses_allowed($element_type, $args));

    echo wp_kses( Element::create($element_type, $element_value, $args), $allowed_html );
}

/**
 * [Admin]
 * personal "do_settings_fields"
 * 
 * @see     https://developer.wordpress.org/reference/functions/do_settings_fields/
 * @since   0.9.0
 * @var     string  $section    Slug title of the settings section whose fields you want to show.
 * @var     string  $tab        Tab id
 * @return  void
 */
function wpnb_do_settings_fields(string $section, string $tab): void
{
	global $wpnb_settings_fields;

	if (!isset($wpnb_settings_fields[$section][$tab])) {
		return;
	}

    // get current settings (auto)
    $settings = Settings::get_all();

    echo '<input type="hidden" name="section" value="' . esc_attr($section) . '">';

	foreach ((array) $wpnb_settings_fields[$section][$tab] as $field) {
        $id = preg_replace('/_/', '.', $field['id'] ?? '', 2);

        echo '<div class="wpnb-settings-field tier justify:center content:center items:center">';

        echo '<div class="cell:20 wpnb-settings-title">';
        echo '<h4>'. esc_html($field['title']) .'</h4>';
        echo '</div>';

        echo '<div class="cell:50 wpnb-settings-input">';
        wpnb_render_settings_field($field['args'], $settings[$id] ?? '');
        echo '</div>';

        echo '<div class="cell:30 wpnb-settings-text">';
        echo wp_kses( $field['text'], Esc::get_allowed_html_text() );
        echo '</div>';

        echo '</div>';
	}
}

/**
 * Adds a new section to a settings page.
 *
 * @since   0.9.0
 * @global  array $wpnb_settings_sections Storage array of all wpnb settings
 *
 * @param   string   $id       Slug-name to identify the section. Used in the 'id' attribute of tags.
 * @param   string   $title    Formatted title of the section. Shown as the heading for the section.
 * @return  void
 */
function wpnb_add_settings_section(string $id, string $title): void
{
    global $wpnb_settings_sections;

    $wpnb_settings_sections[$id] = [
        'id'    => $id,
        'title' => $title
    ];
}

/**
 * Adds a new tab to a settings page.
 *
 * @since   0.9.0
 * @global  array $wpnb_settings_tabs Storage array of all wpnb settings
 *
 * @param   string   $id       Slug-name to identify the tab. Used in the 'id' attribute of tags.
 * @param   string   $title    Formatted title of the tab. Shown as the heading for the tab.
 * @param   string   $section  Section id
 * @return  void
 */
function wpnb_add_settings_tab(string $id, string $title, string $section): void
{
    global $wpnb_settings_tabs;

    if (!isset($wpnb_settings_tabs[$section])) {
        $wpnb_settings_tabs[$section] = [];
    }

    $wpnb_settings_tabs[$section][$id] = [
        'id'    => $id,
        'title' => $title
    ];
}


/**
 * Adds a new field to a section of a settings page.
 *
 * @since   0.9.0
 *
 * @global  array $wpnb_settings_fields Storage array of settings fields and info about their pages/sections.
 *
 * @param   string   $id        Slug-name to identify the field. Used in the 'id' attribute of tags.
 * @param   string   $title     Formatted title of the field. Shown as the label for the field
 *                              during output.
 * @param   string   $text      Text of setting field (description)
 * @param   string   $section   Optional. The slug-name of the section of the settings page
 *                              in which to show the box. Default 'default'.
 * @param   array    $args      Arguments
 */
function wpnb_add_settings_field(string $id, string $title, string $text, string $section, string $tab, array $args = []): void
{
	global $wpnb_settings_fields;

    if (!isset($wpnb_settings_fields[$section])) {
        $wpnb_settings_fields[$section] = [];
    }

    if (!isset($wpnb_settings_fields[$section][$tab])) {
        $wpnb_settings_fields[$section][$tab] = [];
    }

	$wpnb_settings_fields[$section][$tab][$id] = [
		'id'       => $id,
		'title'    => $title,
        'text'     => $text,
		'args'     => $args
    ];
}

/**
 * render whole settings fields and sections
 * 
 * @since   0.9.0
 * @param   $active_section
 * @return  void
 */
function wpnb_render_settings(string $active_section = ''): void
{
    global $wpnb_settings_sections, $wpnb_settings_tabs;

    if (empty($active_section)) {
        $active_section = $wpnb_settings_sections[ array_key_first($wpnb_settings_sections) ]['id'] ?? '';
    }

    echo '<h2 class="nav-tab-wrapper" style="border-bottom: 0; margin-bottom: -21px; padding-left: 10px; padding-right: 10px; ">';

    foreach ((array) $wpnb_settings_sections as $section) {
        $active_section_cls = $section['id'] === $active_section ? ' nav-tab-active' : '';
        $section_url = add_query_arg('section', $section['id']);
        echo '<a href="' . esc_url($section_url) . '" class="nav-tab'. esc_attr($active_section_cls) .'">' . esc_html($section['title']) . '</a>';
    }

    echo '</h2>';

    echo '<div class="wpnb-ad-l-box jst">';

    $section    = $wpnb_settings_sections[$active_section] ?? [];
    $section_id = $section['id'] ?? '';

    if (empty($section) || !isset($wpnb_settings_tabs[$section_id]) || empty($wpnb_settings_tabs[$section_id])) {
        echo 'Error';

        return;
    }

    $data_section = $wpnb_settings_tabs[$section_id];
    $action_url   = add_query_arg('section', $section_id, menu_page_url('wpnb-settings', false));

    echo '<form class="pack:no-gutters wpnb-mx-0" id="wpnb_settings_form_'. esc_attr($section_id) .'" style="max-width: unset !important;" method="POST" action="' . esc_url($action_url) . '">';
    echo '<div class="tier justify:center content:center wpnb-mx-0">';
    echo '<div id="wpnb-settings-tabs" class="wpnb-tabs cell:100">';

    echo '<h2 class="nav-tab-wrapper wpnb-tab-wrapper wpnb-w-100">';

    $active_tab = $data_section[ array_key_first($data_section) ]['id'] ?? '';
    foreach ($data_section as $tab => $tab_data) {
        $tab_active_cls = $active_tab === $tab ? ' nav-tab-active' : '';

        echo '<button type="button" class="nav-tab'. esc_attr($tab_active_cls) .'" data-wpnb-tab-set="'. esc_attr($tab) .'">'. esc_html($tab_data['title']) .'</button>';
    }

    echo '</h2>';

    echo '<hr />';

    foreach ($data_section as $tab => $tab_data) {
        $tab_active_arg = $active_tab === $tab ? ' data-wpnb-tab-active' : '';

        echo '<div class="tab-content cel:100 wpnb-mx-0" data-wpnb-tab="'. esc_attr($tab) .'"'. esc_attr($tab_active_arg) .'>';
        wpnb_do_settings_fields($section_id, $tab);
        echo '</div>';
    }

    submit_button( __('Save settings', 'notif-bell') );

    echo '</div>';
    echo '</div>';
    echo '</form>';

    echo '</div>';
}
