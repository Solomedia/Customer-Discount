<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://www.solomediagroup.co/
 * @since      1.0.0
 *
 * @package    Smg_Customer_Discount
 * @subpackage Smg_Customer_Discount/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Smg_Customer_Discount
 * @subpackage Smg_Customer_Discount/public
 * @author     Moicsmarkez <mmarkez@solomediagroup.co>
 */
class Smg_Customer_Discount_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Smg_Customer_Discount_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Smg_Customer_Discount_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/smg-customer-discount-public.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Smg_Customer_Discount_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Smg_Customer_Discount_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		// wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/smg-customer-discount-public.js', array( 'jquery' ), $this->version, false );

	}
	
	private function smg_find_discount( $product_id, $customer_id = '' ){
		$user_id = $customer_id == '' ? get_current_user_id() : $customer_id;

        $smg_discount_prod_options = get_user_meta($user_id, '_smg_discounts_products', true) ;
		if(!is_array($smg_discount_prod_options) || empty($smg_discount_prod_options))
			return false;
			
			foreach($smg_discount_prod_options as $dicount_k => $discount_v){
				if( $discount_v['prod_id'] == $product_id ){
					return $discount_v;
				}
			}
		return false;
	}

	private function smg_find_discount_all_customers( $product_id ){
        $smg_discounts_all_customers = get_option('smg_discounts_all_customers', array());
		if(!is_array($smg_discounts_all_customers) || empty($smg_discounts_all_customers))
			return false;
			
			foreach($smg_discounts_all_customers as $dicount_k => $discount_v){
				if( $discount_v['prod_id'] == $product_id ){
					return $discount_v;
				}
			}
		return false;
	}
	
	
	public function smg_woocommerce_get_variation_prices_hash ( $hash, $product, $display ) {
		// Delete the cached data, if it exists
		$cache_key = 'wc_var_prices' . substr( md5( json_encode( $hash ) ), 0, 22 ) . WC_Cache_Helper::get_transient_version( 'product' );
        delete_transient($cache_key);
        // woocommerce 2.5.1
		delete_transient('wc_var_prices_' . $product->get_id());
        return $hash;
	}
	
	public function smg_add_cart_item_msg(  ){
		if( !current_user_can('customer') )
			return;
		
		global $product;
		//flags
		$c_find = false;
		$c_all_find = false;
		$print_arr_tmp = array();

		$product_type = ($terms = wp_get_object_terms( $product->get_id(), 'product_type' ))  ?  sanitize_title( current( $terms )->name ) : apply_filters( 'default_product_type', 'simple' );
		if( $product_type == 'variable' ){
			foreach ($product->get_children() as $hijo) {
				$smg_discount_prod_options = $this->smg_find_discount( $hijo );
				$smg_discount_prod_options_all = $this->smg_find_discount_all_customers( $hijo );
				if($smg_discount_prod_options)
					$c_find=true;
				
				if($smg_discount_prod_options_all)
					$c_all_find = true;

				if( !$c_find && !$c_all_find)
					continue;
				
				
				if(!$smg_discount_prod_options && $smg_discount_prod_options_all)
					$smg_discount_prod_options = $smg_discount_prod_options_all;

				$product_child = wc_get_product( $hijo );
				//ordenar array
				$this->sortBy('quantity', $smg_discount_prod_options['discounts'], 'asc');
				
				foreach ($smg_discount_prod_options['discounts'] as $dpkey => $dpvalue) {
					$discount_message = $dpvalue['message'];
					$quantity_discount = $dpvalue['quantity'];
					
					// if(empty(WC()->cart->get_cart()))
					// 	wc_add_notice( __( $discount_message.' ( in '.wc_get_formatted_variation($product_child-> get_attributes(),true, true), $this->plugin_name ).' )', 'notice' );

					foreach( WC()->cart->get_cart() as $values) { 
						if($values['variation_id'] == $hijo && $values['quantity'] >= $quantity_discount)
							$print_arr_tmp = array('msg' => $discount_message, 'attr_name' => wc_get_formatted_variation($product_child-> get_attributes(),true, true),  );
						else 
							continue;
					}
				}

				if(!empty($print_arr_tmp))
				wc_add_notice( __( $print_arr_tmp['msg'].' ( in '.$print_arr_tmp['attr_name'], $this->plugin_name ).' )', 'notice' );

				//reset flags
				$c_find = false;
				$c_all_find = false;
				$print_arr_tmp = array();
			}
		}else{//single product
			$smg_discount_prod_options = $this->smg_find_discount( $product->get_id() );
			$smg_discount_prod_options_all = $this->smg_find_discount_all_customers( $product->get_id() );
				if($smg_discount_prod_options)
					$c_find=true;
				
				if($smg_discount_prod_options_all)
					$c_all_find = true;

				if( !$c_find && !$c_all_find)
					return;
				

				if(!$smg_discount_prod_options && $smg_discount_prod_options_all)
					$smg_discount_prod_options = $smg_discount_prod_options_all;
					//ordenar array
				$this->sortBy('quantity', $smg_discount_prod_options['discounts'], 'asc');
			
			foreach ($smg_discount_prod_options['discounts'] as $dpkey => $dpvalue) {
				$discount_message = $dpvalue['message'];
				$quantity_discount = dpvalue['quantity'];
				if(empty(WC()->cart->get_cart()))
					wc_add_notice( __( $discount_message.' ( in '.wc_get_formatted_variation($product_child-> get_attributes(),true, true), $this->plugin_name ).' )', 'notice' );

				foreach( WC()->cart->get_cart() as $values) { 
					if($values['variation_id'] == $hijo && $values['quantity'] >= $quantity_discount)
						$print_arr_tmp = array('msg' => $discount_message, 'attr_name' => wc_get_formatted_variation($product_child-> get_attributes(),true, true),  );
					else
						continue;
					
					// wc_add_notice( __( $discount_message.' ( in '.wc_get_formatted_variation($product_child-> get_attributes(),true, true), $this->plugin_name ).' )', 'notice' );
				}
			}
			if(!empty($print_arr_tmp))
				wc_add_notice( __( $print_arr_tmp['msg'].' ( in '.$print_arr_tmp['attr_name'], $this->plugin_name ).' )', 'notice' );
		}
	}

	public function smg_append_discount_text( $product_name, $cart_item, $cart_item_key ){
		if( !current_user_can('customer') && !current_user_can('sales_agents_smg'))
			return $product_name;

		$item_id = $cart_item['data']->get_id(); // Product ID
		$product_type = ($terms = wp_get_object_terms( $item_id, 'product_type' ))  ?  sanitize_title( current( $terms )->name ) : apply_filters( 'default_product_type', 'simple' );
		if( $product_type == 'variable' ) 
			$item_id = $cart_item['variation_id'];// Product variable ID
		
		// $cart_qty = $cart_item['quantity'];
		$key_c = $cart_item_key;
		//flags
		$c_find = false;
		$c_all_find = false;

		$customer = '';

		if(current_user_can('sales_agents_smg')){
			foreach( WC()->cart->cart_contents as $cart_item_id=>$cart_item ) {
				$customer  = WC()->cart->cart_contents[$cart_item_id]['customer_id'];
			}
			if($customer == '')
				return $product_name;
		}

		$smg_discount_prod_options =  $this->smg_find_discount( $item_id, $customer );
		$smg_discount_prod_options_all = $this->smg_find_discount_all_customers( $item_id );
		if($smg_discount_prod_options)
			$c_find=true;
		
		if($smg_discount_prod_options_all)
			$c_all_find = true;

		if( !$c_find && !$c_all_find)
			return $product_name;
		

		if(!$smg_discount_prod_options && $smg_discount_prod_options_all)
			$smg_discount_prod_options = $smg_discount_prod_options_all;
		
		//ordenar array
		$this->sortBy('quantity', $smg_discount_prod_options['discounts'], 'asc');

		$original_price	= WC()->cart->cart_contents[$key_c]['data']->get_regular_price();
		$product = wc_get_product( $item_id );
		$product_name_append = '';
		foreach ($smg_discount_prod_options['discounts'] as $dpkey => $dpvalue) {
			$discount_type  =  $dpvalue['type'];
			$quantity_discount = $dpvalue['quantity'];
			$discount_cart_message = $dpvalue['cart_message'];
			$discount_amount = $dpvalue['amount'];

			if($discount_type === "percent"){
				// $original_price =get_post_meta($item_id, '_regular_price', true);
				$regular_price_per_product = doubleval($original_price)-( doubleval($original_price) * (doubleval($discount_amount)/100) ); 
			}elseif( $discount_type === "fixed" ) {
				$regular_price_per_product = $discount_amount; 
			}else{
				$regular_price_per_product = doubleval($original_price)-doubleval($discount_amount); 
			}
			
			if( WC()->cart->cart_contents[$key_c]['quantity'] >= $quantity_discount ){
				$product_name_append = '<br/><em>'.$discount_cart_message.'</em>';
			}	
		}
		$product_name .= $product_name_append;

		return $product_name;
	}

	public function smg_change_price_html_in_cart( $price, $cart_item, $cart_item_key ){
		if( !current_user_can('customer') && !current_user_can('sales_agents_smg') )
			return $price;

		$item_id = $cart_item['data']->get_id(); // Product ID
		$product_type = ($terms = wp_get_object_terms( $item_id, 'product_type' ))  ?  sanitize_title( current( $terms )->name ) : apply_filters( 'default_product_type', 'simple' );
		if( $product_type == 'variable' ) 
			$item_id = $cart_item['variation_id'];// Product variable ID
		
		$key_c = $cart_item_key;
		$cart_qty = WC()->cart->cart_contents[$key_c]['quantity'];
		$original_price	= WC()->cart->cart_contents[$key_c]['data']->get_regular_price();
		//flags
		$c_find = false;
		$c_all_find = false;

		$customer = '';

		if(current_user_can('sales_agents_smg')){
			foreach( WC()->cart->cart_contents as $cart_item_id=>$cart_item ) {
				$customer  = WC()->cart->cart_contents[$cart_item_id]['customer_id'];
			}
			if($customer == '')
				return $price;
		}
		
		$smg_discount_prod_options = $this->smg_find_discount( $item_id, $customer );
		$smg_discount_prod_options_all = $this->smg_find_discount_all_customers( $item_id );
		if($smg_discount_prod_options)
			$c_find=true;
		
		if($smg_discount_prod_options_all)
			$c_all_find = true;

		if( !$c_find && !$c_all_find)
			return $price;
		

		if(!$smg_discount_prod_options && $smg_discount_prod_options_all)
			$smg_discount_prod_options = $smg_discount_prod_options_all;

		//ordenar array
		$this->sortBy('quantity', $smg_discount_prod_options['discounts'], 'asc');
			
		
		$product = wc_get_product( $item_id );

		foreach ($smg_discount_prod_options['discounts'] as $dpkey => $dpvalue) {
			$discount_type  =  $dpvalue['type'];
			$quantity_discount = $dpvalue['quantity'];
			$discount_amount = $dpvalue['amount'];

			if($discount_type === "percent"){
				// $original_price	= get_post_meta($item_id, '_regular_price', true);
				$regular_price_per_product = doubleval($original_price)-( doubleval($original_price) * (doubleval($discount_amount)/100) ); 
			}elseif( $discount_type === "fixed" ) {
				$regular_price_per_product = $discount_amount; 
			}else{
				$regular_price_per_product = doubleval($original_price)-doubleval($discount_amount); 
			}
			
			if( $cart_qty >= $quantity_discount ){
				$price_tmp = $regular_price_per_product;
			}
		}
		
		if($price_tmp){
			WC()->cart->cart_contents[$key_c]['data']->set_price( $price_tmp );
			return wc_price( $price_tmp );
		}
		return $price;
	}

	public function smg_change_price_by_quantity( $cart_object ){
		if ( is_admin() )		
			return;		
	
		if( !current_user_can('customer') && !current_user_can('sales_agents_smg') )
			return;
	
		$discount_applied = false;

		$customer = '';

		if(current_user_can('sales_agents_smg')){
			foreach( WC()->cart->cart_contents as $cart_item_id=>$cart_item ) {
				$customer  = WC()->cart->cart_contents[$cart_item_id]['customer_id'];
			}
			if($customer == '')
				return;
		}
	
		// Iterating through each item in cart
		foreach ( $cart_object->get_cart() as $item_values ) {
			//  Get cart item data
			$item_id = $item_values['data']->get_id(); // Product ID
			$product_type = ($terms = wp_get_object_terms( $item_id, 'product_type' ))  ?  sanitize_title( current( $terms )->name ) : apply_filters( 'default_product_type', 'simple' );
			if( $product_type == 'variable' ) 
				$item_id = $item_values['variation_id'];// Product variable ID
	
			$c_find = false;
			$c_all_find = false;

			$smg_discount_prod_options = $this->smg_find_discount( $item_id, $customer);
			$smg_discount_prod_options_all = $this->smg_find_discount_all_customers( $item_id );
			if($smg_discount_prod_options)
				$c_find=true;
			
			if($smg_discount_prod_options_all)
				$c_all_find = true;

			if( !$c_find && !$c_all_find)
				continue;
			

			if(!$smg_discount_prod_options && $smg_discount_prod_options_all)
				$smg_discount_prod_options = $smg_discount_prod_options_all;
		
			//ordenar array
			$this->sortBy('quantity', $smg_discount_prod_options['discounts'], 'asc');

			$item_qty = $item_values['quantity']; // Item quantity
			$original_price = $item_values['data']->get_regular_price(); // Product original price
	
			// Getting the object
			$product = wc_get_product( $item_id );
			foreach ($smg_discount_prod_options['discounts'] as $dpkey => $dpvalue) {
				$discount_type  =  $dpvalue['type'];
				$quantity_discount = $dpvalue['quantity'];
				$discount_amount = $dpvalue['amount'];
		
				// CALCULATION FOR EACH ITEM 
				// when quantity is up to the targetted quantity and product is not on sale
				if( $item_qty >= $quantity_discount ){
					// for($j = $quantity_discount, $loops = 0; $j <= $item_qty; $j += $quantity_discount, $loops++);
						// $modulo_qty = $item_qty % $quantity_discount; // The remaining non discounted items
					if($discount_type === "percent"){
						$original_price	= get_post_meta($item_id, '_regular_price', true);
						$regular_price_per_product = doubleval($original_price)-( doubleval($original_price) * (doubleval($discount_amount)/100) ); 
					}elseif( $discount_type === "fixed" ) {
						$regular_price_per_product = $discount_amount; 
					}else{
						$regular_price_per_product = doubleval($original_price)-doubleval($discount_amount); 
					}
					// Setting the new price item
					$item_values['data']->set_price($regular_price_per_product);
					$discount_applied = true;
				}
			
			}
		}
		// Optionally display a message for that discount
// 		if ( $discount_applied )
// 			wc_add_notice( __( 'A quantity discount has been applied on some cart items.', $this->plugin_name ), 'success' );
	}


	public function smg_woocommerce_cart_contents(){
			foreach( WC()->cart->cart_contents as $cart_item_id=>$cart_item ) {
				$customer  = WC()->cart->cart_contents[$cart_item_id]['customer_id'];
			}
			
		?>
			<input type="hidden" id="customer_id" name="customer_id" value="<?php echo $customer != '' ? $customer : '';?>" />
			<style>
				tr.woocommerce-shipping-totals.shipping {
					display: none !important;
				}
			</style>
			<script>
				jQuery(document).on('change', 'select#form_id_client_as_shop', function(){
					jQuery('body input#customer_id').val(jQuery(this).val());
					jQuery("[name='update_cart']").removeAttr('disabled');
					jQuery("[name='update_cart']").trigger("click");
				});
			</script>
		<?php
	}

	public function smg_on_action_cart_updated( $cart_updated ){
		if ( isset( $_POST['customer_id'] ) ) {
			// $discount_applied = false;
			
			foreach( WC()->cart->cart_contents as $cart_item_id=>$cart_item ) {
				WC()->cart->cart_contents[$cart_item_id]['customer_id'] = $_POST['customer_id'];
			}
			WC()->cart->set_session();
			WC()->cart->calculate_totals();
		}
		return $cart_updated;
	}

	public function add_cart_item_data( $cart_item_data, $product_id, $variation_id  ){
		$customer = '';

		foreach( WC()->cart->cart_contents as $cart_item_id=>$cart_item ) {
			$customer  = WC()->cart->cart_contents[$cart_item_id]['customer_id'];
		}
		if($customer != ''){
			$cart_item_data[ "customer_id" ] = $customer;  
			return $cart_item_data;;
		}
			
		$cart_item_data[ "customer_id" ] = isset($_POST["customer_id"]) ? $_POST["customer_id"] : '';  
			
		return $cart_item_data;
	}

	/*********** XERO NOTES REFERENCES COLUMN *************************/
    /*
    ** Function to synchronize the comments of the orders by customer temporarily can be deleted for the publication of the plugin **
    ** Take into consideration if the plugin woocommerce-xero is updated **
    */
	
    private function replace_between($str, $needle_start, $needle_end, $replacement) {
			$pos = strpos($str, $needle_start);
			$start = $pos === false ? 0 : $pos + strlen($needle_start);

			$pos = strpos($str, $needle_end, $start);
			$end = $start === false ? strlen($str) : $pos;

			return substr_replace($str,$replacement,  $start, $end - $start);
		}

    public function smg_notes_woocommerce_xero_invoice_to_xml( $xml, $_class ){
		$order = $_class->get_order();
		if( $order ){
        	if (strpos($xml, '</Reference>') !== false) {
            	$note = $order->get_meta('_billing_myfield3') ? $order->get_meta('_billing_myfield3') : '' ;
				$xml = $this->replace_between($xml, '<Reference>', '</Reference>', $note);
                //$xml = str_replace("</Reference>", " ".$note."</Reference>", $xml);
			}
			$sales_user_id = get_user_meta($order->get_customer_id(), 'sales_agent_id', true);
			if( ( $sales_user_id != '' || $sales_user_id > 0 ) ){
				$user = get_user_by( 'ID', $sales_user_id );
				$tracking_cat = '<Tracking><TrackingCategory><Name>Sales Representative Name</Name><Option>'.$user->display_name.'</Option></TrackingCategory></Tracking>';

				$xml = str_replace("</LineItem>", $tracking_cat."</LineItem>", $xml);
			}
        }
		

        return $xml;
	}
	

	public function smg_discount_woocommerce_xero_to_xml( $line_items, $_class  ){
		$order = $_class->get_order();
		
		$customer_id = $order->get_customer_id();
		//$line_items = $_class->get_line_items();
		if(user_can( $customer_id, 'customer' ) ){
			$order = wc_get_order( $order->get_id() );

			foreach ($order->get_items() as $item) {
				$product_type = ($terms = wp_get_object_terms( $item->get_product_id(), 'product_type' ))  ?  sanitize_title( current( $terms )->name ) : apply_filters( 'default_product_type', 'simple' );
				$product = $product_type == 'variable' ? wc_get_product($item->get_variation_id()) : wc_get_product($item->get_product_id());
				foreach ( $line_items as $k_line_item => $line_item) {
					if($line_item->get_item_code() == $product->get_sku()){
						
						$c_find = false;
						$c_all_find = false;

						$smg_discount_prod_options = $this->smg_find_discount( $product->get_id(), $customer_id);
						$smg_discount_prod_options_all = $this->smg_find_discount_all_customers( $product->get_id() );
						if($smg_discount_prod_options)
							$c_find=true;
						
						if($smg_discount_prod_options_all)
							$c_all_find = true;

						if( !$c_find && !$c_all_find)
							continue;
						

						if(!$smg_discount_prod_options && $smg_discount_prod_options_all)
							$smg_discount_prod_options = $smg_discount_prod_options_all;

						//ordenar array
						$this->sortBy('quantity', $smg_discount_prod_options['discounts'], 'asc');
						$discount_aplied = false;
						$original_price	= get_post_meta($product->get_id(), '_regular_price', true);
						$user_show_percentage = get_user_meta($order->get_customer_id(), 'show_percentage_in_invoice', true) === '0' ? 0 : 1;
						foreach ($smg_discount_prod_options['discounts'] as $dpkey => $dpvalue) {
							$discount_type  =  $dpvalue['type'];
							$quantity_discount = $dpvalue['quantity'];
							$discount_amount = $dpvalue['amount'];
							 if( $line_item->get_quantity() >= $quantity_discount){
								$discount_aplied = true;
								if($discount_type === "percent"){
									$regular_price_per_product = doubleval($original_price)-( doubleval($original_price) * (doubleval($discount_amount)/100) ); 
								}elseif( $discount_type === "fixed" ) {
									$regular_price_per_product = $discount_amount; 
								}else{
									$regular_price_per_product = doubleval($original_price)-doubleval($discount_amount); 
								}

								$item_discount_as_percentage = abs( ($original_price -$regular_price_per_product) / $original_price ) * 100.0;
								$line_item->set_unit_amount( $regular_price_per_product );
								if($user_show_percentage > 0){
									$line_item->set_unit_amount( $original_price );
									$line_item->set_discount_rate( $item_discount_as_percentage );
								}
								$line_items[$k_line_item]= $line_item;
							} 
						}
						if(!$discount_aplied && $product->is_on_sale() ){

							$sale_price	= $product->get_sale_price();

							$item_discount_as_percentage = abs( ($original_price - $sale_price) / $original_price ) * 100.0;
							if($user_show_percentage > 0){
								$line_item->set_unit_amount( $original_price );
								$line_item->set_discount_rate( $item_discount_as_percentage );
							}
							$line_items[$k_line_item]= $line_item;
						}
					}    
				}	
			}
			// return $line_items;
		}

		foreach ($order->get_items() as $item) {
			$product_type = ($terms = wp_get_object_terms( $item->get_product_id(), 'product_type' ))  ?  sanitize_title( current( $terms )->name ) : apply_filters( 'default_product_type', 'simple' );
			$product = $product_type == 'variable' ? wc_get_product($item->get_variation_id()) : wc_get_product($item->get_product_id());
			foreach ( $line_items as $k_line_item => $line_item) {
				$value_xcd = $product->get_meta('_xero_new_description');
				if($line_item->get_item_code() == $product->get_sku() && ($value_xcd != '' || $value_xcd)){
					$line_item->set_description( $value_xcd );
					$line_items[$k_line_item]= $line_item;
				}
			}
		}

		$_class->set_line_items($line_items);
		return $line_items;
	}

	private function get_smg_discount_prod_options( $product_id, $customer_id = ''){
		$c_find = false;
		$c_all_find = false;

		$smg_discount_prod_options = $this->smg_find_discount( $product_id, $customer_id);
		$smg_discount_prod_options_all = $this->smg_find_discount_all_customers( $product_id );
		if($smg_discount_prod_options)
			$c_find=true;
		
		if($smg_discount_prod_options_all)
			$c_all_find = true;

		if( !$c_find && !$c_all_find)
			return false;
		// elseif($c_find && $c_all_find){
		// 	foreach ($smg_discount_prod_options_all['discounts'] as $dpkey => $dpvalue) {
		// 		foreach ($smg_discount_prod_options['discounts'] as $dpkey_real => $dpvalue_real) {
		// 			if($dpvalue_real['quantity'] == $dpvalue['quantity']){
		// 				unset($smg_discount_prod_options_all['discounts'][$dpkey]);
		// 			}
		// 		}
		// 	}
			
		// 	$discount_tmp = $smg_discount_prod_options['discounts'];
		// 	foreach ($smg_discount_prod_options_all['discounts'] as $dpkey => $dpvalue) {
		// 		array_push($discount_tmp, $dpvalue);
		// 	}
			
		// 	$smg_discount_prod_options['discounts']  = $discount_tmp;
		// }
		
		if(!$c_find && $c_all_find)
			$smg_discount_prod_options = $smg_discount_prod_options_all;

		return $smg_discount_prod_options;
	}

	private function sortBy($field, &$array, $direction = 'asc'){
		usort($array, create_function('$a, $b', '
			$a = $a["' . $field . '"];
			$b = $b["' . $field . '"];

			if ($a == $b)
			{
				return 0;
			}

			return ($a ' . ($direction == 'desc' ? '>' : '<') .' $b) ? -1 : 1;
		'));

		return true;
	}

	public function sgm_filter_dropdown_html( $html, $args ) {
		if( !current_user_can('customer'))
			return $html;

		$options               = $args['options'];
		$product               = $args['product'];
		$attribute             = $args['attribute'];
		$name                  = $args['name'] ? $args['name'] : 'attribute_' . sanitize_title( $attribute );
		$id                    = $args['id'] ? $args['id'] : sanitize_title( $attribute );
		$class                 = $args['class'];
		$show_option_none      = (bool) $args['show_option_none'];
		$show_option_none_text = __( 'Choose an option now', 'woocommerce' ); // We'll do our best to hide the placeholder, but we'll need to show something when resetting options.
	
		if ( empty( $options ) && ! empty( $product ) && ! empty( $attribute ) ) {
			$attributes = $product->get_variation_attributes();
			$options    = $attributes[ $attribute ];
		}
	
		$newhtml  = '<select id="' . esc_attr( $id ) . '" class="' . esc_attr( $class ) . '" name="' . esc_attr( $name ) . '" data-attribute_name="attribute_' . esc_attr( sanitize_title( $attribute ) ) . '" data-show_option_none="' . ( $show_option_none ? 'yes' : 'no' ) . '">';
		$newhtml   .= '<option value="">' . esc_html( $show_option_none_text ) . '</option>';
			
		if ( ! empty( $options ) ) {
			if ( $product && taxonomy_exists( $attribute ) ) {
				// Get terms if this is a taxonomy - ordered. We need the names too.
				$terms = wc_get_product_terms(
					$product->get_id(),
					$attribute,
					array(
						'fields' => 'all',
					)
				);
				foreach ( $terms as $term ) {
					if ( in_array( $term->slug, $options, true ) ) {
						$newhtml   .= '<option value="' . esc_attr( $term->slug ) . '" ' . selected( sanitize_title( $args['selected'] ), $term->slug, false ) . '>' . esc_html( apply_filters( 'woocommerce_variation_option_name', $term->name, $term, $attribute, $product ) ) . '</option>';
					}
				}
			} else {
				foreach ( $options as $option ) {
					// This handles < 2.4.0 bw compatibility where text attributes were not sanitized.
					$selected = sanitize_title( $args['selected'] ) === $args['selected'] ? selected( $args['selected'], sanitize_title( $option ), false ) : selected( $args['selected'], $option, false );
					$newhtml      .= '<option value="' . esc_attr( $option ) . '" ' . $selected . '>' . esc_html( apply_filters( 'woocommerce_variation_option_name', $option, null, $attribute, $product ) ) . '</option>';
				}   
			}
		}
			$newhtml   .= '</select>';

			$options_discount = array();
			foreach ($product->get_children() as $hijo) {
				$product_child = wc_get_product( $hijo );
				// wc_get_formatted_variation($product_child-> get_attributes(),true, false);
				$discount_prod = $this->get_smg_discount_prod_options($hijo);
				if(!$discount_prod || $discount_prod['prod_id'] != $hijo)
					continue;

				//ordenar array
				$this->sortBy('quantity', $discount_prod['discounts'], 'asc');
				
				foreach ($discount_prod['discounts'] as $dpkey => $dpvalue) {
					$original_price	= $product_child->get_regular_price();
					

					if($dpvalue['type'] === "percent"){
						$regular_price_per_product = doubleval($original_price)-( doubleval($original_price) * (doubleval($dpvalue['amount'])/100) ); 
					}elseif( $dpvalue['type'] === "fixed" ) {
						$regular_price_per_product = $dpvalue['amount']; 
					}else{
						$regular_price_per_product = doubleval($original_price)-doubleval($dpvalue['amount']); 
					}
					$tmp_arr = array(
						'qty_discount' => $dpvalue['quantity'],
						'original_price' => $product_child->get_price() < $regular_price_per_product ? $product_child->get_regular_price() :  $product_child->get_price(),
						'discount_price' => $regular_price_per_product,
						'attr_name' => wc_get_formatted_variation($product_child-> get_attributes(),true, false),
						'trigger_type' =>  !isset($dpvalue['trigger']) ? 0 : $dpvalue['trigger']
					);
					array_push($options_discount, $tmp_arr);
				}
				// print_r( $options_discount );
			}

			ob_start();
			?>
				<div class="smg_discount_by_variation" style="display: none;margin: 10px 0px;">
					<!--<span style="color: #000;"><b><em>Take advantage of the special offer</em></b></span> <br/>-->
					<?php $key_ant = -1; foreach ($options_discount as $okey => $ovalue) : ?>
						<?php if($ovalue['trigger_type'] > 0) { ?>
							<input type="hidden" class="inmediate-<?php echo $ovalue['attr_name'];?>" value="" data-qty="<?php echo $ovalue['qty_discount'];?>" data-qty-discount-price="<?php echo strip_tags(wc_price(floatval($ovalue['discount_price'])));?>" data-original-price="<?php echo strip_tags(wc_price(floatval($ovalue['original_price'])));?>" />
							<?php $key_ant = $okey; ?>
						<?php continue; }  ?>
						<?php if($tmpattr == '' || $tmpattr != $ovalue['attr_name']) { 
						$tmpattr = $ovalue['attr_name']; ?>
						<div class="smg_control-group group-off-<?php echo str_replace('.','-',$tmpattr);?>">
						<?php } ?>
						<?php $price_org_tmp = $key_ant >= 0 ? strip_tags(wc_price(floatval($options_discount[$key_ant]['discount_price'])))  : strip_tags(wc_price(floatval($ovalue['original_price']))); ?>
						<?php $key_ant = $okey; ?>
						<label class="smg_control smg_control-checkbox off-<?php echo $ovalue['attr_name'].'_'.$okey;?>" for="off-<?php echo $ovalue['qty_discount'].'-'.$okey;?>">
						<?php $attr_name =  strpos($ovalue['attr_name'], 'cc') !== false ? ' vial': ' unit' ; ?>
							"<?php echo $ovalue['qty_discount'];?>+ vials bulk order discount from <?php echo $price_org_tmp;?> to <?php echo strip_tags(wc_price(floatval($ovalue['discount_price']))); ?>  per<?php echo $attr_name; ?>” 
							<input class="smg-discounts-list" id="off-<?php echo $ovalue['qty_discount'].'-'.$okey;?>" value="qty-<?php echo $ovalue['qty_discount'];?>" name="smg_discount_qty" type="checkbox" data-qty="<?php echo $ovalue['qty_discount'];?>" data-qty-discount-price="<?php echo strip_tags(wc_price(floatval($ovalue['discount_price'])));?>" data-original-price="<?php echo strip_tags(wc_price(floatval($ovalue['original_price'])));?>"  >  
							<div class="smg_control_indicator"></div>
						</label>
						<?php if( $options_discount[$okey+1]['attr_name'] != $ovalue['attr_name']) { $key_ant = -1; ?>
						</div>
						<?php } ?>
					<?php endforeach; ?>
					
				</div>
				<script >
					jQuery.fn.outerHTML = function() {
						return jQuery('<div />').append(this.eq(0).clone()).html();
					};

					jQuery(document).ready(function($){
						//blank field price on load page
						$('.quantity > input[name="quantity"]').val(1);
						
						//select checkboxes change price
						$(document).on('change', '.smg-discounts-list', function() {
							$('.smg-discounts-list').not(this).prop('checked', false); 
							if($('form.variations_form.cart .woocommerce-variation-price > span.price > del').length && $('form.variations_form.cart .woocommerce-variation-price > span.price > ins').length) {
								let currency_html = $("form.variations_form.cart .woocommerce-variation-price > span.price > ins > .woocommerce-Price-currencySymbol").outerHTML();
								$('form.variations_form.cart .woocommerce-variation-price > span.price > ins > .woocommerce-Price-amount.amount').html($(this).data('qty-discount-price'));
								$('.single_variation_wrap .quantity > input[name="quantity"]').val($(this).data('qty'));
								if (!$('.smg-discounts-list:checked').length) {
									let attr_slct = $('select[name^="attribute"]').val();
									if($('.inmediate-'+attr_slct).length)
										$('.single_variation_wrap .price > ins .woocommerce-Price-amount.amount').html(currency_html+$('.inmediate-'+attr_slct).data('qty-discount-price'));
									else
										$('.single_variation_wrap .price > ins .woocommerce-Price-amount.amount').html(currency_html+$(this).data('original-price'));
									$('.single_variation_wrap .quantity > input[name="quantity"]').val(1);
								}
							}else{
								let currency_html = $(".woocommerce-Price-currencySymbol").outerHTML();
								$('.single_variation_wrap .price > .woocommerce-Price-amount.amount').html($(this).data('qty-discount-price'));
								$('.single_variation_wrap .quantity > input[name="quantity"]').val($(this).data('qty'));
								if (!$('.smg-discounts-list:checked').length) {
									let attr_slct = $('select[name^="attribute"]').val();
									if($('.inmediate-'+attr_slct).length)
										$('.single_variation_wrap .price > .woocommerce-Price-amount.amount').html(currency_html+$('.inmediate-'+attr_slct).data('qty-discount-price'));
									else
										$('.single_variation_wrap .price > .woocommerce-Price-amount.amount').html(currency_html+$(this).data('original-price'));
									$('.single_variation_wrap .quantity > input[name="quantity"]').val(1);
								}
							}
						});
						//select variation show checkboxes
						$(document).on('change', 'select[name^="attribute"]', function(){
							let tmp_arr = [<?php foreach ($options_discount as $key => $value){  if ($key < (count($options_discount)-1)){ echo '"'.$value['attr_name'].'",'; }else {  echo '"'.$value['attr_name'].'"'; }} ?>];
							if(tmp_arr.indexOf($(this).val()) > -1 ){
								if($('.inmediate-'+$(this).val()).length){
									 if($('form.variations_form.cart .woocommerce-variation-price > span.price > del').length && $('form.variations_form.cart .woocommerce-variation-price > span.price > ins').length) {
										let currency_html = $("form.variations_form.cart .woocommerce-variation-price > span.price > ins > .woocommerce-Price-currencySymbol").outerHTML();
										$('form.variations_form.cart .woocommerce-variation-price > span.price > ins > .woocommerce-Price-amount.amount').html($('.inmediate-'+$(this).val()).data('qty-discount-price'));
									}else{
										let currency_html = $(".woocommerce-Price-currencySymbol").outerHTML();
										$('.single_variation_wrap .price > .woocommerce-Price-amount.amount').html($('.inmediate-'+$(this).val()).data('qty-discount-price'));
									}
								}
								$('.smg_discount_by_variation').slideDown('slow');
								let tmpstr = $(this).val();
								tmpstr = tmpstr.replace('.','-');
								$('.single_variation_wrap .quantity > input[name="quantity"]').val(1);
								$('div[class*=" group-off-"]').css('display','none');
								$('div[class*=" group-off-"]').removeClass('showing-disc');
								$('.smg_control-group.group-off-'+tmpstr).css('display','block');
								$('.smg_control-group.group-off-'+tmpstr).addClass('showing-disc');
							} else {
								$('.smg-discounts-list').prop('checked', false);  
								$('.single_variation_wrap .quantity > input[name="quantity"]').val(1);
								$('.smg_discount_by_variation').slideUp('slow');
								$('div[class*=" group-off-"]').css('display','none');
							}
						});
						$('select[name^="attribute"]').val('');
						$('form.variations_form.cart').trigger('reset_data');
					});
				</script>
			<?php
			
			$brown_html =ob_get_clean();
			$newhtml   .= $brown_html;
				
			return $newhtml  ;
	}
	

}
