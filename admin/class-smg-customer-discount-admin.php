<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://www.solomediagroup.co/
 * @since      1.0.0
 *
 * @package    Smg_Customer_Discount
 * @subpackage Smg_Customer_Discount/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Smg_Customer_Discount
 * @subpackage Smg_Customer_Discount/admin
 * @author     Moicsmarkez <mmarkez@solomediagroup.co>
 */
class Smg_Customer_Discount_Admin {

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
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

	    if('product' === get_post_type()){
		    wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/smg-customer-discount-admin.css', array(), $this->version, 'all' );
        }
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		if('product' === get_post_type()){
		    wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/smg-customer-discount-admin.js', array( 'jquery' ), $this->version, true );
        }
	}
		
	public function smg_discount_producto_customer( $tabs ) {
        if(!current_user_can('administrator')) return $tabs;
        $tabs['product_d_customer'] = array(
            'label'   => __( 'Discount customer\'s', 'smg-sales-agents' ), // The name of your panel
            'target'  => 'product_d_customer_panel', // Will be used to create an anchor link so needs to be unique
            'class'   => array( 'attribute_options','product_d_customer_tab', 'show_if_simple', 'show_if_variable' ), // Class for your panel tab - helps hide/show depending on product type
            'priority'=> 60, // Where your panel will appear. By default, 70 is last item
        );
        return $tabs;
    }
    
    public function smg_display_discount_product_fields() { 
        if(!current_user_can('administrator')) return;
        global $woocommerce, $post;

        $product_type = ($terms = wp_get_object_terms( $post->ID, 'product_type' ))  ?  sanitize_title( current( $terms )->name ) : apply_filters( 'default_product_type', 'simple' );
        if( $product_type == 'variable'){
            require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/partials/smg-customer-discount-admin-variable-display.php';            
        }else{
            require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/partials/smg-customer-discount-admin-simple-display.php';
        }
    }
	
	/***************** xero decripti1on *******************/
    public function smg_xcd( $tabs ) {
        if(!current_user_can('administrator')) return $tabs;
        $tabs['product_xcd'] = array(
            'label'   => __( 'Xero Custom Description', 'smg-sales-agents' ), // The name of your panel
            'target'  => 'product_xcd_panel', // Will be used to create an anchor link so needs to be unique
            'class'   => array( 'attribute_options','product_xcd_tab', 'show_if_simple', 'show_if_variable' ), // Class for your panel tab - helps hide/show depending on product type
            'priority'=> 60, // Where your panel will appear. By default, 70 is last item
        );
        return $tabs;
    }
    
    public function smg_display_xcd() { 
        if(!current_user_can('administrator')) return;
        global $woocommerce, $post;

        $product_type = ($terms = wp_get_object_terms( $post->ID, 'product_type' ))  ?  sanitize_title( current( $terms )->name ) : apply_filters( 'default_product_type', 'simple' );
        
        if( $product_type == 'variable'){
        $product = wc_get_product( $post->ID );
        ?>
        <div id='product_xcd_panel' class='panel woocommerce_options_panel'>
            <div class="options_group" style="display: flex;flex-flow: row wrap;"> 
                <h2 style="margin: 0 auto;">Customize Description XERO invoice by product</h2>
                <br/>
                <?php foreach($product->get_children() as $hijo){ ?>
                <?php $value_xcd =  get_post_meta($hijo, '_xero_new_description', true); ?>
                <h4 class="accordion-toggle" style="width: 100%;">
                    <span class="dashicons dashicons-edit"></span> 
                    <?php echo get_the_title( $hijo );?>
                    <span class="icon  indicador-tab-on" style="font-size:25px; line-height: 25px;"></span>
                </h4>
                <div class="accordion-content" style="display: none;" data-element-id="<?php echo $hijo;?>">
                    <label style="float: none; margin: 0;" for=""><em><b>New Description: </b></em></label>
                    <input style="float: none;margin: 0;width: 100%;padding: 5px;border-radius: 5px;" type="text" placeholder="Default is:<?php echo get_the_title( $hijo ); ?> + attributes" value="<?php echo $value_xcd !='' ? $value_xcd : ''; ?>" ?>
                    <input type="button" value="Save description" style="margin-top: 5px; float: right;" class="action_saved_xcd button button-primary button-large" />
                </div>
                <?php } ?>
                <?php wp_nonce_field('dfn91234dxcd', 'nonce_xcd_save', true, true); ?>
            </div>
        </div>
        <?php
        }else{
        ?>
            <div id='product_xcd_panel' class='panel woocommerce_options_panel'>
            <div class="options_group" style="display: flex;flex-flow: row wrap;"> 
                <h2 style="margin: 0 auto;">Customize Description XERO invoice by product</h2>
                <br/>
                <h4 class="accordion-toggle" style="width: 100%;">
                    <span class="dashicons dashicons-edit"></span> 
                    <?php echo get_the_title(  $post->ID  );?>
                    <span class="icon  indicador-tab-off" style="font-size:25px; line-height: 25px;"></span>
                </h4>
                <?php $value_xcd =  get_post_meta($post->ID , '_xero_new_description', true); ?>
                <div style="width: 100%;" class="accordion-content" data-element-id="<?php echo $post->ID;?>">
                    <label style="float: none; margin: 0;" for=""><em><b>New Description: </b></em></label>
                    <input style="float: none;margin: 0;width: 100%;padding: 5px;border-radius: 5px;" type="text" placeholder="Default is:<?php echo get_the_title( $hijo ); ?> + attributes" value="<?php echo $value_xcd !='' ? $value_xcd : ''; ?>" ?>
                    <input type="button" value="Save description" style="margin-top: 5px; float: right;" class="action_saved_xcd button button-primary button-large" />
                </div>
            
            </div>
            <?php wp_nonce_field('dfn91234dxcd', 'nonce_xcd_save', true, true); ?>
        </div>
        <?php
        }
    }

    public function smg_saved_xcd(){
        //verifico nonce
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'dfn91234dxcd')) {
            wp_send_json_error('Process invalid, please refresh current page!');
            wp_die();
        }
        //verifico no declaracion
        if( !isset($_POST['post_id'])  || !isset($_POST['element_id']) ){
            wp_send_json_error('Process invalid, no field processed!');
            wp_die();
        }
        // verifico no null
        if( empty($_POST['post_id'])  || empty($_POST['element_id']) ){
            wp_send_json_error('Process invalid, no product found, please check form!');
            wp_die();
        }

        // $product_type = ($terms = wp_get_object_terms( $_POST['post_id'], 'product_type' ))  ?  sanitize_title( current( $terms )->name ) : apply_filters( 'default_product_type', 'simple' );

        $product = wc_get_product($_POST['element_id']) ;
        $new_xcd = $_POST['new_xcd'];
        
        if(empty($new_xcd) || $new_xcd == '')
            $product->delete_meta_data('_xero_new_description');    
        else
            $product->update_meta_data('_xero_new_description', $new_xcd);

        $product->save();
        
        wp_send_json_success('New decription has saved!');
        wp_die();
    }

    /***************** end xero decription *******************/

    private function construct_new_array_option(  $product_id, $discount_message, $discount_cart_message, $discount_amount, $discount_type, $type_trigger, $quantity_discount ) {
        $saved_discount []= array(
            // 'user_id' => $customer_id,
            'prod_id' => $product_id,
            'discounts' => array(array(
                'message' => $discount_message,
                'cart_message' => $discount_cart_message,
                'amount' => $discount_amount,
                'type' => $discount_type,
                'trigger' => $type_trigger,
                'quantity' => $quantity_discount 
            ))
        );

        return $saved_discount;
    }
        
    public function smg_saved_discount(){
        //verifico nonce
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'dfn91234d')) {
            wp_send_json_error('Process invalid, please refresh current page!');
            wp_die();
        }
        //verifico no declaracion
        if( !isset($_POST['customer_id'])  || !isset($_POST['discount_cart_message']) || !isset($_POST['discount_message']) || !isset($_POST['quantity_discount'])  || !isset($_POST['post_id']) || !isset($_POST['type_discount']) || !isset($_POST['discount_amount']) ){
            wp_send_json_error('Process invalid, no field processed!');
            wp_die();
        }
        //verifico no null
        if( empty($_POST['customer_id'])  || empty($_POST['discount_cart_message']) || empty($_POST['discount_message'])  || empty($_POST['post_id']) || empty($_POST['type_discount']) || empty($_POST['discount_amount']) ){
            wp_send_json_error('Process invalid, field entery null, please check form!');
            wp_die();
        }

        if( $_POST['type_trigger'] != 1  && empty($_POST['quantity_discount']) ){
            wp_send_json_error('Process invalid, field entery null, please check form!');
            wp_die();
        }

        $product_type = ($terms = wp_get_object_terms( $_POST['post_id'], 'product_type' ))  ?  sanitize_title( current( $terms )->name ) : apply_filters( 'default_product_type', 'simple' );
        if( $product_type == 'variable'){
            //verifico no null y no declaracion id producto variable
            if(empty($_POST['post_var_id']) || !isset($_POST['post_var_id']) ){
                wp_send_json_error('Process invalid, field entery null, please check form! - 2');
                wp_die();
            }
        }
        
        $customer_id = $_POST['customer_id'];
        $type_trigger = $_POST['type_trigger'];
        $product = $product_type == 'variable' ? wc_get_product($_POST['post_var_id']) : wc_get_product($_POST['post_id']);
        $quantity_discount = intval($type_trigger) != 1 ? intval($_POST['quantity_discount']) : 1 ;
        $discount_message = $_POST['discount_message'];
        $discount_cart_message = $_POST['discount_cart_message'];
        $discount_amount = floatval($_POST['discount_amount']);
        $discount_type = $_POST['type_discount'];

        //verifico quantity discount no sea menor que cero
        if( $type_trigger != 1 && $quantity_discount <= 0 ){
            wp_send_json_error('Process invalid, The amount of discount per product although you should reach one (1).');
            wp_die();
        }

        //verifico discount no sea mayor al price
        if($product->get_price() <  $discount_amount && ($discount_type != 'percent' && $discount_type != 'fixed') ){
            wp_send_json_error('Process invalid, Discount cannot be higher than the price.');
            wp_die();
        }

        //verifico discount sea porcentaje no sea mayor a 100 o 0
        if($discount_type === 'percent' && ($discount_amount > 100 || $discount_amount < 0) ) {
            wp_send_json_error('Process invalid, Percentage discount cannot be greater than 100 or less than zero.');
            wp_die();
        }
        if($customer_id === 'all_customers'){
            //Agregar funcion para all       
            $this->smg_all_customers_discount_applied( $product, $quantity_discount, $type_trigger, $discount_message, $discount_cart_message, $discount_amount, $discount_type);
        }elseif( $customer_id > 0 ){
            //Funcion para agregar descuento al doctor
            $this->smg_customer_discount_applied($customer_id, $product, $quantity_discount, $type_trigger, $discount_message, $discount_cart_message, $discount_amount, $discount_type);    
        }

        ob_start();
        $user_info = $customer_id === 'all_customers' ? 'All Doctors' : get_userdata($customer_id);
        $price = $product->is_on_sale() ? $product->get_regular_price() : $product->get_price() ;
        if($discount_type === "percent"){ $regular_price_per_product = doubleval($price)-( doubleval($price) * (doubleval($discount_amount)/100) ); }elseif( $discount_type === "fixed" ) { $regular_price_per_product = $discount_amount; }else{ $regular_price_per_product = doubleval($price)-doubleval($discount_amount); }
        ?>
        <div class="divTableRow">
            <div class="divTableCell table-data"><?php echo $customer_id === 'all_customers' ? 'All Doctors' : $user_info->display_name; ?></div>
            <?php if($product_type == 'variable') echo '<div class="divTableCell table-data">'.$product->get_id().'-'.wp_trim_words(wc_get_formatted_variation($product-> get_attributes(),true, false),2,'...').'</div>'?> 
            <div class="divTableCell table-data"><?php echo $type_trigger !=1 ?  $quantity_discount : 'Immediate'; ?></div>
            <div class="divTableCell table-data"><?php echo ucwords($discount_type); ?></div>
            <div class="divTableCell table-data"><?php echo $discount_amount; echo $discount_type === 'percent' ? '%' : get_woocommerce_currency_symbol(); ?></div>
            <div class="divTableCell table-data msg" style="display: flex;" >
                <input style=" width: 80px; margin-bottom: 5px;" data-msg="<?php echo $discount_message;?>" type="button" class="button button-large" value="Msg page" />
                <input style=" width: 80px; margin-bottom: 5px;" data-msg="<?php echo $discount_cart_message;?>" type="button" class="button button-large" value="Msg Cart" />
            </div>
            <div class="divTableCell table-data" title="<?php echo  $product->is_on_sale() ? 'Sale price ' : 'Normal price ' ; echo $price.get_woocommerce_currency_symbol();?>"><?php echo $regular_price_per_product.get_woocommerce_currency_symbol(); ?></div>
            <div class="divTableCell">
                <div class="button remove_discoun_customer" data-element-qty="<?php echo $quantity_discount?>" data-element-id="<?php echo $customer_id; ?>" data-element-varid="<?php echo $product->get_id(); ?>" >
                <span class="dashicons dashicons-trash"></span>
                    <div class="target-discount-<?php echo '_'.$product->get_id(); ?> ">
                        <span><b>Are you sure?</b></span>
                        <br>
                        <span class="confirm-yes"> Yeah! </span><span class="confirm-no"> Nop! </span>
                    </div>
                </div>
            </div>
        </div>
        <?php
        wp_send_json_success(ob_get_clean());
        wp_die();
    }
        
    public function smg_remove_discount(){
        //verifico nonce
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'dfn91234d')) {
            wp_send_json_error('Process new date invalid, please refresh current page! - 1');
            wp_die();
        }
        //verifico no declaracion
        if(!isset($_POST['discount_qty'])  || !isset($_POST['customer_id'])  || !isset($_POST['post_id']) ){
            wp_send_json_error('Process new date invalid, please refresh current page! - 2');
            wp_die();
        }
        //verifico no null
        if(empty($_POST['discount_qty'])  || empty($_POST['customer_id'])  || empty($_POST['post_id']) ){
            wp_send_json_error('Process new date invalid, please refresh current page! - 3');
            wp_die();
        }

        $product_type = ($terms = wp_get_object_terms( $_POST['post_id'], 'product_type' ))  ?  sanitize_title( current( $terms )->name ) : apply_filters( 'default_product_type', 'simple' );
        if($product_type == 'variable' && (empty($_POST['rem_post_var_id'])  || !isset($_POST['rem_post_var_id'])) ) {
            wp_send_json_error('Process new date invalid, please refresh current page! - 4');
            wp_die();
        }

        $customer_id = $_POST['customer_id'];
        $product =  $product_type == 'variable' ? wc_get_product($_POST['rem_post_var_id']) : wc_get_product($_POST['post_id']);
        $quantity_discount = intval($_POST['discount_qty']);

        //colocar funciona adicionales
        if($customer_id === 'all_customers'){
            //Agregar funcion para all       
            $this->sm_remove_all_option_customer_d( $product, $quantity_discount );
        }elseif( $customer_id > 0 ){
            //Funcion para agregar descuento al doctor
            $this->sm_remove_customer_discount($customer_id, $product, $quantity_discount);    
        }
        
        wp_send_json_success('exito');
        wp_die();

    }
    
    //funcion para agregar descuento al customer
    private function smg_customer_discount_applied($customer_id, $product, $quantity_discount, $type_trigger, $discount_message, $discount_cart_message, $discount_amount, $discount_type){
        $nunca = true;
        // Crear option para cantidades usuario y productos
        $smg_discount_prod_options = get_user_meta($customer_id, '_smg_discounts_products', true) ;
        if ($smg_discount_prod_options && is_array($smg_discount_prod_options) && count($smg_discount_prod_options)) {
            foreach ($smg_discount_prod_options as $option_k => $option_v) {
                if( in_array($product->get_id(), $option_v) ){
                    // debe agregar solo cantidad
                    if(!array_key_exists('0', $option_v['discounts'] )){
                        $option_v['discounts']  = array($option_v['discounts']);
                    }
                    foreach ($option_v['discounts'] as $discount_k => $discount_v) {
                        if( $discount_v['quantity'] != $quantity_discount  ){
                            continue;
                        }
                        wp_send_json_error('Process invalid, There is already a discount for this product. Please Changue value quantity');
                        wp_die();   
                    }                    
                    $nunca = false;
                    $discount_tmp = $option_v['discounts'];
                    $discount_new = array(
                        'message' => $discount_message,
                        'cart_message' => $discount_cart_message,
                        'amount' => $discount_amount,
                        'type' => $discount_type,
                        'trigger' => $type_trigger,
                        'quantity' => $quantity_discount 
                    );
                    array_push($discount_tmp, $discount_new);
                    $option_v['discounts'] = $discount_tmp;
                    $smg_discount_prod_options[$option_k] = $option_v;
                    $saved_discount = $smg_discount_prod_options;
                    break;
                }
            }
            if($nunca){
            // elseif(in_array($customer_id, $option_v) && !in_array($product->get_id(), $option_v) && $nunca  ){
                // debe agregar un nuevo array option
                $saved_discount = $this->construct_new_array_option(  $product->get_id(), $discount_message, $discount_cart_message, $discount_amount, $discount_type, $type_trigger, $quantity_discount );
                array_push($smg_discount_prod_options, $saved_discount[0]);
                $saved_discount = $smg_discount_prod_options;
            // }
            }
        }else{
            $saved_discount = $this->construct_new_array_option(  $product->get_id(), $discount_message, $discount_cart_message, $discount_amount, $discount_type, $type_trigger, $quantity_discount );
        }
        update_user_meta($customer_id, '_smg_discounts_products', $saved_discount);
        
        $customer_in_products = $product->get_meta( '_smg_customers_in_discounts' );
       
        if(is_array($customer_in_products)){
            if(!in_array($customer_id, $customer_in_products)){
                array_push($customer_in_products, $customer_id);
                $product->update_meta_data( '_smg_customers_in_discounts', $customer_in_products  );
            }
        }else{
            if( empty($customer_in_products) || $customer_in_products == '' ){
                $customer_in_products = array($customer_id);
                $product->update_meta_data( '_smg_customers_in_discounts', $customer_in_products  );
            }
        }
        $product->save();
    }

    //funcion para agregar descuento a todos los customers
    private function smg_all_customers_discount_applied( $product, $quantity_discount, $type_trigger, $discount_message, $discount_cart_message, $discount_amount, $discount_type){
        $nunca = true;
        // Crear option para cantidades usuario y productos
        $smg_discounts_all_customers = get_option('smg_discounts_all_customers', array());
        
        // $smg_discount_prod_options = get_user_meta($customer_id, '_smg_discounts_products', true) ;

        if ($smg_discounts_all_customers && is_array($smg_discounts_all_customers) && count($smg_discounts_all_customers)) {
            foreach ($smg_discounts_all_customers as $option_k => $option_v) {
                if( in_array($product->get_id(), $option_v) ){
                    // debe agregar solo cantidad
                    if(!array_key_exists('0', $option_v['discounts'] )){
                        $option_v['discounts']  = array($option_v['discounts']);
                    }
                    foreach ($option_v['discounts'] as $discount_k => $discount_v) {
                        if( $discount_v['quantity'] != $quantity_discount  ){
                            continue;
                        }
                        wp_send_json_error('Process invalid, There is already a discount for this product. Please Changue value quantity');
                        wp_die();   
                    }                    
                    $nunca = false;
                    $discount_tmp = $option_v['discounts'];
                    $discount_new = array(
                        'message' => $discount_message,
                        'cart_message' => $discount_cart_message,
                        'amount' => $discount_amount,
                        'type' => $discount_type,
                        'trigger' => $type_trigger,
                        'quantity' => $quantity_discount 
                    );
                    array_push($discount_tmp, $discount_new);
                    $option_v['discounts'] = $discount_tmp;
                    $smg_discounts_all_customers[$option_k] = $option_v;
                    $saved_discount = $smg_discounts_all_customers;
                    break;
                }
            }
            if($nunca){
            // elseif(in_array($customer_id, $option_v) && !in_array($product->get_id(), $option_v) && $nunca  ){
                // debe agregar un nuevo array option
                $saved_discount = $this->construct_new_array_option(  $product->get_id(), $discount_message, $discount_cart_message, $discount_amount, $discount_type, $type_trigger, $quantity_discount );
                array_push($smg_discounts_all_customers, $saved_discount[0]);
                $saved_discount = $smg_discounts_all_customers;
            // }
            }
        }else{
            $saved_discount = $this->construct_new_array_option(  $product->get_id(), $discount_message, $discount_cart_message, $discount_amount, $discount_type, $type_trigger, $quantity_discount );
        }
        update_option('smg_discounts_all_customers', $saved_discount, false);
        
        // $customer_in_products = $product->get_meta( '_smg_customers_in_discounts' );
       
        // if(is_array($customer_in_products)){
        //     if(!in_array($customer_id, $customer_in_products)){
        //         array_push($customer_in_products, $customer_id);
        //         $product->update_meta_data( '_smg_customers_in_discounts', $customer_in_products  );
        //     }
        // }else{
        //     if( empty($customer_in_products) || $customer_in_products == '' ){
        //         $customer_in_products = array($customer_id);
        //         $product->update_meta_data( '_smg_customers_in_discounts', $customer_in_products  );
        //     }
        // }
        // $product->save();
    }

    private function sm_remove_customer_discount($customer_id, $product, $quantity_discount){
        
        $customer_in_products = $product->get_meta( '_smg_customers_in_discounts' );
        $smg_discount_prod_options = get_user_meta($customer_id, '_smg_discounts_products', true) ;
        
        foreach($smg_discount_prod_options as $dicount_k => $discount_v){
            if( $discount_v['prod_id'] == $product->get_id()){
                foreach ($discount_v['discounts'] as $dpkey => $dpvalue) {
                    if( $dpvalue['quantity'] == $quantity_discount){
                        unset($discount_v['discounts'][$dpkey]);
                        $smg_discount_prod_options[$dicount_k]['discounts'] = $discount_v['discounts'];
                    }

                }
                if( !count($discount_v['discounts']) ){
                    if(is_array($customer_in_products) && $customer_in_products && ($key = array_search($customer_id, $customer_in_products)) !== false ){
                        unset($customer_in_products[$key]);
                    }
                    unset($smg_discount_prod_options[$dicount_k]);
                    $product->update_meta_data( '_smg_customers_in_discounts', $customer_in_products  );
                    $product->save();
                }
            }
        }
        update_user_meta($customer_id, '_smg_discounts_products', $smg_discount_prod_options);
    }

    private function sm_remove_all_option_customer_d($product, $quantity_discount){
        
        // $customer_in_products = $product->get_meta( '_smg_customers_in_discounts' );
        
        $smg_discounts_all_customers = get_option('smg_discounts_all_customers', array());

        // $smg_discount_prod_options = get_user_meta($customer_id, '_smg_discounts_products', true) ;
        
        foreach($smg_discounts_all_customers as $dicount_k => $discount_v){
            if( $discount_v['prod_id'] == $product->get_id()){
                foreach ($discount_v['discounts'] as $dpkey => $dpvalue) {
                    if( $dpvalue['quantity'] == $quantity_discount){
                        unset($discount_v['discounts'][$dpkey]);
                        $smg_discounts_all_customers[$dicount_k]['discounts'] = $discount_v['discounts'];
                    }

                }
                if( !count($discount_v['discounts']) ){
                    unset($smg_discounts_all_customers[$dicount_k]);
                }
            }
        }
        update_option('smg_discounts_all_customers', $smg_discounts_all_customers, false);
    }

    
}
