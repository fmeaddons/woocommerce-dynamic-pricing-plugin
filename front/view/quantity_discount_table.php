<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
	require_once FMEDPR_PLUGIN_DIR . 'front/class-fme-dynamic-pricing-rules-front.php';
	$fmedpr = new FME_Dynamic_Pricing_Rules_Front();
	$rules = $fmedpr->getAllCatalogRules();
?>

<?php foreach($rules as $rule) {  ?>
<?php
	
	$_product = wc_get_product( $post->ID );
	$price = $_product->get_price();
	$currency = get_option('woocommerce_currency');
	$currency_symbol = get_woocommerce_currency_symbol($currency);
	$user_ID = get_current_user_id();

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


?>

<?php

//Check Dates
if(($current_date <= $date_to)) { 
		//Check mode of discount if quantiy then true if not false
	if($mode_of_discount == 'quantity_discount') { 
		if(($cus_applied_toArray!='' && in_array($user_ID,$cus_applied_toArray))) {
		?>

		<?php 
			//Check for product ids
			if(($products_applied_toArray!='' && in_array($post->ID,$products_applied_toArray))) { ?>
				
				<div class="qty_table">
					<h2><?php echo $rule->post_title; ?></h2>
					<div class="offer_ends"><b><?php echo _e('Offers Ends:','fmedpr') ?> </b><?php echo date('l j F, Y',strtotime($date_to)); ?></div>
					<div class="offer_table">
						<div class="qty_table_top">
							<div class="pro_qty"><b><?php _e('Quantity','fmedpr') ?></b></div>
							<div class="pro_offer"><b><?php _e('Discount','fmedpr') ?></b></div>
							<div class="pro_price"><b><?php _e('New Price','fmedpr') ?></b></div>
						</div>
						<?php 
						$a = 0;
						foreach($qty_options as $option) { ?>
						<div <?php if($a%2==0) { ?> class="qty_table_bottom even" <?php } else { ?> class="qty_table_bottom odd" <?php } ?>>
							<div class="pro_qty"><?php echo $option['min_qty'].' - '.$option['max_qty']; ?></div>
							<div class="pro_offer">
								<?php if($option['qty_type_of_discount'] == 'percentage') { ?>
									<?php echo $option['qty_discount_amount']; ?><?php _e('% OFF each','fmedpr'); ?>
								<?php } else { ?>
									<?php echo $currency_symbol.$option['qty_discount_amount']; ?><?php _e(' OFF each','fmedpr'); ?>
								<?php } ?>
							</div>
							<div class="pro_price">
							<?php if($option['qty_type_of_discount'] == 'percentage') { ?>
								<?php echo $currency_symbol.number_format($price - $price*$option['qty_discount_amount']/100,2);  ?>
							<?php } else { ?>
								<?php echo $currency_symbol.number_format($price - $option['qty_discount_amount'],2);  ?>
							<?php } ?>
							</div>
						</div>
						<?php $a++; } ?>
					</div>


				</div>
				
			<?php break;  } else { continue; } ?>

			<?php } else {} ?>
	<?php }  ?>





<?php } else { } ?>
	
<?php } ?>