<?php

if (!defined('ABSPATH')) exit;

class Vinnia_Tracker
{

    const STATUS_CODES = [
        100 => [
            'class' => 'success',
            'icon' => 'fa-check'
        ],
        200 => [
            'class' => 'info',
            'icon' => 'fa-info'
        ],
        500 => [
            'class' => 'danger',
            'icon' => 'fa-warning'
        ],
        0 => [
            'class' => 'info',
            'icon' => 'fa-info'
        ],
    ];

    /**
     * The single instance of Vinnia_Tracker.
     * @var    object
     * @access  private
     * @since    1.0.0
     */
    private static $_instance = null;

    /**
     * Settings class object
     * @var     object
     * @access  public
     * @since   1.0.0
     */
    public $settings = null;

    /**
     * The version number.
     * @var     string
     * @access  public
     * @since   1.0.0
     */
    public $_version;

    /**
     * The token.
     * @var     string
     * @access  public
     * @since   1.0.0
     */
    public $_token;

    /**
     * The main plugin file.
     * @var     string
     * @access  public
     * @since   1.0.0
     */
    public $file;

    /**
     * The main plugin directory.
     * @var     string
     * @access  public
     * @since   1.0.0
     */
    public $dir;

    /**
     * The plugin assets directory.
     * @var     string
     * @access  public
     * @since   1.0.0
     */
    public $assets_dir;

    /**
     * The plugin assets URL.
     * @var     string
     * @access  public
     * @since   1.0.0
     */
    public $assets_url;

    /**
     * Suffix for Javascripts.
     * @var     string
     * @access  public
     * @since   1.0.0
     */
    public $script_suffix;

    /**
     * Constructor function.
     * @access  public
     * @since   1.0.0
     * @return  void
     */
    public function __construct($file = '', $version = '1.0.0')
    {
        $this->_version = $version;
        $this->_token = 'vinnia_tracker';

        // Load plugin environment variables
        $this->file = $file;
        $this->dir = dirname($this->file);
        $this->assets_dir = trailingslashit($this->dir) . 'assets';
        $this->assets_url = esc_url(trailingslashit(plugins_url('/assets/', $this->file)));

        $this->script_suffix = defined('SCRIPT_DEBUG') && SCRIPT_DEBUG ? '' : '.min';

        register_activation_hook($this->file, array($this, 'install'));

        // Load frontend JS & CSS
        add_action('wp_enqueue_scripts', array($this, 'enqueue_styles'), 10);
        add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'), 10);

        // Load admin JS & CSS
        add_action('admin_enqueue_scripts', array($this, 'admin_enqueue_scripts'), 10, 1);
        add_action('admin_enqueue_scripts', array($this, 'admin_enqueue_styles'), 10, 1);

        $this->shortcodeManager = new Vinnia_Tracker_Shortcodes($this);
        $this->shortcodeManager->registerShortcodes();

        //Add AJAX action
        add_action('wp_ajax_nopriv_track_package', function () {
            $this->trackPackage();
        });

        // Load API for generic admin functions
        if (is_admin()) {
            $this->admin = new Vinnia_Tracker_Admin_API();
        }

        // Handle localisation
        $this->load_plugin_textdomain();
        add_action('init', array($this, 'load_localisation'), 0);
    } // End __construct ()

    /**
     * Wrapper function to register a new post type
     * @param  string $post_type Post type name
     * @param  string $plural Post type item plural name
     * @param  string $single Post type item single name
     * @param  string $description Description of post type
     * @return object              Post type class object
     */
    public function register_post_type($post_type = '', $plural = '', $single = '', $description = '', $options = array())
    {

        if (!$post_type || !$plural || !$single) return;

        $post_type = new Vinnia_Tracker_Post_Type($post_type, $plural, $single, $description, $options);

        return $post_type;
    }

    /**
     * Wrapper function to register a new taxonomy
     * @param  string $taxonomy Taxonomy name
     * @param  string $plural Taxonomy single name
     * @param  string $single Taxonomy plural name
     * @param  array $post_types Post types to which this taxonomy applies
     * @return object             Taxonomy class object
     */
    public function register_taxonomy($taxonomy = '', $plural = '', $single = '', $post_types = array(), $taxonomy_args = array())
    {

        if (!$taxonomy || !$plural || !$single) return;

        $taxonomy = new Vinnia_Tracker_Taxonomy($taxonomy, $plural, $single, $post_types, $taxonomy_args);

        return $taxonomy;
    }

