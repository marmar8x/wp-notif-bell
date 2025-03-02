<?php

namespace Irmmr\WpNotifBell\User;

// If this file is called directly, abort.
defined('WPINC') || die;

use Irmmr\WpNotifBell\Helpers\Notif;
use Irmmr\WpNotifBell\Interfaces\UserInterface;
use Irmmr\WpNotifBell\Logger;
use Irmmr\WpNotifBell\Module\Query\Selector as QuerySelector;
use Irmmr\WpNotifBell\Notif\Collector;
use Irmmr\WpNotifBell\Notif\Module\Database;
use Irmmr\WpNotifBell\Settings;
use WP_User;

/**
 * Class Eye
 * and eye for seeing notifications,
 * define every notification status in eye of
 * defined user: seen | unseen
 *
 * @since    1.0.0
 * @package  Irmmr\WpNotifBell\User
 */
class Eye implements UserInterface
{
    // method: auto
    // @since   1.0.0
    public const BY_AUTO    = 0x40;

    // method: using binary for set seen, unseen [gmp]
    // @since   1.0.0
    public const BY_BIN     = 0x41;

    // method: using comma separated list for define seen notifications
    // @since   1.0.0
    public const BY_COMMA   = 0x42;

    // valid methods
    // @since   1.0.0
    public const VALID_METHODS = [
        self::BY_AUTO, self::BY_BIN, self::BY_COMMA
    ];

    // default class options  [null: use settings =>  wpnb-settings page]
    // @since   1.0.0
    public const DEF_OPTIONS = [
        // force using one method                   [default: self::BY_AUTO]
        'by'          => null,
        // manager can detect the best method       [default: true]
        // and change data based on situation
        'manager'     => null,
        // count limit for convert to binary        [default: 100]
        'count_limit' => null
    ];

    /**
     * WordPress user is here
     *
     * @since   1.0.0
     * @var     WP_User
     */
    protected WP_User $user;

    /**
     * Eye options
     *
     * @since   1.0.0
     * @var     array
     */
    protected array $options = self::DEF_OPTIONS;

    /**
     * user seen method
     *
     * @since   1.0.0
     * @var     int
     */
    protected int $by;

    /**
     * use data manager
     *
     * @since   1.0.0
     * @var     bool
     */
    protected bool $use_manager;

    /**
     * count limit for convert comma lists
     * to binary in seen data
     *
     * @since   1.0.0
     * @var     int
     */
    protected int $count_limit;

    /**
     * class constructor
     *
     * @param   WP_User     $user
     * @param   array       $options
     *  by      [int]   recommended: self::BY_AUTO, don't force Eye for data method
     *  manager [bool]  data manager status
     * @since   1.0.0
     */
    public function __construct(WP_User $user, array $options = self::DEF_OPTIONS)
    {
        $this->user = $user;

        $this->set_options($options);
    }

    /**
     * instance of this class
     *
     * @param   WP_User     $user
     * @param   array       $options
     *  by      [int]   recommended: self::BY_AUTO, don't force Eye for data method
     *  manager [bool]  data manager status
     * @since   1.0.0
     * @return  self
     */
    public static function ins(WP_User $user, array $options = self::DEF_OPTIONS): self
    {
        return new self($user, $options);
    }

    /**
     * set current options
     *
     * @param   array   $options
     * @since   1.0.0
     * @return  $this
     */
    public function set_options(array $options): self
    {
        $this->options = array_merge($this->options, $options);

        $by      = $this->options['by'] ?? Settings::get('modules.eye.method', 'auto');
        $manager = $this->options['manager'] ?? Settings::get('modules.eye.manager', 'enable');
        $limit   = $this->options['count_limit'] ?? Settings::get('modules.eye.count_limit', 100);

        $by_selects = [
            'auto' => self::BY_AUTO,
            'bin' => self::BY_BIN,
            'comma' => self::BY_COMMA
        ];

        $method = is_int($by) ? $by : $by_selects[$by] ?? self::BY_AUTO;

        $this->by           = in_array($method, self::VALID_METHODS, true) ? $method : self::BY_AUTO;
        $this->use_manager  = is_bool($manager) ? $manager : $manager === 'enable';
        $this->count_limit = intval($limit);

        return $this;
    }

