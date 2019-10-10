<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://www.solomediagroup.co/
 * @since             1.0.0
 * @package           Smg_Customer_Discount
 *
 * @wordpress-plugin
 * Plugin Name:       Woo - Customer Dicounts
 * Plugin URI:        https://www.solomediagroup.co/
 * Description:       Make discounts for specific customers on specific offers by percentage or fixed price.
 * Version:           1.3.87
 * Author:            SMG
 * Author URI:        https://www.solomediagroup.co/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       smg-customer-discount
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'SMG_CUSTOMER_DISCOUNT_VERSION', '1.3.87' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-smg-customer-discount-activator.php
 */
function activate_smg_customer_discount() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-smg-customer-discount-activator.php';
	Smg_Customer_Discount_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-smg-customer-discount-deactivator.php
 */
function deactivate_smg_customer_discount() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-smg-customer-discount-deactivator.php';
	Smg_Customer_Discount_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_smg_customer_discount' );
register_deactivation_hook( __FILE__, 'deactivate_smg_customer_discount' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-smg-customer-discount.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_smg_customer_discount() {

	$plugin = new Smg_Customer_Discount();
	$plugin->run();

}
run_smg_customer_discount();
