<?php

namespace Irmmr\WpNotifBell\Notif\Assist;

// If this file is called directly, abort.
defined('WPINC') || die;

use HTMLPurifier_Config;
use HTMLPurifier;
use Irmmr\WpNotifBell\Container;
use Irmmr\WpNotifBell\Logger;
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
        if (isset($data['content'])) {
            // get pure html for start encoding
            $content = self::decodeHtmlChars( $data['content'] );

            // preventing xss, excusable html codes :/
            $config     = HTMLPurifier_Config::createDefault();
            $config->set('Core.Encoding', 'UTF-8');

            $purifier   = new HTMLPurifier($config);
            $clean_html = $purifier->purify($content);

            // [Debug] runs only when debugging
            if (Container::$debugging) {
                Logger::add('Formatter: encoding html content', Logger::N_DEBUG, Logger::LEVEL_LOG, [
                    'content' => $content,
                    'encoded' => $clean_html
                ]);
            }

            $data['content'] = htmlentities( $clean_html );
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
        if (isset($data->content)) {
            $data->content = self::decodeHtmlChars( $data->content );
            $data->content = wp_unslash($data->content);

            $data->title  = wp_unslash($data->title);
        }

        return $data;
    }
}