    /**
     * get all options in array
     *
     * @since   1.0.0
     * @return  array
     */
    public function get_options(): array
    {
        return $this->options;
    }

    /**
     * get user seen field data [pure]
     *
     * @since   1.0.0
     * @return  string
     */
    public function get_seen_data(): string
    {
        return  trim( strval( get_user_meta($this->user->ID, self::SEEN_META_KEY, true) ) );
    }

    /**
     * get user meta seen list method
     *
     * @since   1.0.0
     * @return  int  self::BY_COMMA | self::BY_BIN
     */
    public function get_seen_method(): int
    {
        $field = get_user_meta($this->user->ID, self::SEEN_METHOD_KEY, true);

        if (!empty( $field )) {
            $method = intval($field);

            if ($method === self::BY_BIN || $method === self::BY_COMMA) {
                return $method;
            }
        }

        return $this->detect_seen_method() ?? self::BY_COMMA;
    }

    /**
     * !CORE!
     * get list of seen notifications [ids]
     *
     * @since   1.0.0
     * @param   null|int    $consider
     * @param   bool        $based_max_id
     * @return  array<int>
     */
    public function get_seen(?int $consider = null, bool $based_max_id = true): array
    {
        $method = $consider ?? $this->get_seen_method();
        $data   = $this->get_seen_data();

        if (empty( $data )) {
            return [];
        }

        $ids = [];

        if ($method === self::BY_BIN && $this->is_seen_data_binary($data)) {
            $bitmap = $this->gmp_init($data);
            $i      = 0;

            if (is_numeric($bitmap)) {
                return [];
            }

            $max_id = $this->get_max_notifs_id();

            while (true) {
                $i = gmp_scan1($bitmap, $i);

                if ($i === -1 || ($based_max_id && $i + 1 > $max_id)) {
                    break;
                }

                $ids[] = $i + 1;
                $i ++;
            }
        } elseif ($method === self::BY_COMMA) {
            $ids = explode(self::SEEN_SEPARATOR, $data);
            $ids = array_map('intval', $ids);
        }

        return $ids;
    }

    /**
     * !CORE!
     * add notif id to seen list
     *
     * @param   int     ...$ids
     * @return  void
     * @since   1.0.0
     */
    public function set_seen(int ...$ids): void
    {
        $this->set_status(true, ...$ids);
    }

    /**
     * !CORE!
     * set notifs that not seen [not recommended]
     *
     * @param   int     ...$ids
     * @return  void
     * @since   1.0.0
     */
    public function set_unseen(int ...$ids): void
    {
        $this->set_status(false, ...$ids);
    }

    /**
     * !CORE!
     * add notif id to seen list
     *
     * @param   bool    $status
     * @param   int     ...$ids
     * @return  void
     * @since   1.0.0
     */
    public function set_status(bool $status, int ...$ids): void
    {
        $this->manage();

        $method = $this->get_seen_method();
        $data   = $this->get_seen_data();

        if ($method === self::BY_BIN) {
            $value = $this->update_seen_list_bin($data, $ids, $status);

            if (!is_null($value)) {
                $this->set_seen_method($method);
                $this->set_seen_data($value);
            }
        } elseif ($method === self::BY_COMMA) {
            $value = $this->update_seen_list_com($data, $ids, $status);

            $this->set_seen_method($method);
            $this->set_seen_data($value);
        }
    }

    /**
     * set all notifs seen
     *
     * @return  void
     * @since   1.0.0
     */
    public function set_seen_all(): void
    {
        $this->set_status_all(true);
    }

    /**
     * set all notifs unseen
     *
     * @return  void
     * @since   1.0.0
     */
    public function set_unseen_all(): void
    {
        $this->set_status_all(false);
    }

