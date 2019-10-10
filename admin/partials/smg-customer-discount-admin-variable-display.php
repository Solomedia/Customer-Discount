<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://www.solomediagroup.co/
 * @since      1.0.0
 *
 * @package    Smg_Customer_Discount
 * @subpackage Smg_Customer_Discount/admin/partials
 */
?>
<div id='product_d_customer_panel' class='panel woocommerce_options_panel'>
    <div class="options_group" style="display: flex;flex-flow: row wrap;">
    <?php
        $product = wc_get_product( $post->ID );
    ?>
    <div class="par-opciones customers">
        <h4 style="width: 90%;text-align: center;margin: 5px 0px;text-decoration: underline;font-size: 16px;">Doctor (customer) and Product</h4>
        <div class="optiones" style="flex: auto;margin: 0 10px 10px;  max-width: 35%;" >
            <?php
                $users = get_users( 'orderby=nicename&role=customer' );
                if ( ! $users ) {
            ?>
            <div>
                    <label for="customer_id" style="float: none;width: auto; margin: 0;" ><?php _e( 'Existing Customer', 'smg-customer-discount' ); ?></label>
                    <div><?php _e( 'You must create although one (1) user with customer role.', 'smg-customer-discount' ); ?></div>
                <?php
                } else {
            ?>
                <div>
                    <label for="customer_id" style="float: none;width: auto; margin: 0;" ><b><?php _e( 'Existing Customer', 'smg-customer-discount' ) ?></b></label>
                </div>
                <div>
                    <div>
                        <select name="customer_id" id="customer_id" class="wc-enhanced-select regular-text" style="width: 230px;">
                        <?php 
                            echo '<option value="">' . __( 'Search a Doctor', 'smg-customer-discount' ) . '</option>';
                            echo '<option value="all_customers">' . __( 'Alls Doctors', 'smg-customer-discount' ) . '</option>';
                            foreach ( $users as $user ) {
                            echo '<option value="' . $user->ID . '" >' . esc_html( ucwords( $user->display_name ) ) . '</option>';
                        }
                        ?>
                        </select>
                    </div>
                    <br />
                    <small><em><?php _e( 'Customer to make the discount.', 'smg-customer-discount' ) ?></em></small>
                </div>
            <?php
                }
            ?>
        </div>
        <div class="optiones" style="flex: auto;margin: 0 10px 10px;  max-width: 55%;" >
                <div>
                    <label for="post_id_var" style="float: none;width: auto; margin: 0;" ><b><?php _e( 'Product Variation ID', 'smg-customer-discountnts' ) ?></b></label>
                </div>
                <select name="post_id_var" id="post_id_var" class="wc-enhanced-select regular-text" style="width: 100%">
                    <option value="">#ID</option>;
                    <?php 
                        /*<option value="<?php echo $hijo;?>" >#<?php echo $hijo;?></option>;*/
                        foreach ($product->get_children() as $hijo) {?>
                            <?php $product_child = wc_get_product( $hijo ); ?>
                            <?php $price = $product_child->is_on_sale() ? $product_child->get_regular_price() : $product_child->get_price() ;?>
                            <option value="<?php echo $hijo;?>" > <?php echo $hijo.' - '.wp_trim_words(wc_get_formatted_variation($product_child-> get_attributes(),true, false),2,'...').' (price: '.$price.get_woocommerce_currency_symbol().')'; ?></option>;
                        <?php }
                    ?>
                    
                </select>
                <small style="display: block;"><em>Choose which product variation should have the discount</em></small>
        </div>
    </div>
    <div class="par-opciones discounts">
        <h4 style="width: 90%;text-align: center;margin: 5px 0px;text-decoration: underline;font-size: 16px;">Discount (type, trigger and amount)</h4>
        <div class="optiones" style="flex: auto;margin: 0 10px 10px;  max-width: 25%;" >
            <div>
                <label for="type_discount" style="float: none;width: auto; margin: 0;" ><b><?php _e( 'Type of discount', 'smg-customer-discountnts' ) ?></b></label>
            </div>
            <div>
                <select name="type_discount" id="type_discount" class="wc-enhanced-select regular-text" style="width: 100%;">
                    <option value="fixed">Fixed</option>;
                    <option value="manually" >Manually</option>;
                    <option value="percent" >Percentage</option>;
                </select>
                <br />
                <small><em><?php _e( 'It can be manual or percentage based.', 'smg-customer-discountnts' ) ?></em></small>
            </div>
        </div>
        <div class="optiones" style="flex: auto;margin: 0 10px 10px;  max-width: 20%;" >
            <div>
                <label for="type_trigger" style="float: none;width: auto; margin: 0;" ><b><?php _e( 'Type of trigger', 'smg-customer-discountnts' ) ?></b></label>
            </div>
            <div>
                <select name="type_trigger" id="type_trigger" class="wc-enhanced-select regular-text" style="width: 100%;">
                    <option value="1">Immediate</option>;
                    <option value="0" >Greater than</option>;
                </select>
                <br />
                <small><em><?php _e( 'Trigger, can be immediate or for an amount greater than.', 'smg-customer-discountnts' ) ?></em></small>
            </div>
        </div>
        <div class="optiones" style="flex: auto;margin: 0 10px 10px;  max-width: 20%;">
            <div>
                <label for="quantity_discount" style="float: none;width: auto; margin: 0;" ><b><?php _e( 'Amount greater than', 'smg-customer-discountnts' ) ?></b></label>
            </div>
            <div style="display: flex;flex-flow: column;">
                <div style="display: block;">
                    <input id="quantity_discount" name="quantity_discount" type="number" step="1" value="0" min="0" style="width: 100%" disabled="disabled">
                </div>
                <small style="display: block;"><em>quantity greater than, to make the discount per product</em></small>
            </div>
        </div>
        <div class="optiones" style="flex: auto;margin: 0 10px 10px;  max-width: 20%;"  >
            <div>
                <label for="discount_amount" style="float: none;width: auto; margin: 0;" ><b><?php _e( 'Discount amount', 'smg-customer-discountnts' ) ?></b></label>
            </div>
            <div style="display: flex;flex-flow: column;">
                <div style="display: block;">
                    <input id="discount_amount" name="discount_amount" type="number" step="0.01" value="0" min="0" style="width: 100%;">
                </div>
                <small style="display: block;"><em>Amount or percentage remaining from the final price</em></small>
            </div>
        </div>
    </div>
    <div class="par-opciones messages-save">
        <h4 style="width: 90%;text-align: center;margin: 5px 0px;text-decoration: underline;font-size: 16px;">Messagess and save</h4>
        <div class="optiones" style="flex: auto;margin: 0 10px 10px;  max-width: 40%;" >
            <div>
                <label for="discount_message" style="float: none;width: auto; margin: 0;" ><b><?php _e( 'Message add to cart discount', 'smg-customer-discountnts' ) ?></b></label>
            </div>
            <div style="display: flex;flex-flow: column;">
                <div style="display: block;">
                    <input id="discount_message" name="discount_message" type="text" value="" placeholder="eg: save (995-975) per vial if your order 10+" style="width: 100%;padding: 5px;border-radius: 5px;">
                </div>
                <small style="display: block;"><em>Message that the customer will see when buying the product after matching or exceeding the discount</em></small>
            </div>
        </div>
        <div class="optiones" style="flex: auto;margin: 0 10px 10px;  max-width: 40%;" >
            <div>
                <label for="discount_cart_message" style="float: none;width: auto; margin: 0;" ><b><?php _e( 'Discount Applied Message - Cart', 'smg-customer-discountnts' ) ?></b></label>
            </div>
            <div style="display: flex;flex-flow: column;">
                <div style="display: block;">
                    <input id="discount_cart_message" name="discount_cart_message" type="text" value="" placeholder="eg: You got the discount" style="width: 100%;padding: 5px;border-radius: 5px;">
                </div>
                <small style="display: block;"><em>Message with discount price below the product name in the cart list.</em></small>
            </div>
        </div>
        <div class="optiones" style="flex: auto;margin: 0 10px 10px;max-width: 15%;display: flex;align-items: center;justify-content: flex-end;" >
                <input type="button" id="add_discount" class="button button-primary button-large" style="margin-right: 15px;" value="Add" />
        </div>
    </div>
        <!-- fin option groups -->
    </div>
        <a href="#0" class="button button-primary button-large js-cd-panel-trigger" data-panel="main" style="display: block;margin: 15px auto 40px;width: 190px;text-align: center;font-size: 15px;">
            <div class="content-a">
                <span style="font-weight: 700;">Show all discounts</span>
            </div>
        </a>
        <!-- table result -->
        <div class="cd-panel cd-panel--from-right js-cd-panel-main">
            <header class="cd-panel__header">
                <h1><b>ALL REGISTERED DISCOUNTS</b></h1>
                <a href="#0" class="cd-panel__close js-cd-close">Close</a>
            </header>
            <div class="cd-panel__container">
                <div class="cd-panel__content">
                    <div style="padding: 10px 0px;margin-left: 5px;">
                        <input value="" onkeyup="searchCustomerFunction()" id="search_customer" placeholder="Filter by content row..." style="border-radius: 5px;border: 1px solid #ededed;padding: 5px;width: 97%;">
                    </div>
                    <div class="divTable">
                        <div class="divTableBody">
                            <div class="divTableHeader">
                                <div class="header__item"><a id="customer" class="filter__link" href="#">Doctor</a></div>
                                <div class="header__item"><a id="variation_id" class="filter__link" href="#">Product Variation ID</a></div>
                                <div class="header__item"><a id="quantity_discount" class="filter__link" href="#">Quantity Discount</a></div>
                                <div class="header__item"><a id="type" class="filter__link " href="#">Type</a></div>
                                <div class="header__item"><a id="discount" class="filter__link " href="#">Discount Amount</a></div>
                                <div class="header__item"><span>Messages</span></div>
                                <div class="header__item"><a id="final_price" class="filter__link " href="#">Final price</a></div>
                                <div class="header__item"><span>Remove</span></div>
                            </div>
                        <div class="table-content">
                        <?php
                        $empty_table = true;
                        foreach ($product->get_children() as $hijo) {
                            $product_child = wc_get_product( $hijo );
                            $customer_in_products = $product_child->get_meta('_smg_customers_in_discounts');
                            $smg_discounts_all_customers = get_option('smg_discounts_all_customers', array());

                            if( is_array($customer_in_products) || !empty($customer_in_products) || $smg_discounts_all_customers && is_array($smg_discounts_all_customers) || !empty($smg_discounts_all_customers) ) {
                                $empty_table = false;
                            }else{
                                continue;
                            }

                            $price = $product_child->is_on_sale() ? $product_child->get_regular_price() : $product_child->get_price() ;
                            if($customer_in_products && count($customer_in_products) > 0) {                        
                                foreach ($customer_in_products as $key => $value) {
                                    $user_info = get_userdata($value);

                                    $smg_discount_prod_options = get_user_meta($value, '_smg_discounts_products', true) ;
                                    foreach($smg_discount_prod_options as $pd){
                                        if(($pdkey = array_search($hijo, $pd)) !== false){
                                            $smg_discount_prod_options = $pd;
                                            break;
                                        }    
                                    } 
                                    foreach($smg_discount_prod_options['discounts'] as $discount_v){
                                        $discount_type  = $discount_v['type'];
                                        $type_trigger = $discount_v['trigger'];
                                        $quantity_discount = $discount_v['quantity'];
                                        $discount_message = $discount_v['message'];
                                        $discount_cart_message = $discount_v['cart_message'];
                                        $discount_amount = $discount_v['amount'];
                                        if($discount_type === "percent"){ $regular_price_per_product = doubleval($price)-( doubleval($price) * (doubleval($discount_amount)/100) ); }elseif( $discount_type === "fixed" ) { $regular_price_per_product = $discount_amount; }else{ $regular_price_per_product = doubleval($price)-doubleval($discount_amount); }
                                        ?><div class="divTableRow">
                                            <div class="divTableCell table-data"><?php echo $user_info->display_name; ?></div>
                                            <div class="divTableCell table-data"><?php echo $hijo.'-'.wp_trim_words(wc_get_formatted_variation($product_child-> get_attributes(),true, false),2,'...'); ?></div>
                                            <div class="divTableCell table-data"><?php echo $type_trigger != 1 ? $quantity_discount : 'Immediate'; ?></div>
                                            <div class="divTableCell table-data"><?php echo ucwords($discount_type); ?></div>
                                            <div class="divTableCell table-data"><?php echo $discount_amount; echo $discount_type === 'percent' ? '%' : get_woocommerce_currency_symbol() ; ?></div>
                                            <div class="divTableCell table-data msg" style="display: flex;" >
                                                <input style=" width: 80px; margin-bottom: 5px;" data-msg="<?php echo $discount_message;?>" type="button" class="button button-large" value="Msg page" />
                                                <input style=" width: 80px; margin-bottom: 5px;" data-msg="<?php echo $discount_cart_message;?>" type="button" class="button button-large" value="Msg Cart" />
                                            </div>
                                            <div class="divTableCell table-data" title="<?php echo  $product_child->is_on_sale() ? 'Sale price ' : 'Normal price ' ; echo $price.get_woocommerce_currency_symbol();?>"><?php echo $regular_price_per_product.get_woocommerce_currency_symbol(); ?></div>
                                            <div class="divTableCell">
                                                <div class="button remove_discoun_customer" data-element-qty="<?php echo $quantity_discount?>" data-element-id="<?php echo $value; ?>" data-element-varid="<?php echo $hijo; ?>">
                                                    <span class="dashicons dashicons-trash"></span>
                                                    <div class="target-discount-<?php echo '_'.$product_child->get_id(); ?> ">
                                                        <span><b>Are you sure?</b></span>
                                                        <br>
                                                        <span class="confirm-yes"> Yeah! </span><span class="confirm-no"> Nop! </span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <?php
                                    }
                                }
                            }//Finalize customer in products
                            
                            // Si encuentra descuentos en todos lo usuarios
                            foreach($smg_discounts_all_customers as $pd){
                                if(($pdkey = array_search($hijo, $pd)) !== false){
                                    $smg_discounts_all_customers = $pd;
                                    break;
                                }    
                            } 
                            foreach($smg_discounts_all_customers['discounts'] as $discount_v){
                                $discount_type  = $discount_v['type'];
                                $type_trigger = $discount_v['trigger'];
                                $quantity_discount = $discount_v['quantity'];
                                $discount_message = $discount_v['message'];
                                $discount_cart_message = $discount_v['cart_message'];
                                $discount_amount = $discount_v['amount'];
                                if($discount_type === "percent"){ $regular_price_per_product = doubleval($price)-( doubleval($price) * (doubleval($discount_amount)/100) ); }elseif( $discount_type === "fixed" ) { $regular_price_per_product = $discount_amount; }else{ $regular_price_per_product = doubleval($price)-doubleval($discount_amount); }
                                ?><div class="divTableRow">
                                    <div class="divTableCell table-data">All Doctors</div>
                                    <div class="divTableCell table-data"><?php echo $hijo.'-'.wp_trim_words(wc_get_formatted_variation($product_child-> get_attributes(),true, false),2,'...'); ?></div>
                                    <div class="divTableCell table-data"><?php echo $type_trigger != 1 ? $quantity_discount : 'Immediate'; ?></div>
                                    <div class="divTableCell table-data"><?php echo ucwords($discount_type); ?></div>
                                    <div class="divTableCell table-data"><?php echo $discount_amount; echo $discount_type === 'percent' ? '%' : get_woocommerce_currency_symbol() ; ?></div>
                                    <div class="divTableCell table-data msg" style="display: flex;" >
                                        <input style=" width: 80px; margin-bottom: 5px;" data-msg="<?php echo $discount_message;?>" type="button" class="button button-large" value="Msg page" />
                                        <input style=" width: 80px; margin-bottom: 5px;" data-msg="<?php echo $discount_cart_message;?>" type="button" class="button button-large" value="Msg Cart" />
                                    </div>
                                    <div class="divTableCell table-data" title="<?php echo  $product_child->is_on_sale() ? 'Sale price ' : 'Normal price ' ; echo $price.get_woocommerce_currency_symbol();?>"><?php echo $regular_price_per_product.get_woocommerce_currency_symbol(); ?></div>
                                    <div class="divTableCell">
                                        <div class="button remove_discoun_customer" data-element-qty="<?php echo $quantity_discount?>" data-element-id="all_customers" data-element-varid="<?php echo $hijo; ?>">
                                            <span class="dashicons dashicons-trash"></span>
                                            <div class="target-discount-<?php echo '_'.$product_child->get_id(); ?> ">
                                                <span><b>Are you sure?</b></span>
                                                <br>
                                                <span class="confirm-yes"> Yeah! </span><span class="confirm-no"> Nop! </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php
                            }
                        }//Finalize foreach prudct child
                        if($empty_table){
                            ?>
                            <div class="divTableRow empty_reg_event_kwe_ac" >
                                <div  style="padding: 15px;">
                                    <em style="color: #a5a5a5;">There are no registered customers.</em>
                                </div>
                            </div>    
                            <?php
                        }
                        ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
            
            </div>
		<div id="price_original"><em>Original front view price: </em><?php echo $product->get_price_html();?></em></div>
        <?php wp_nonce_field('dfn91234d', 'nonce_discount_save', true, true); ?>
        </div>

<?php
