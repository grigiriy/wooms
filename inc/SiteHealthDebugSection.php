<?php

namespace WooMS;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}


/**
 * Import Product Images
 */
class SiteHealthDebugSection
{


    public static $plugin_dir = ABSPATH . "wp-content/plugins/";
    public static $base_plugin_url = "wooms/wooms.php";
    public static $xt_plugin_url = "wooms-extra/wooms-extra.php";
    public static $settings_page_url = 'admin.php?page=mss-settings';
    public static $wooms_check_login_password;
    public static $wooms_check_woocommerce_version_for_wooms;



    public static function init()
    {
        add_filter('debug_information', [__CLASS__, 'add_info_to_debug']);

        add_filter('add_wooms_plugin_debug', [__CLASS__, 'wooms_debug_check_version_for_wooms']);

        add_filter('add_wooms_plugin_debug', [__CLASS__, 'wooms_check_different_versions_of_plugins']);

        add_filter('add_wooms_plugin_debug', [__CLASS__, 'check_login_and_password']);
    }

    public static function wooms_debug_check_version_for_wooms($debug_info)
    {
        $wc_version = WC()->version;

        $result = [
            'label'    => 'Версия WooCommerce',
            'value'   => sprintf('%s %s', $wc_version, '✔️'),
        ];

        if (version_compare($wc_version, '3.6.0', '<=')) {
            $result['value'] = sprintf('Ваша версия WooCommerce плагина %s. Обновите пожалуйста WooCommerce чтобы WooMS & WooMS XT работали %s', $wc_version, '❌');
        }

        $debug_info['wooms-plugin-debug']['fields']['Woocommerce'] = $result;

        return $debug_info;
    }

    /**
     * check differences of versions
     *
     * @return void
     */
    public static function wooms_check_different_versions_of_plugins($debug_info)
    {

        $base_plugin_data = get_plugin_data(self::$plugin_dir . self::$base_plugin_url);
        $xt_plugin_data = get_plugin_data(self::$plugin_dir . self::$xt_plugin_url);
        $base_version = $base_plugin_data['Version'];
        $xt_version = $xt_plugin_data['Version'];

        $result = [
            'label'    => 'Версии плагинов',
            'value'   => sprintf('Wooms(%s) %s WoomsXT(%s) %s', $base_version, '=', $xt_version, '✔️'),
        ];

        if ($base_version !== $xt_version) {
            $result = [
                'label'    => 'Версии плагинов',
                'value'   => sprintf('Wooms(%s) WoomsXT(%s) %s', $base_version, $xt_version, '❌'),
            ];
        }

        $debug_info['wooms-plugin-debug']['fields']['wooms-plugins-versions'] = $result;

        return $debug_info;
    }

    /**
     * debuging and adding to debug sections of health page
     *
     * @param [type] $debug_info
     * @return void
     */
    public static function add_info_to_debug($debug_info)
    {

        $base_plugin_data = get_plugin_data(self::$plugin_dir . self::$base_plugin_url);
        $xt_plugin_data = get_plugin_data(self::$plugin_dir . self::$xt_plugin_url);
        $base_version = $base_plugin_data['Version'];
        $xt_version = $xt_plugin_data['Version'];

        $debug_info['wooms-plugin-debug'] = [
            'label'    => 'Wooms',
            'fields'   => [
                'Wooms Version' => [
                    'label'    => 'Версия Wooms',
                    'value'   => sprintf('%s %s', $base_version, '✔️'),
                ],
                'WoomsXT Version' => [
                    'label'    => 'Версия WoomsXT',
                    'value'   => sprintf('%s %s', $xt_version, '✔️'),
                ],
            ],
        ];

        $debug_info = apply_filters('add_wooms_plugin_debug', $debug_info);

        return $debug_info;
    }

    /**
     * checking login and password moy sklad
     *
     * @param [type] $debug_info
     * @return void
     */
    public static function check_login_and_password($debug_info)
    {

        if (!get_transient('wooms_check_login_password')) {
            return $debug_info;
        }

        $debug_info['wooms-plugin-debug']['fields']['wooms-login-check'] = [
            'label'    => 'Версия Wooms',
            'value'   => sprintf('Ваш логин и пароль от мой склад неверен %s', '❌'),
        ];
        return $debug_info;
    }
    
}

SiteHealthDebugSection::init();