    /**
     * Load frontend CSS.
     * @access  public
     * @since   1.0.0
     * @return void
     */
    public function enqueue_styles()
    {
        wp_register_style($this->_token . '-frontend', esc_url($this->assets_url) . 'css/frontend.css', array(), $this->_version);
        wp_enqueue_style($this->_token . '-frontend');
    } // End enqueue_styles ()

    /**
     * Load frontend Javascript.
     * @access  public
     * @since   1.0.0
     * @return  void
     */
    public function enqueue_scripts()
    {
        wp_register_script($this->_token . '-frontend', esc_url($this->assets_url) . 'js/frontend' . $this->script_suffix . '.js', array('jquery'), $this->_version);
        wp_enqueue_script($this->_token . '-frontend');
        wp_localize_script($this->_token . '-frontend', 'PMPObject', ['ajaxUrl' => admin_url('admin-ajax.php')]);


        if (empty(get_option($this->settings->base.'disable_fontawesome'))) {
            wp_enqueue_script('font-awesome', 'https://use.fontawesome.com/7f36b8149b.js');
        }
    } // End enqueue_scripts ()

    /**
     * Load admin CSS.
     * @access  public
     * @since   1.0.0
     * @return  void
     */
    public function admin_enqueue_styles($hook = '')
    {
        wp_register_style($this->_token . '-admin', esc_url($this->assets_url) . 'css/admin.css', array(), $this->_version);
        wp_enqueue_style($this->_token . '-admin');
    } // End admin_enqueue_styles ()

    /**
     * Load admin Javascript.
     * @access  public
     * @since   1.0.0
     * @return  void
     */
    public function admin_enqueue_scripts($hook = '')
    {
        wp_register_script($this->_token . '-admin', esc_url($this->assets_url) . 'js/admin' . $this->script_suffix . '.js', array('jquery'), $this->_version);
        wp_enqueue_script($this->_token . '-admin');

    } // End admin_enqueue_scripts ()

    /**
     * Load plugin localisation
     * @access  public
     * @since   1.0.0
     * @return  void
     */
    public function load_localisation()
    {
        load_plugin_textdomain('vinnia-tracker', false, dirname(plugin_basename($this->file)) . '/lang/');
    } // End load_localisation ()

    /**
     * Load plugin textdomain
     * @access  public
     * @since   1.0.0
     * @return  void
     */
    public function load_plugin_textdomain()
    {
        $domain = 'vinnia-tracker';

        $locale = apply_filters('plugin_locale', get_locale(), $domain);

        load_textdomain($domain, WP_LANG_DIR . '/' . $domain . '/' . $domain . '-' . $locale . '.mo');
        load_plugin_textdomain($domain, false, dirname(plugin_basename($this->file)) . '/lang/');
    } // End load_plugin_textdomain ()

    /**
     * Main Vinnia_Tracker Instance
     *
     * Ensures only one instance of Vinnia_Tracker is loaded or can be loaded.
     *
     * @since 1.0.0
     * @static
     * @see Vinnia_Tracker()
     * @return Main Vinnia_Tracker instance
     */
    public static function instance($file = '', $version = '1.0.0')
    {
        if (is_null(self::$_instance)) {
            self::$_instance = new self($file, $version);
        }
        return self::$_instance;
    } // End instance ()

    /**
     * Cloning is forbidden.
     *
     * @since 1.0.0
     */
    public function __clone()
    {
        _doing_it_wrong(__FUNCTION__, __('Cheatin&#8217; huh?'), $this->_version);
    } // End __clone ()

    /**
     * Unserializing instances of this class is forbidden.
     *
     * @since 1.0.0
     */
    public function __wakeup()
    {
        _doing_it_wrong(__FUNCTION__, __('Cheatin&#8217; huh?'), $this->_version);
    } // End __wakeup ()

    /**
     * Installation. Runs on activation.
     * @access  public
     * @since   1.0.0
     * @return  void
     */
    public function install()
    {
        $this->_log_version_number();
    } // End install ()

    /**
     * Log the plugin version number.
     * @access  public
     * @since   1.0.0
     * @return  void
     */
    private function _log_version_number()
    {
        update_option($this->_token . '_version', $this->_version);
    } // End _log_version_number ()


