<?php 
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( !class_exists( 'FME_Dynamic_Pricing_Rules_Admin' ) ) {

	class FME_Dynamic_Pricing_Rules_Admin extends FME_Dynamic_Pricing_Rules {

		public function __construct() {

			add_action( 'wp_loaded', array( $this, 'admin_init' ) );
			
			
			add_action('wp_ajax_searchData', array($this, 'searchData')); 
			add_action('wp_ajax_searchCustomerData', array($this, 'searchCustomerData')); 
			add_action('wp_ajax_searchRolesData', array($this, 'searchRolesData'));
			add_action('wp_ajax_catalog_rule_form', array($this, 'process_catalog_rule_form'));
			add_action('wp_ajax_deleteCatalogRule', array($this, 'deleteCatalogRule'));

			add_action('wp_ajax_cart_rule_form', array($this, 'process_cart_rule_form'));
			add_action('wp_ajax_deleteCartRule', array($this, 'deleteCartRule'));


		}

		public function admin_init() {
			add_action( 'admin_menu', array( $this, 'create_admin_menu' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'admin_scripts' ) );	
		}

		public function create_admin_menu() {	
			add_menu_page('Dynamic Pricing Rules', __( 'DPR', 'fmedpr' ), null, 'fmeaddon-dynamic-pricing-rules', array( $this, 'fmedpr_catalog_pricing_rules_module' ) ,plugins_url( 'images/fma.jpg', dirname( __FILE__ ) ), apply_filters( 'fmedpr_menu_position', 30 ) );
			add_submenu_page( 'fmeaddon-dynamic-pricing-rules', __( 'Catalog Pricing Rules', 'fmedpr' ), __( 'Catalog Pricing Rules', 'fmedpr' ), 'manage_options', 'fmeaddon-catalog-pricing-rules', array( $this, 'fmedpr_catalog_pricing_rules_module' ) );	
			add_submenu_page( 'fmeaddon-dynamic-pricing-rules', __( 'Cart Pricing Rules', 'fmedpr' ), __( 'Cart Pricing Rules', 'fmedpr' ), 'manage_options', 'fmeaddon-cart-pricing-rules', array( $this, 'fmedpr_cart_pricing_rules_module' ) );	
			//add_submenu_page( 'fmeaddon-dynamic-pricing-rules', __( 'Settings', 'fmedpr' ), __( 'Settings', 'fmedpr' ), 'manage_options', 'fmedpr_settings', array( $this, 'fmedpr_mdoule_settings' ) );	

	        //register_setting( 'fmedpr_settings', 'fmedpr_settings', array( $this, 'fmedpr_settings' ) );

	    }

	    function fmedpr_catalog_pricing_rules_module() {

	    	require_once( FMEDPR_PLUGIN_DIR . 'admin/view/catalog_pricing_rules.php' );
	    }

	    function fmedpr_cart_pricing_rules_module() {
	    	
	    	require_once( FMEDPR_PLUGIN_DIR . 'admin/view/cart_pricing_rules.php' );
	    }

	    public function fmedpr_mdoule_settings() {
	    	echo "Setting Page";
			//require  FMEDPR_PLUGIN_DIR . 'admin/view/settings.php';
		}

		public function admin_scripts() {	
           
        	wp_enqueue_style( 'fmedpr-admin-css', plugins_url( '/css/fmedpr_style.css', __FILE__ ), false );
        	

			//select2 css and js
			wp_enqueue_script('jquery');
			wp_enqueue_style( 'fmpiw-select2-css', plugins_url( '/css/select2.min.css', __FILE__ ), false );
			wp_enqueue_style( 'fmpiw-select2-bscss', plugins_url( '/css/select2-bootstrap.css', __FILE__ ), false );
        	wp_enqueue_script( 'fmepiw-select2-js', plugins_url( '/js/select2.min.js', __FILE__ ), false);

        	//Date Picker
        	
 	 		wp_enqueue_script( 'jquery-ui-datepicker');
  			wp_enqueue_style( 'jquery-ui-css', plugins_url( '/css/jquery-ui.css', __FILE__ ), false );

		}

		function searchData() {

			if(isset($_POST['q']) && $_POST['q']!='') {
				$q = sanitize_text_field($_POST['q']);
			} else { $q = ''; }

			if(isset($_POST['v']) && $_POST['v']!='') {
				$value = sanitize_text_field($_POST['v']);
			} else { $value = ''; }
			
			global $wpdb;
			
			

        		$result = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM ".$wpdb->prefix."posts  
	                  WHERE (post_type = %s OR post_type = %s) AND post_status = %s AND 
	            	post_title LIKE %s", 'product', 'product_variation', 'publish', '%' . $q . '%'));

        		foreach ($result as $res) {
	            	 
        			$img = wp_get_attachment_url( get_post_thumbnail_id($res->ID, 'thumbnail'));
        			
	            	 if($img!='') {
	            	 	$image = $img;
	            	 } else {

	            	 	$image = FMEPIW_URL.'/images/no_image.png';
	            	 }

	            	 $aa[] = array('id' => $res->ID, 'name' => $res->post_title, 'image' => $image, 'total_count' => count($result));
	            }
	            //$bb = array('items' => $aa);
	            echo json_encode($aa);

        	
            

			die();
		}


		



		function searchCustomerData() {

			if(isset($_POST['q']) && $_POST['q']!='') {
				$q = sanitize_text_field($_POST['q']);
			} else { $q = ''; }

			if(isset($_POST['v']) && $_POST['v']!='') {
				$value = sanitize_text_field($_POST['v']);
			} else { $value = ''; }
			global $wpdb;
			
			

			$aa = ''; 
			$bb = '';
            $result = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM ".$wpdb->prefix."users WHERE user_email LIKE %s", '%' . $q . '%'));      
            foreach ($result as $res) {
            	
            	 $aa[] = array('id' => $res->ID, 'name' => $res->display_name, 'email' => $res->user_email, 'total_count' => count($result));
            }
            //$bb = array('items' => $aa);
            echo json_encode($aa);

            die();

		}
		


		

		

		function process_catalog_rule_form() {

			if ( empty($_POST) || !wp_verify_nonce($_POST['123catalogruleform123'],'catalog_rule_form') ) {
			    echo 'You targeted the right function, but sorry, your nonce did not verify.';
			    die();
			} else {

				//echo "<pre>";
				//print_r($_POST['rule_data']);
				//echo count($_POST['rule_data']);
				//exit();

				

				foreach($_POST['rule_data'] as $rule_data) {

					$catids = '';
					$proids = '';
					$cusids = '';
					$rolesids = '';

					$adjusted_catids = '';
					$adjusted_proids = '';

					if(isset($rule_data['rule_id']) && $rule_data['rule_id']!='') {
						$postid = intval($rule_data['rule_id']);
					} else {
						$postid = '';
					}

					//General Info
					$rule_name = sanitize_text_field($rule_data['name']);
					$priority = intval($rule_data['priority']);
					$mode_of_discount = sanitize_text_field($rule_data['mode_of_discount']);
					$date_from = sanitize_text_field($rule_data['date_from']);
					$date_to = sanitize_text_field($rule_data['date_to']);
					$status = sanitize_text_field($rule_data['status']);

					//Conditions
					$applied_to = sanitize_text_field($rule_data['applied_to']);
					$customer_applied_to = sanitize_text_field($rule_data['customer_customer_roles']);

					if($applied_to == 'products') {

						$products = $rule_data['pro_name'];
						$pro_prefix = '';
						if($products!='') {
							foreach ($products as $product)
							{
							    $proids .= $pro_prefix.$product;
							    $pro_prefix = ',';
							}
							$ProID_applied_to = $proids;
						}
						

					}


					if($customer_applied_to == 'customers') {

						$customers = $rule_data['customer_name'];
						$cus_prefix = '';
						if($customers!='') {
							foreach ($customers as $customer)
							{
							    $cusids .= $cus_prefix.$customer;
							    $cus_prefix = ',';
							}
							$CusID_applied_to = $cusids;
						}
						
					} 


					//Actions
					//Quantity Discount
					$qty_option= serialize($rule_data['qty_option']);

					
					if(isset($rule_data['type_of_discount']) && $rule_data['type_of_discount']!='') {
						$type_of_discount = sanitize_text_field($rule_data['type_of_discount']);
					} else { $type_of_discount = ''; }

					if(isset($rule_data['discount_amount']) && $rule_data['discount_amount']!='') {
						$discount_amount = sanitize_text_field($rule_data['discount_amount']);
					} else { $discount_amount = 0; }
					


					if(isset($postid) && $postid!='') {
						
						if($rule_name!='') {
							$post_data = array(
			                                   	
			                'ID' 			  => $postid,                                    
	                        'post_title'      => $rule_name,
	                        'post_status'     => $status,
	                        'post_modified'   => date('Y-m-d h:i:s'),
	                        'menu_order'      => $priority,

		                    );
		                    wp_update_post( $post_data );

		                    update_post_meta($postid,'_mode_of_discount', $mode_of_discount);
		                    update_post_meta($postid,'_date_to', $date_to);
		                    update_post_meta($postid,'_date_from', $date_from);

		                    update_post_meta($postid,'_applied_to', $applied_to);
		                    update_post_meta($postid,'_customer_applied_to', $customer_applied_to);

		                    update_post_meta($postid,'_products_applied_to', $ProID_applied_to);
		                    update_post_meta($postid,'_customers_applied_to', $CusID_applied_to);

		                    update_post_meta($postid,'_qty_option', $qty_option);



		                    update_post_meta($postid,'_type_of_discount', $type_of_discount);
		                    update_post_meta($postid,'_discount_amount', $discount_amount);
		             		

		             		
	             		}
	             		

					} else { 

					if($rule_name!='') {
						$post_data = array(
			                                                    
	                        'post_title'      => $rule_name,
	                        'post_status'     => $status,
	                        'post_date'       => date('Y-m-d h:i:s'),
	                        'post_modified'   => date('Y-m-d h:i:s'),
	                        'post_type'     => 'catalog_pricing_rule',
	                        'menu_order'      => $priority,

	                    );
	                    $post_id = wp_insert_post( $post_data );

	                    add_post_meta($post_id,'_mode_of_discount', $mode_of_discount);
	                    add_post_meta($post_id,'_date_to', $date_to);
	                    add_post_meta($post_id,'_date_from', $date_from);

	                    add_post_meta($post_id,'_applied_to', $applied_to);
	                    add_post_meta($post_id,'_customer_applied_to', $customer_applied_to);

	                    add_post_meta($post_id,'_products_applied_to', $ProID_applied_to);
	                    add_post_meta($post_id,'_customers_applied_to', $CusID_applied_to);

	                    add_post_meta($post_id,'_qty_option', $qty_option);



	                    add_post_meta($post_id,'_type_of_discount', $type_of_discount);
	                    add_post_meta($post_id,'_discount_amount', $discount_amount);
	             		

	             		
             		}



             	}

             		


				}

				// do your function here 
			    wp_redirect('admin.php?page=fmeaddon-catalog-pricing-rules');

			}

			die();
		}


		function getAllCatalogRules() {

			global $wpdb;
			
			$result = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM ".$wpdb->prefix."posts WHERE post_type = %s", 'catalog_pricing_rule'));

			return $result;
		}

		function deleteCatalogRule() {

			global $wpdb;
			$post_id = intval($_POST['rule_id']);

			wp_delete_post( $post_id, true );
			
            delete_post_meta($post_id,'_mode_of_discount');
            
            delete_post_meta($post_id,'_date_to');
            delete_post_meta($post_id,'_date_from');
            
            delete_post_meta($post_id,'_applied_to');
            delete_post_meta($post_id,'_customer_applied_to');

            delete_post_meta($post_id,'_products_applied_to');
            delete_post_meta($post_id,'_customers_applied_to');

            delete_post_meta($post_id,'_qty_option');


            delete_post_meta($post_id,'_type_of_discount');
            delete_post_meta($post_id,'_discount_amount');
     		

			die();
			return true;
		}

		

		


		



	}

	new FME_Dynamic_Pricing_Rules_Admin();
}

?>