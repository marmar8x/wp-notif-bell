<?php

namespace Irmmr\WpNotifBell\Notif\Assist;

// If this file is called directly, abort.
defined('WPINC') || die;

use HTMLPurifier_Config;
use HTMLPurifier;
use Irmmr\WpNotifBell\Container;
use Irmmr\WpNotifBell\Helpers\Esc;
use Irmmr\WpNotifBell\Logger;
use Irmmr\WpNotifBell\Helpers\Data;
use stdClass;

/**
 * Class Formatter
 * clean and render all notif fields
 * 
 * @since    0.9.0
 * @package  Irmmr\WpNotifBell\Notif\Assist
 */
class Formatter
{
    /**
     * @param string $string
     * @return string
     */
    protected static function decodeHtmlChars(string $string): string
    {
        return html_entity_decode(htmlspecialchars_decode( $string ));
    }

    /**
     * encode database array data for insert
     * 
     * @since   0.9.0
     * @param   array   $data
     * @return  array
     */
    public static function encode(array $data): array
    {
        // title
        if (isset($data['title'])) {
            $data['title'] = Data::clean_db( strip_tags( $data['title'] ) );
        }

        // content
        if (isset($data['content'])) {
            // get pure html for start encoding
            $init_content   = $data['content'];
            $content        = $init_content;

            // checking the content type
            $format  = $data['format'] ?? 'pure-text';

            // remove all html tags for pure-text & text
            if ($format === 'pure-text' || $format === 'text') {
                $content = strip_tags($content);

            } elseif ($format === 'html' || $format === 'markdown') {
                // preventing xss, excusable html codes :/
                $config     = HTMLPurifier_Config::createDefault();
                $config->set('Core.Encoding', 'UTF-8');

                $purifier   = new HTMLPurifier($config);
                $content    = $purifier->purify($content);

                // xss: filter all html elements with attrs [escaping]
                $content    = wp_kses($content, Esc::get_allowed_html_content());

                // [Debug] runs only when debugging
                if (Container::$debugging) {
                    Logger::add('Formatter: encoding html content', Logger::N_DEBUG, Logger::LEVEL_LOG, [
                        'content' => $init_content,
                        'encoded' => $content
                    ]);
                }

            }

            $data['content'] = Data::clean_db($content);
        }

        return $data;
    }

    /**
     * decode database array data for fetch
     * 
     * @since   0.9.0
     * @param   stdClass   $data
     * @return  stdClass
     */
    public static function decode(stdClass $data): stdClass
    {
        // title
        if (isset($data->title)) {
            $data->title    = wp_unslash( self::decodeHtmlChars( $data->title ) );
        }

        // content
        if (isset($data->content)) {
            $data->content  = wp_unslash( self::decodeHtmlChars( $data->content ) );
        }

        return $data;
    }
}