    private function servicesFactory()
    {
        $services = [];
        $guzzle = new GuzzleHttp\Client();

        $dhlSiteId = get_option($this->settings->base.'dhl_site_id');
        $dhlPassword = get_option($this->settings->base.'dhl_password');
        $dhlAccountNumber = get_option($this->settings->base.'dhl_account_number');

        if (!empty($dhlSiteId) && !empty($dhlPassword) && !empty($dhlAccountNumber)) {
            $dhlCredentials = new Vinnia\Shipping\DHL\Credentials($dhlSiteId, $dhlPassword, $dhlAccountNumber);
            $dhlService = new Vinnia\Shipping\DHL\Service(new GuzzleHttp\Client(), $dhlCredentials);
            array_push($services, $dhlService);
        }

        $fedexCredentialKey = get_option($this->settings->base.'fedex_credential_key');
        $fedexCredentialPassword = get_option($this->settings->base.'fedex_credential_password');
        $fedexAccountNumber = get_option($this->settings->base.'fedex_account_number');
        $fedexMeterNumber = get_option($this->settings->base.'fedex_meter_number');

        if (!empty($fedexCredentialKey) && !empty($fedexCredentialPassword) && !empty($fedexAccountNumber) && !empty($fedexMeterNumber)) {
            $fedexCredentials = new Vinnia\Shipping\FedEx\Credentials($fedexCredentialKey, $fedexCredentialPassword, $fedexAccountNumber, $fedexMeterNumber);
            $fedexService = new Vinnia\Shipping\FedEx\Service($guzzle, $fedexCredentials);
            array_push($services, $fedexService);
        }

        $upsUsername = get_option($this->settings->base.'ups_username');
        $upsPassword = get_option($this->settings->base.'ups_password');
        $upsAccessLicence = get_option($this->settings->base.'ups_access_licence');

        if (!empty($upsUsername) && !empty($upsPassword) && !empty($upsAccessLicence)) {
            $upsCredentials = new Vinnia\Shipping\UPS\Credentials($upsUsername, $upsPassword, $upsAccessLicence);
            $upsService = new Vinnia\Shipping\UPS\Service(new GuzzleHttp\Client(), $upsCredentials);
            array_push($services, $upsService);
        }

        $tntUsername = get_option($this->settings->base.'tnt_username');
        $tntPassword = get_option($this->settings->base.'tnt_password');
        $tntAccountNumber = get_option($this->settings->base.'tnt_account_number');

        if (!empty($tntUsername) && !empty($tntPassword) && !empty($tntAccountNumber)) {
            $tntCredentials = new Vinnia\Shipping\UPS\Credentials($tntUsername, $tntPassword, $tntAccountNumber);
            $tntService = new Vinnia\Shipping\UPS\Service(new GuzzleHttp\Client(), $tntCredentials);
            array_push($services, $tntService);
        }

        return $services;
    }

    private function trackPackage()
    {
        $response = [];
        $trackingNumber = $_POST['trackingNumber'] ?? '';

        if (empty($trackingNumber)) {
            $return = [
                'success' => false,
                'html' => sprintf('<div class="alert alert-info alert-dismisible" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>%s</div>', __('No tracking number supplied', 'ysds'))
            ];
            http_response_code(422);
            echo wp_json_encode($return);
            wp_die();
        }

        $compositeTracker = new \Vinnia\Shipping\CompositeTracker($this->servicesFactory());
        $templateLoader = new TemplateLoader();

        $promise = $compositeTracker->getTrackingStatus($trackingNumber)->then(
            function ($result) use ($trackingNumber, $templateLoader) {

                //if result == null

                ob_start();
                $templateLoader->getTemplate('tracking-result.php', [
                    "result" => $result,
                    "trackingNumber" => $trackingNumber
                ]);
                $html = ob_get_clean();

                $response['html'] = $html;
                $response['success'] = true;
                $response['trackingNo'] = $trackingNumber;
                $response['data'] = $result;

                echo wp_json_encode($response);
            },
            function($result) {
                $response['html'] = '<h1>No!</h1>';
                $response['success'] = false;
                $response['trackingNo'] = "none";
                $response['data'] = $result;
                echo wp_json_encode($response);
            });
        //4796890652

        $result = $promise->wait();

        //error_log(print_r($result, true));

        wp_die();
    }

}
