<?php
/**
 * Created by PhpStorm.
 * User: joakimcarlsten
 * Date: 2017-09-25
 * Time: 15:20
 */

if ( ! defined( 'ABSPATH' ) ) exit;

class Vinnia_Tracker_Shortcodes {


    private $parent;

    public function __construct($parent)
    {
        $this->parent = $parent;
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
        $templateLoader = new TemplateLoader();

        ob_start();
        $templateLoader->getTemplate('form-tracking.php');
        return ob_get_clean();
    }
}