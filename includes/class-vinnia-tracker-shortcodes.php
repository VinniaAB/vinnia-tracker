<?php
/**
 * Created by PhpStorm.
 * User: joakimcarlsten
 * Date: 2017-09-25
 * Time: 15:20
 */

if ( ! defined( 'ABSPATH' ) ) exit;

class Vinnia_Tracker_Shortcodes {


    public function __construct()
    {

    }

    public function registerShortcodes()
    {
        $shortcodes = [
            'tracking_form' => 'Vinnia_Tracker_Shortcodes::form'
        ];

        foreach ($shortcodes as $shortcode => $function) {
            add_shortcode("{$shortcode}", $function);
        }
    }

    public static function form()
    {
        $template = plugin_dir_path(__DIR__).'views/form-tracking.php';

        ob_start();
        include($template);
        return ob_get_clean();
    }
}