    /**
     * set all notifications status
     *
     * @param   bool    $status         true => seen   false => unseen
     * @param   bool    $bin_limited    true => using own user notifs ids for bin [user_notifs_ids]      [slower, more precisely]
     *                                  false => make all of notifs status to seen [1,2,3,...,max_id]    [faster, without precision]
     * @return  void
     * @since   1.0.0
     */
    public function set_status_all(bool $status, bool $bin_limited = true): void
    {
        // we are facing an enforced data method
        if ($this->by !== self::BY_AUTO) {
            $this->convert($this->by);
        }

        $method = $this->get_seen_method();
        $data   = $this->get_seen_data();

        if (empty( $data )) {
            return;
        }

        $max_id = $this->get_max_notifs_id();

        if ($method === self::BY_BIN) {

            if ($bin_limited) {
                $notifs_ids = $this->get_notifs_ids();

                $this->set_status($status, ...$notifs_ids);
            } else {
                $bitmap  = $this->gmp_init($data);

                if (is_null($bitmap)) {
                    return;
                }

                $max_bit = gmp_pow(2, $max_id);

                if ($status) {
                    $bitmap = gmp_sub($max_bit, 1);
                } else {
                    $bitmap = gmp_mul($bitmap, $max_bit);
                }

                $this->set_seen_data( gmp_strval($bitmap, 2) );
                $this->set_seen_method($method);
            }

        } elseif ($method === self::BY_COMMA) {
            $notifs_ids = $this->get_notifs_ids();

            $this->set_seen_data( implode(self::SEEN_SEPARATOR, $notifs_ids) );
            $this->set_seen_method($method);
        }
    }

    /**
     * get notif status
     *
     * @param   int     $id
     * @since   1.0.0
     * @return  bool    true=seen   &   false=unseen
     */
    public function get_status(int $id): bool
    {
        $data   = $this->get_seen_data();
        $method = $this->get_seen_method();

        if (empty($data)) {
            return false;
        }

        if ($method === self::BY_BIN) {
            $bitmap_gmp = $this->gmp_init($data);

            if (is_null($bitmap_gmp)) {
                return false;
            }

            return gmp_testbit($bitmap_gmp, $id - 1);
        } elseif ($method === self::BY_COMMA) {
            global $wpdb;

            // get database prefix
            $db_prefix  = $wpdb->prefix;

            // get `usermeta` table name
            $user_meta_table = $db_prefix . 'usermeta';

            // create new mysql query
            $query = new QuerySelector($user_meta_table);

            // user query selector
            // trying to search in seen list for notif id
            // $notif_id is a safe entered integer
            $query->selector()
                ->count()
                ->where()
                ->equals('meta_key', self::SEEN_META_KEY)
                ->equals('user_id', $this->user->ID)
                ->asLiteral('find_in_set('.$id.',wp_usermeta.meta_value)')
                ->end();

            // get database result
            $db = new Database;
            $result = intval( $db->get_var($query->get(), $query->get_builder_values()) );

            return $result > 0;
        }

        return false;
    }

    /**
     * get count of seen notifications
     *
     * @since   1.0.0
     * @param   bool    $status     true:seen   false:unseen
     * @return  int
     */
    public function get_count(bool $status): int
    {
        $method = $this->get_seen_method();
        $data   = $this->get_seen_data();

        if (empty( $data )) {
            return 0;
        }

        if ($method === self::BY_BIN) {
            $bitmap = $this->gmp_init($data);

            $seen_count = is_null($bitmap) ? 0 : gmp_popcount($bitmap);

            if ($status) {
                return $seen_count;
            }

            $total_notifs = strlen($data);

            return $total_notifs - $seen_count;
        } elseif ($method === self::BY_COMMA) {
            return count( explode(self::SEEN_SEPARATOR, $data) );
        }

        return 0;
    }

    /**
     * get seen notifs count
     *
     * @since   1.0.0
     * @return  int
     */
    public function get_seen_count(): int
    {
        return $this->get_count(true);
    }

    /**
     * get unseen notifs count
     *
     * @since   1.0.0
     * @return  int
     */
    public function get_unseen_count(): int
    {
        return $this->get_count(false);
    }

    /**
     * init for first usage
     *
     * @return  void
     * @since   1.0.0
     */
    protected function init(): void
    {
        // reset data for invalid entries
        if (!$this->is_seen_data_valid()) {
            $this->set_seen_data('');
        }
    }

