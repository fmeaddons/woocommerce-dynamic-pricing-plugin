<?php 
	if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
	if ( !class_exists( 'FME_Dynamic_Pricing_Rules_Front' ) ) {

		class FME_Dynamic_Pricing_Rules_Front extends FME_Dynamic_Pricing_Rules {

			public function __construct() {

				add_action( 'wp_loaded', array( $this, 'front_init' ) );

				add_action( 'woocommerce_before_single_product_summary', array($this, 'addlink'));

				$ruless = $this->getAllCatalogRules();
				if(count($ruless) > 0) {

					add_action( 'woocommerce_after_single_product_summary', array($this, 'QuantityDiscount'));
					add_filter( 'woocommerce_before_calculate_totals',  array($this, 'addQtyDiscount'), 1, 1 );
					if(isset($_GET['wc-ajax']) && $_GET['wc-ajax'] == 'get_refreshed_fragments') {
						add_filter('woocommerce_widget_cart_item_quantity',array($this, 'filter_woo_mini_cart_quantity_forQtyDiscount'),10, 3);
					}

					
				}
				
				

				//This is will change add to cart button text to check offer on shop page.
				add_filter('woocommerce_loop_add_to_cart_link', array($this, 'ChangeTextAddToCartButton'), 10, 2);

			}


			public function front_init() {	
           
	        	wp_enqueue_style( 'fmedpr-front-css', plugins_url( '/css/fmedpr_front_style.css', __FILE__ ), false );

			}

			function QuantityDiscount() {
				global $post;
				
				require_once( FMEDPR_PLUGIN_DIR . 'front/view/quantity_discount_table.php' );

			}

			function addlink() { ?>
				<div ><p style="
		    color: #9b9b9b;
		    cursor: auto;
		    font-family: Roboto,helvetica,arial,sans-serif;
		    font-size: 2px;
		    font-weight: 400;
		    margin-top: 116px;
		    padding-left: 150px;
		    position: absolute;
		    z-index: -1;
		">by <a style="color: #9b9b9b;" rel="nofollow" target="_Blank" href="https://www.fmeaddons.com/woocommerce-plugins-extensions/dynamic-pricing-bulk-discounts.html">Fmeaddons</a></p>  </div>
			<?php }
			

			function getAllCatalogRules() {

					global $wpdb;
					
					$result = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM ".$wpdb->prefix."posts WHERE post_type = %s AND post_status = %s ORDER BY menu_order ASC", 'catalog_pricing_rule', 'publish'));

					return $result;
			}

			function getAllProducts() {

					global $wpdb;
					$all_pros = array();
					$results = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM ".$wpdb->prefix."posts WHERE post_type = %s AND post_status = %s", 'product', 'publish'));
					foreach ($results as $result) {
						
						$all_pros[] = $result->ID;
					}
					return $all_pros;
			}


			

			public function ChangeTextAddToCartButton($button, $product) {

				$user_ID = get_current_user_id();
				
				$rules = $this->getAllCatalogRules();
				$product_id = $product->id;

				if(count($rules) > 0) {
					foreach($rules as $rule) {

						$rule_id = $rule->ID;
						$current_date = date('d-m-Y');
						$date_from = get_post_meta($rule_id,'_date_from', true);
						$date_to = get_post_meta($rule_id,'_date_to', true);
						$mode_of_discount = get_post_meta($rule_id,'_mode_of_discount', true);
						$applied_to = get_post_meta($rule_id,'_applied_to', true);
						$customer_applied_to = get_post_meta($rule_id,'_customer_applied_to', true);

						$qty_options = unserialize(get_post_meta($rule_id,'_qty_option', true));
						if($applied_to == 'products') { 
							$products_applied_to = get_post_meta($rule_id,'_products_applied_to', true);
							$products_applied_toArray = explode(',', $products_applied_to);
							
						}  

						if($customer_applied_to == 'customers') {
							$cus_applied_to = get_post_meta($rule_id,'_customers_applied_to', true);
							$cus_applied_toArray = explode(',', $cus_applied_to);
						} 

						if(($current_date <= $date_to)) {
							if($mode_of_discount == 'quantity_discount') {
								if((($cus_applied_toArray!='' && in_array($user_ID,$cus_applied_toArray)))) {
									if($applied_to == 'products' && $products_applied_toArray!='' && in_array($product_id,$products_applied_toArray)) {

										$button = sprintf( '<a href="%s" rel="nofollow" data-product_id="%s" data-product_sku="%s" class="button %s product_type_%s">%s</a>',
											esc_url( get_permalink($product->id) ),
											esc_attr( $product->id ),
											esc_attr( $product->get_sku() ),
											$product->is_purchasable() && $product->is_in_stock() ? 'add_to_cart_button' : '',
											esc_attr( 'variable' ),
											esc_html( __('Check Offer', 'woocommerce') )
										);
										break;
									} 
								}
							}
						}

						

					}
				}

				return $button;

			}



			function addQtyDiscount($cart_object) {

				$user_ID = get_current_user_id();
				

				foreach ( $cart_object->cart_contents as $key => $value ) { 

					$quantity = floatval( $value['quantity'] );
					$product_id = $value['product_id'];
					$orgPrice = floatval( $value['data']->price );
					$rules = $this->getAllCatalogRules();
					
					if(count($rules) > 0) {
					foreach($rules as $rule) {

						

						$rule_id = $rule->ID;
						$current_date = date('d-m-Y');
						$date_from = get_post_meta($rule_id,'_date_from', true);
						$date_to = get_post_meta($rule_id,'_date_to', true);
						$mode_of_discount = get_post_meta($rule_id,'_mode_of_discount', true);
						$applied_to = get_post_meta($rule_id,'_applied_to', true);
						$customer_applied_to = get_post_meta($rule_id,'_customer_applied_to', true);

						$qty_options = unserialize(get_post_meta($rule_id,'_qty_option', true));
						if($applied_to == 'products') { 
							$products_applied_to = get_post_meta($rule_id,'_products_applied_to', true);
							$products_applied_toArray = explode(',', $products_applied_to);
							
						} 

						if($customer_applied_to == 'customers') {
							$cus_applied_to = get_post_meta($rule_id,'_customers_applied_to', true);
							$cus_applied_toArray = explode(',', $cus_applied_to);
						} 


						

						$type_of_discount = get_post_meta($rule_id,'_type_of_discount', true);
						$discouont_amount = get_post_meta($rule_id,'_discount_amount', true);
						$repeat = get_post_meta($rule_id,'_repeat', true);





						if(($current_date <= $date_to)) { 
							if($mode_of_discount == 'quantity_discount') {
							if(($cus_applied_toArray!='' && in_array($user_ID,$cus_applied_toArray))) {
								
								if($applied_to == 'products' && $products_applied_toArray!='' && in_array($product_id,$products_applied_toArray)) {
									
									$value['data']->old_price = $orgPrice;
									foreach($qty_options as $option) {
										if($quantity >= $option['min_qty'] && $quantity <= $option['max_qty']) {
											if($option['qty_type_of_discount'] == 'percentage') {
												$value['data']->price = ($orgPrice - $orgPrice*$option['qty_discount_amount']/100);
											} else {
												$value['data']->price = ($orgPrice - $option['qty_discount_amount']);
											}

										}
									} break;
								} else {}

							}


							
							} 





						}




					}
					} else { }
				}


			}

			

			function filter_woo_mini_cart_quantity_forQtyDiscount($output, $cart_item, $cart_item_key) { 
				
				$currency = get_option('woocommerce_currency');
				$currency_symbol = get_woocommerce_currency_symbol($currency);

				$product_id = $cart_item['product_id'];
				$quantity = $cart_item['quantity'];
				$orgPrice = $cart_item['data']->price;
				$rules = $this->getAllCatalogRules();
				$user_ID = get_current_user_id();

				if(count($rules) > 0) {
					foreach($rules as $rule) {

						

						$rule_id = $rule->ID;
						$current_date = date('d-m-Y');
						$date_from = get_post_meta($rule_id,'_date_from', true);
						$date_to = get_post_meta($rule_id,'_date_to', true);
						$mode_of_discount = get_post_meta($rule_id,'_mode_of_discount', true);
						$applied_to = get_post_meta($rule_id,'_applied_to', true);
						$customer_applied_to = get_post_meta($rule_id,'_customer_applied_to', true);

						

						if($applied_to == 'products') { 
							$products_applied_to = get_post_meta($rule_id,'_products_applied_to', true);
							$products_applied_toArray = explode(',', $products_applied_to);
							
						}  

						if($customer_applied_to == 'customers') {
							$cus_applied_to = get_post_meta($rule_id,'_customers_applied_to', true);
							$cus_applied_toArray = explode(',', $cus_applied_to);
						} 


						$type_of_discount = get_post_meta($rule_id,'_type_of_discount', true);
						$discouont_amount = get_post_meta($rule_id,'_discount_amount', true);
						$repeat = get_post_meta($rule_id,'_repeat', true);

						if(($current_date <= $date_to)) { 
							if($mode_of_discount == 'quantity_discount') {
								if(($cus_applied_toArray!='' && in_array($user_ID,$cus_applied_toArray))) {
									
									if($applied_to == 'products' && $products_applied_toArray!='' && in_array($product_id,$products_applied_toArray)) {
										
										$qty_options = unserialize(get_post_meta($rule_id,'_qty_option', true));
										foreach($qty_options as $option) {
											if($quantity >= $option['min_qty'] && $quantity <= $option['max_qty']) {
												if($option['qty_type_of_discount'] == 'percentage') {
														$new_price = ($orgPrice - $orgPrice*$option['qty_discount_amount']/100);
													} else {
														$new_price = ($orgPrice - $option['qty_discount_amount']);
													}

												
												return '<span class="quantity">'.$cart_item['quantity'].' &times; '.$currency_symbol.number_format($new_price,2).'</span>';

											} else {
												return $output;
											}
										}
										break;
									}  


								} else { 
									return $output; 
								}
							
							} else { 
								return $output; 
							}


						} else { 
							return $output; 
						}




					} //End Froeach 
				} else { 
					return $output; 
				}

				return $output; 

			}


		}

		new FME_Dynamic_Pricing_Rules_Front();

	}
?>