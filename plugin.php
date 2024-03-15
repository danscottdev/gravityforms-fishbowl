<?php
/**
 * Plugin Name: GravityForms Fishbowl Integration
 * Description: Enables GravityForms entries to submit to Fishbowl's CRM Platform
 * Version: 1.1
 * Author: Dan Scott
 * Author URI: https://danscott.dev
 * Author Email: danscott2150@gmail.com
 *
 */

namespace GF_Fishbowl;

define( 'GF_FISHBOWL_PATH', plugin_dir_path( __FILE__ ) );
define( 'GF_FISHBOWL_URL', plugin_dir_url( __FILE__ ) );

if ( ! defined( 'WPINC' ) ) {
	die( 'Nope' );
}

use GF_Fishbowl\Integration;
use GF_Fishbowl\Settings;


add_action( 'plugins_loaded', function() {
	require_once GF_FISHBOWL_PATH . '/class-integration.php';
	require_once GF_FISHBOWL_PATH . '/class-settings.php';
	require_once GF_FISHBOWL_PATH . '/class-meta-boxes.php';
	require_once GF_FISHBOWL_PATH . '/class-utils.php';
	require_once GF_FISHBOWL_PATH . '/class-validation.php';
	new Integration();
	new Settings();
	new Meta_Boxes();
	new Validation();
} );

// On plugin activation: Adds custom 'CRM Status' field to all forms.
// This field is not mission-critical, but adds a helpful layer of visibility to any entries that may not have posted.
require_once GF_FISHBOWL_PATH . '/class-activation.php';
new Activation();
register_activation_hook( __FILE__, array( 'GF_Fishbowl\Activation', 'activate' ) );