    /**
     * [!] This is better in public access [!]
     * manage seen method
     * this can change method in db based
     * or notifs count and current status for
     * better performance.
     *
     * @since   1.0.0
     * @return  void
     */
    public function manage(): void
    {
        $this->init();

        // we are facing an enforced data method
        if ($this->by !== self::BY_AUTO) {
            $this->convert($this->by);

            return;
        }

        // manage is enabled?
        if (!$this->use_manager) {
            return;
        }

        $all_count  = $this->get_notifs_count();
        $seen_count = $this->get_count(true);

        if ($this->by === self::BY_AUTO) {
            $this->convert( $seen_count > $this->count_limit ? self::BY_BIN : self::BY_COMMA );
        }

        $method     = $this->get_seen_method();
        $notifs_ids = $this->get_notifs_ids();

        if ($method === self::BY_COMMA && $seen_count > $all_count) {
            $refresh_seen = array_filter($this->get_seen(self::BY_COMMA), function ($id) use ($notifs_ids) {
                return in_array($id, $notifs_ids);
            });

            $this->set_seen_data( implode(self::SEEN_SEPARATOR, $refresh_seen) );
        }
    }

    /**
     * convert data and method for seen list
     *
     * @param   int     $by
     * @since   1.0.0
     * @return  void
     */
    public function convert(int $by): void
    {
        $method = $this->get_seen_method();

        if ($method === $by) {
            return;
        }

        if ($by === self::BY_BIN) {
            $this->convert_data_to_bin();
        } elseif ($by === self::BY_COMMA) {
            $this->convert_data_to_comma();
        }
    }

    /**
     * get list of all user notifications with id
     *
     * @since   1.0.0
     * @return  array<int>
     */
    public function get_notifs_ids(): array
    {
        $collector  = new Collector;
        $ids        = [];
        $collector->target_by_user($this->user);

        foreach ($collector->get() as $notif) {
            if (isset($notif->id) && $notif->id !== 0) {
                $ids[] = intval($notif->id);
            }
        }

        return $ids;
    }

    /**
     * get notifications count
     *
     * @since   1.0.0
     * @return  int
     */
    public function get_notifs_count(): int
    {
        $collector = new Collector;

        $collector->target_by_user($this->user);

        return $collector->get_count();
    }

    /**
     * convert to Binary
     *
     * @since   1.0.0
     * @return  void
     */
    protected function convert_data_to_bin(): void
    {
        if ($this->get_seen_method() === self::BY_BIN) {
            return;
        }

        $value  = '0';
        $data   = $this->get_seen_data();
        $list   = explode(self::SEEN_SEPARATOR, $data);
        $max_id = $this->get_max_notifs_id();

        if (!empty( $list ) && $max_id !== 0) {
            $binary_string = str_repeat('0', $max_id);

            foreach ($list as $id) {
                if ($id >= 1 && $id <= $max_id) {
                    $binary_string[$id - 1] = '1';
                }
            }

            $binary_gmp = $this->gmp_init($binary_string);

            if (!is_null( $binary_gmp )) {
                $value = gmp_strval($binary_gmp, 2);
            }
        }

        $this->set_seen_data($value);
        $this->set_seen_method(self::BY_BIN);
    }

    /**
     * convert to Comma
     * [please don't use this type of conversion by yourself :/]
     *
     * @return  void
     *@since   1.0.0
     */
    protected function convert_data_to_comma(): void
    {
        if ($this->get_seen_method() === self::BY_COMMA) {
            return;
        }

        $value = '';
        $data  = $this->get_seen_data();

        if (!empty( $data )) {
            $ids   = $this->get_seen(self::BY_BIN);
            $value = implode(self::SEEN_SEPARATOR, $ids);
        }

        $this->set_seen_data($value);
        $this->set_seen_method(self::BY_COMMA);
    }

