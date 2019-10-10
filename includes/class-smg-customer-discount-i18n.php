<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       https://www.solomediagroup.co/
 * @since      1.0.0
 *
 * @package    Smg_Customer_Discount
 * @subpackage Smg_Customer_Discount/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Smg_Customer_Discount
 * @subpackage Smg_Customer_Discount/includes
 * @author     Moicsmarkez <mmarkez@solomediagroup.co>
 */
class Smg_Customer_Discount_i18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'smg-customer-discount',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}



}
