<?php 
/*
 * Plugin Name:       Dynamic Pricing Rules(Free)
 * Plugin URI:        https://www.fmeaddons.com/woocommerce-plugins-extensions/dynamic-pricing-bulk-discounts.html
 * Description:       FME Dynamic Pricing Rules provide facility to create pricing rules for the products and on shopping cart and apply discount on that.
 * Version:           1.0.1
 * Author:            FME Addons
 * Developed By:  	  Raja Usman Mehmood
 * Author URI:        http://fmeaddons.com/
 * Support:		  	  http://support.fmeaddons.com/
 * Text Domain:       fmedpr
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Domain Path:       /languages
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Check if WooCommerce is active
 * if wooCommerce is not active FME Dynamic Pricing Rules module will not work.
 **/
if ( !in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {

	
	function my_admin_notice() {

		// Deactivate the plugin
		   deactivate_plugins(__FILE__);
	$error_message = __('This plugin requires <a href="http://wordpress.org/extend/plugins/woocommerce/">WooCommerce</a> plugin to be installed and active!', 'woocommerce');
	die($error_message);
}
add_action( 'admin_notices', 'my_admin_notice' );
}

if ( !class_exists( 'FME_Dynamic_Pricing_Rules' ) ) {

	class FME_Dynamic_Pricing_Rules {

		function __construct() {

			$this->module_constants();
			add_action( 'wp_loaded', array( $this, 'init' ) );
			if ( is_admin() ) {
				add_filter( 'plugin_action_links_' . plugin_basename(__FILE__), array($this, 'plugin_action_links' ));
				require_once( FMEDPR_PLUGIN_DIR . 'admin/class-fme-dynamic-pricing-rules-admin.php' );
				add_filter( 'admin_footer_text', array( $this, 'admin_footer_text' ), 1 );
				add_action('wp_ajax_fme_dynamic_pricing_rules_rated', array($this, 'fme_dynamic_pricing_rules_rated')); 
			} else {

				require_once( FMEDPR_PLUGIN_DIR . 'front/class-fme-dynamic-pricing-rules-front.php' );
			}
		}

		public function module_constants() {


            if ( !defined( 'FMEDPR_URL' ) )
                define( 'FMEDPR_URL', plugin_dir_url( __FILE__ ) );

            if ( !defined( 'FMEDPR_BASENAME' ) )
                define( 'FMEDPR_BASENAME', plugin_basename( __FILE__ ) );

            if ( ! defined( 'FMEDPR_PLUGIN_DIR' ) )
                define( 'FMEDPR_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
        }


        function plugin_action_links( $actions ) {
		
			$custom_actions = array();
		
			// support url
			$custom_actions['support'] = sprintf( '<a href="%s" target="_blank">%s</a>', 'http://support.fmeaddons.com/', __( 'Support', 'fmepiw' ) );
			
			// add the links to the front of the actions list
			return array_merge( $custom_actions, $actions );
			
		}

		function init() {
	        if ( function_exists( 'load_plugin_textdomain' ) )
	            load_plugin_textdomain( 'fmedpr', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
	   	}

	   	/**
		 * Change the admin footer text on admin pages.
		 * This function is get from woocommerce admin
		 * @since  2.3
		 * @param  string $footer_text
		 * @return string
		 */
		public function admin_footer_text( $footer_text ) { 
			

			// Check to make sure we're on a WooCommerce admin page
			if ( apply_filters( 'woocommerce_display_admin_footer_text', $footer_text ) ) {
				// Change the footer text
				if ( ! get_option( 'fme_dynamic_pricing_rules_rated_text' ) ) {
					$footer_text = sprintf( __( 'If you like <strong>FME Dynamic Pricing Rules</strong> please leave us a %s&#9733;&#9733;&#9733;&#9733;&#9733;%s rating. A huge thank you from FME Addons in advance!', 'woocommerce' ), '<a href="https://www.fmeaddons.com" target="_blank" class="wc-rating-link" data-rated="' . esc_attr__( 'Thanks :)', 'woocommerce' ) . '">', '</a>' );
					wc_enqueue_js( "
						jQuery( 'a.wc-rating-link' ).click( function() { 
							jQuery.post( '" . WC()->ajax_url() . "', { action: 'fme_dynamic_pricing_rules_rated' } );
							jQuery( this ).parent().text( jQuery( this ).data( 'rated' ) );
						});
					" );
				} else {
					$footer_text = __( 'Thank you for buying with FME Addons', 'woocommerce' );
				}
			}

			return $footer_text;
		}


		function fme_dynamic_pricing_rules_rated() {

			update_option( 'fme_dynamic_pricing_rules_rated_text', 1 );
		}







	}

	$fmedpr = new FME_Dynamic_Pricing_Rules();

}


?>