    /**
     * check for seen data validation
     * it is broken, or it's just normal
     *
     * @param   string|null  $data
     * @return  bool
     * @since   1.0.0
     */
    public function is_seen_data_valid(?string $data = null): bool
    {
        $data = $data ?? $this->get_seen_data();

        return empty($data) || preg_match('/^[0-9]+(,[0-9]+)*,?$/', $data) === 1;
    }

    /**
     * if seen data is binary
     *
     * @param   string|null  $data
     * @return  bool
     * @since   1.0.0
     */
    public function is_seen_data_binary(string $data = null): bool
    {
        $data = $data ?? $this->get_seen_data();

        return empty($data) || preg_match('~^[01]+$~', $data) === 1;
    }

    /**
     * try to detect seen method with data
     *
     * @return  int|null
     * @since   1.0.0
     */
    public function detect_seen_method(): ?int
    {
        $data = $this->get_seen_data();

        // it's broken
        if (!$this->is_seen_data_valid( $data )) {
            return null;
        }

        // it's binary[01]
        if ( preg_match('~^[01]+$~', $data) ) {

            if (strlen($data) !== 1) {
                return self::BY_BIN;
            }
        }

        return self::BY_COMMA;
    }

    /**
     * set seen data as pure string [db]
     *
     * @param   string  $data
     * @return  bool
     * @since   1.0.0
     */
    protected function set_seen_data(string $data): bool
    {
        return update_user_meta($this->user->ID, self::SEEN_META_KEY, $data);
    }

    /**
     * set seen data as integer [db]
     *
     * @param   int  $method
     * @return  bool
     * @since   1.0.0
     */
    protected function set_seen_method(int $method): bool
    {
        return update_user_meta($this->user->ID, self::SEEN_METHOD_KEY, strval( $method ));
    }

    /**
     * get max notifications id
     *
     * @since   1.0.0
     * @return  int
     */
    protected function get_max_notifs_id(): int
    {
        return Notif::get_max_id();
    }

    /**
     * update seen list status for binary bitmap
     *
     * @param   string  $data
     * @param   int     ...$ids
     * @param   bool    $status true => add     false => remove
     * @return  string|null
     * @since   1.0.0
     */
    protected function update_seen_list_bin(string $data, array $ids, bool $status): ?string
    {
        $ids    = array_map('intval', $ids);
        $bitmap = $this->gmp_init(empty($data) ? '0' : $data);
        $max_id = $this->get_max_notifs_id();

        if (is_null( $bitmap )) {
            return null;
        }

        foreach ($ids as $id) {
            if ($id < 1 || $id > $max_id) {
                continue;
            }

            if ($status) {
                gmp_setbit($bitmap, $id - 1);
            } else {
                gmp_clrbit($bitmap, $id - 1);
            }
        }

        return gmp_strval($bitmap, 2);
    }

    /**
     * update seen list status for binary bitmap
     *
     * @param   string  $data
     * @param   int     ...$ids
     * @param   bool    $status true => add     false => remove
     * @return  string
     * @since   1.0.0
     */
    protected function update_seen_list_com(string $data, array $ids, bool $status): string
    {
        $ids    = array_map('intval', $ids);
        $list   = explode(self::SEEN_SEPARATOR, $data);
        $max_id = $this->get_max_notifs_id();

        if ($status) {
            $value = array_merge($list, $ids);
        } else {
            $value = array_filter($list, function ($id) use ($ids) {
                return !in_array($id, $ids);
            });
        }

        $result = array_unique( array_filter($value, function ($id) use ($max_id) {
            return !empty($id) && $id <= $max_id;
        }) );

        return implode(self::SEEN_SEPARATOR, $result);
    }

    /**
     * handle and init a gmp
     *
     * @since   1.0.0
     * @param   string  $bitmap
     * @param   int     $base
     * @return  \GMP|resource|null
     */
    protected function gmp_init(string $bitmap, int $base = 2)
    {
        $bitmap = empty($bitmap) ? '0' : $bitmap;

        try {
            return gmp_init($bitmap, $base);
        } catch (\Exception $e) {
            Logger::add("gmp_init: {$e->getMessage()}", Logger::N_MAIN, Logger::LEVEL_ERROR, [
                'code' => $e->getCode(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);

            return null;
        }
    }
}