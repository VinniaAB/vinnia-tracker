<?php
/*
 * Plugin Name: Vinnia Tracker
 * Version: 1.0
 * Plugin URI: http://www.vinnia.se/
 * Description: Plugin for tracking shipments.
 * Author: Joakim Carlsten
 * Author URI: http://www.vinnia.se/
 * Requires at least: 4.0
 * Tested up to: 4.0
 *
 * Text Domain: vinnia-tracker
 * Domain Path: /lang/
 *
 * @package WordPress
 * @author Hugh Lashbrooke
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) exit;

// Load plugin class files
require_once( 'includes/class-vinnia-tracker.php' );
require_once( 'includes/class-vinnia-tracker-settings.php' );

// Load plugin libraries
require_once( 'includes/lib/class-vinnia-tracker-admin-api.php' );
require_once( 'includes/lib/class-vinnia-tracker-post-type.php' );
require_once( 'includes/lib/class-vinnia-tracker-taxonomy.php' );

/**
 * Returns the main instance of Vinnia_Tracker to prevent the need to use globals.
 *
 * @since  1.0.0
 * @return object Vinnia_Tracker
 */
function Vinnia_Tracker () {
	$instance = Vinnia_Tracker::instance( __FILE__, '1.0.0' );

	if ( is_null( $instance->settings ) ) {
		$instance->settings = Vinnia_Tracker_Settings::instance( $instance );
	}

	return $instance;
}

Vinnia_Tracker();
