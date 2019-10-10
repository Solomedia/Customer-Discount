<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://www.solomediagroup.co/
 * @since      1.0.0
 *
 * @package    Smg_Customer_Discount
 * @subpackage Smg_Customer_Discount/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Smg_Customer_Discount
 * @subpackage Smg_Customer_Discount/includes
 * @author     Moicsmarkez <mmarkez@solomediagroup.co>
 */
class Smg_Customer_Discount {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Smg_Customer_Discount_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		if ( defined( 'SMG_CUSTOMER_DISCOUNT_VERSION' ) ) {
			$this->version = SMG_CUSTOMER_DISCOUNT_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = 'smg-customer-discount';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Smg_Customer_Discount_Loader. Orchestrates the hooks of the plugin.
	 * - Smg_Customer_Discount_i18n. Defines internationalization functionality.
	 * - Smg_Customer_Discount_Admin. Defines all hooks for the admin area.
	 * - Smg_Customer_Discount_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-smg-customer-discount-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-smg-customer-discount-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-smg-customer-discount-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-smg-customer-discount-public.php';

		$this->loader = new Smg_Customer_Discount_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Smg_Customer_Discount_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Smg_Customer_Discount_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new Smg_Customer_Discount_Admin( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );
		
		$this->loader->add_action( 'woocommerce_product_data_tabs', $plugin_admin, 'smg_xcd');
        $this->loader->add_action( 'woocommerce_product_data_panels', $plugin_admin, 'smg_display_xcd');
		$this->loader->add_action( 'wp_ajax_smg_saved_xcd', $plugin_admin, 'smg_saved_xcd');
		
		$this->loader->add_action( 'woocommerce_product_data_tabs', $plugin_admin, 'smg_discount_producto_customer');
        $this->loader->add_action( 'woocommerce_product_data_panels', $plugin_admin, 'smg_display_discount_product_fields');
		
		$this->loader->add_action( 'wp_ajax_smg_saved_discount', $plugin_admin, 'smg_saved_discount');
		$this->loader->add_action( 'wp_ajax_smg_remove_discount', $plugin_admin, 'smg_remove_discount');

	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new Smg_Customer_Discount_Public( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );

		$this->loader->add_action( 'woocommerce_update_cart_action_cart_updated', $plugin_public, 'smg_on_action_cart_updated', 20, 1 );
		$this->loader->add_action( 'woocommerce_cart_contents', $plugin_public, 'smg_woocommerce_cart_contents', 10, 0 );

		$this->loader->add_filter( 'woocommerce_add_cart_item_data', $plugin_public, 'add_cart_item_data', 99, 3 );

		/*********** XERO NOTES REFERENCES *************************/
		$this->loader->add_filter( 'woocommerce_xero_invoice_to_xml', $plugin_public, 'smg_notes_woocommerce_xero_invoice_to_xml', 0, 2);
		$this->loader->add_filter( 'woocommerce_xero_invoice_line_items', $plugin_public,  'smg_discount_woocommerce_xero_to_xml', 1, 2);
		
		$this->loader->add_filter( 'woocommerce_dropdown_variation_attribute_options_html', $plugin_public, 'sgm_filter_dropdown_html', 99, 2 );


		$this->loader->add_filter( 'woocommerce_before_calculate_totals', $plugin_public, 'smg_change_price_by_quantity', 20, 1 );
		$this->loader->add_filter( 'woocommerce_cart_item_name', $plugin_public, 'smg_append_discount_text', 20, 3 );
		//$this->loader->add_action( 'woocommerce_before_single_product', $plugin_public, 'smg_add_cart_item_msg', 10 ); 
		$this->loader->add_action( 'woocommerce_cart_item_price', $plugin_public, 'smg_change_price_html_in_cart', 10, 3 ); 
		
		
		//cache price reset
        $this->loader->add_filter('woocommerce_get_variation_prices_hash', $plugin_public, 'smg_woocommerce_get_variation_prices_hash' , 10, 3);


	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Smg_Customer_Discount_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

}
