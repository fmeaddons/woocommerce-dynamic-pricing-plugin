<?php 
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
	require_once FMEDPR_PLUGIN_DIR . 'admin/class-fme-dynamic-pricing-rules-admin.php';
	$fmedpr = new FME_Dynamic_Pricing_Rules_Admin();
	$rules = $fmedpr->getAllCatalogRules();
?>
<div class="field_wrapper">
	<h1><?php _e('Catalog Pricing Rules','fmedpr'); ?></h1>
	<p><?php echo _e('Create Rules for products on which you want to gave offers and promotions!','fmedpr'); ?></p>

	<h2><?php _e('Catalog Rules','fmedpr'); ?></h2>
	<p><?php _e('Only One Rule is implemented based on priority! If One Rule is applied on a product(s) then all other rules will be skiped for that product(s).','fmedpr'); ?></p>

	<div class="field_success"></div>
	<div class="addbatches">
		<input type="button" class="btt2 button button-primary button-large" value="Add Rule" onClick="addRule();">
	</div>

	<form action="<?php echo admin_url('admin-ajax.php'); ?>" id="catalog_rule_form" method="post" enctype="multipart/form-data" />
		

		<!-- Start Saved Rules-->

		<?php foreach($rules as $rule) { ?>
		<?php 
			$post_id = $rule->ID;
			$rule_name = $rule->post_title;
			$priority = $rule->menu_order;
			$status = $rule->post_status;

			$mode_of_discount = get_post_meta($post_id,'_mode_of_discount', true);
			$date_from = get_post_meta($post_id,'_date_from', true);
			$date_to = get_post_meta($post_id,'_date_to', true);
			$applied_to = get_post_meta($post_id,'_applied_to', true);
			$customer_applied_to = get_post_meta($post_id,'_customer_applied_to', true);

			$qty_options = unserialize(get_post_meta($post_id,'_qty_option', true));
			$purchase = get_post_meta($post_id,'_purchase', true);
			$receive = get_post_meta($post_id,'_receive', true);

			$products_to_adjust = get_post_meta($post_id,'_products_to_adjust', true);
			$type_of_discount = get_post_meta($post_id,'_type_of_discount', true);
			$discount_amount = get_post_meta($post_id,'_discount_amount', true);
			?>
			<input type="hidden" name="rule_data[<?php echo $post_id; ?>][rule_id]" value="<?php echo $post_id; ?>">
			<div class="batches" id="filter-row-rule<?php echo $post_id; ?>">
				<div class="option-heading" onClick="getDivs('<?php echo $post_id; ?>')">
					<div class="arrow-up">&#9652;</div>
					<div class="arrow-down">&#9662;</div>
					<h4><?php echo $rule_name; ?></h4>
					
				</div>
				<div class="btt"><a onclick="deleteRule('<?php echo $post_id; ?>');" class="button button-danger button-large"><?php _e("Remove Rule","fmedpr"); ?></a></div>

				<div class="option-content">
					
					<div class="optdata">
						<h3><b><?php _e("General Information","fmedpr"); ?></b></h3>
						<div class="optdataleft"><b><?php _e("Rule Name:","fmedpr"); ?></b></div>
						<div class="optdataright"><input onchange="changeRuleName('<?php echo $post_id; ?>', this.value)" type="text" name="rule_data[<?php echo $post_id; ?>][name]" value="<?php echo $rule_name; ?>" placeholder="<?php _e("Enter Rule Name","fmedpr") ?>" class="fmeinputfield" /></div>
					</div>

					<div class="optdata">
						<div class="optdataleft"><b><?php _e("Rule Priority:","fmedpr"); ?></b></div>
						<div class="optdataright"><input type="number" name="rule_data[<?php echo $post_id; ?>][priority]" value="<?php echo $priority; ?>" placeholder="0" min="0" /></div>
					</div>

					<div class="optdata">
						<div class="optdataleft"><b><?php _e("Mode of Discount:","fmedpr"); ?></b></div>
						<div class="optdataright"><select name="rule_data[<?php echo $post_id; ?>][mode_of_discount]" onChange="changeMode('<?php echo $post_id; ?>', this.value)">
							<option value="quantity_discount" <?php echo selected('quantity_discount', $mode_of_discount); ?>><?php _e("Quantity Discount","fmedpr"); ?></option>
							
						</select></div>
					</div>

					<div class="optdata">
						<div class="optdataleft"><b><?php _e("Date From:","fmedpr"); ?></b></div>
						<div class="optdataright"><input class="datepicker" type="text" name="rule_data[<?php echo $post_id; ?>][date_from]" value="<?php echo $date_from; ?>" placeholder="Date From" /></div>
					</div>

					<div class="optdata">
						<div class="optdataleft"><b><?php _e("Date To:","fmedpr"); ?></b></div>
						<div class="optdataright"><input class="datepicker" type="text" name="rule_data[<?php echo $post_id; ?>][date_to]" value="<?php echo $date_to; ?>" placeholder="Date To" /></div>
					</div>

					<div class="optdata">
						<div class="optdataleft"><b><?php _e("Status:","fmedpr"); ?></b></div>
						<div class="optdataright"><select name="rule_data[<?php echo $post_id; ?>][status]">
							<option value="publish" <?php echo selected('publish', $status); ?>><?php _e("Active","fmedpr"); ?></option>
							<option value="unpublish" <?php echo selected('unpublish', $status); ?>><?php _e("Inactive","fmedpr"); ?></option>
						</select></div>
					</div>

					<div class="optdata">
						<h3><b><?php _e("Conditions","fmedpr"); ?></b></h3>
					</div>

					<div class="optdata">
						<div class="optdataleft"><b><?php _e("Applied To:","fmedpr"); ?></b></div>
						<div class="optdataright"><select name="rule_data[<?php echo $post_id; ?>][applied_to]" onChange="changeAppliedTo('<?php echo $post_id; ?>', this.value)">
							
							<option value="" <?php echo selected('', $applied_to); ?>><?php _e("Select Critaria","fmedpr"); ?></option>
							<option value="products" <?php echo selected('products', $applied_to); ?>><?php _e("Products","fmedpr"); ?></option>
						</select></div>
					</div>
					

					<div class="product_selection" <?php if($applied_to == 'products') { ?> style="display:block" <?php } else { ?> style="display:none" <?php } ?>>
						<div class="optdata">
							<div class="optdataleft"><b><?php _e("Select Products:","fmedpr"); ?></b></div>
							<div class="optdataright"><select style="width:90%" class="js-data-example-ajax-pro<?php echo $post_id; ?>" name="rule_data[<?php echo $post_id; ?>][pro_name][]" >
							</select></div>
						</div>
					</div>

					

					<div class="optdata">
						<div class="optdataleft"><b><?php _e("Customers","fmedpr"); ?></b></div>
						<div class="optdataright"><select name="rule_data[<?php echo $post_id; ?>][customer_customer_roles]" onChange="changeCustomerAppliedTo('<?php echo $post_id; ?>', this.value)">
							<option value="" <?php echo selected('', $customer_applied_to); ?>><?php _e("Selecte Critaria","fmedpr"); ?></option>
							<option value="customers" <?php echo selected('customers', $customer_applied_to); ?>><?php _e("Customers","fmedpr"); ?></option>
						</select></div>
					</div>

					<div class="customer_selection" <?php if($customer_applied_to == 'customers') { ?> style="display:block" <?php } else { ?> style="display:none" <?php } ?>>
						<div class="optdata">
							<div class="optdataleft"><b><?php _e("Select Customers:","fmedpr"); ?></b></div>
							<div class="optdataright"><select style="width:90%" class="js-data-example-ajax-cus<?php echo $post_id; ?>" name="rule_data[<?php echo $post_id; ?>][customer_name][]" >
							</select></div>
						</div>
					</div>


					<div class="optdata">
						<h3><b><?php _e("Actions","fmedpr"); ?></b></h3>
					</div>

					


					<!-- Quantity Discount-->
					<div class="qty_discount" <?php if($mode_of_discount == 'quantity_discount') { ?> style="display:block" <?php } else { ?> style="display:none" <?php } ?>>

						<div class="optdata">
							<div class="min_qty">
								<b><?php _e("Min Quantity","fmedpr"); ?></b>
							</div>
						
							<div class="max_qty">
								<b><?php _e("Max Quantity","fmedpr"); ?></b>
							</div>
						
							<div class="dis_type">
								<b><?php _e("Type of Discount","fmedpr"); ?></b>
							</div>
						
							<div class="dis_amount">
								<b><?php _e("Discount Amount","fmedpr"); ?></b>
							</div>
						
							<div class="qty_remove">
								
							</div>
						</div>
						
						<!-- Saved Qty-->
						<?php $a = 100000000; ?>
						<?php if(count($qty_options) > 0) { ?>
						<?php foreach($qty_options as $qty_option) { ?>
						<div class="optdata" id="<?php echo $post_id; ?>filter-row-qty<?php echo $a; ?>">
			
							<div class="min_qty">
								<input type="number" name="rule_data[<?php echo $post_id; ?>][qty_option][<?php echo $a; ?>][min_qty]" value="<?php echo $qty_option['min_qty'];  ?>" placeholder="0" min="0" />
							</div>
						
							<div class="max_qty">
								<input type="number" name="rule_data[<?php echo $post_id; ?>][qty_option][<?php echo $a; ?>][max_qty]" value="<?php echo $qty_option['max_qty'];  ?>" placeholder="0" min="0" />
							</div>
						
							<div class="dis_type">
								<select name="rule_data[<?php echo $post_id; ?>][qty_option][<?php echo $a; ?>][qty_type_of_discount]">
									<option value="percentage" <?php echo selected('percentage', $qty_option['qty_type_of_discount']); ?>><?php _e("Percentage","fmedpr"); ?></option>
									<option value="fixed" <?php echo selected('fixed', $qty_option['qty_type_of_discount']); ?>><?php _e("Fixed","fmedpr"); ?></option>
								</select>
							</div>
						
							<div class="dis_amount">
								<input type="number" name="rule_data[<?php echo $post_id; ?>][qty_option][<?php echo $a; ?>][qty_discount_amount]" value="<?php echo $qty_option['qty_discount_amount'];  ?>" placeholder="0" min="0" />
							</div>
						
							<div class="qty_remove">
								<div class="btt"><a onclick="jQuery('#'+<?php echo $post_id; ?>+'filter-row-qty'+<?php echo $a; ?>).remove();" class="button button-danger button-large"><?php _e("Remove","fmedpr"); ?></a></div>
							</div>
							
						</div>
						<?php $a++; } } ?>


						<div class="topfilters" id="beforeqt<?php echo $post_id; ?>"></div>
						<div class="optdata">
							<div class="addbatches">
								<input type="button" class="btt2 button button-primary button-large" value="Add Quantity" onClick="addQty('<?php echo $post_id; ?>')">
							</div>
						</div>

					</div>

				</div>

			</div>

		<?php } ?>

		<!-- End Saved Rules-->

		<div class="topfilters" id="beforetf"></div>

		<?php wp_nonce_field('catalog_rule_form','123catalogruleform123'); ?>
		<input name="action" value="catalog_rule_form" type="hidden">
		<div class="addbatches">
			<input id="saverule" type="submit" name="saverule" class="button-primary" value="<?php _e( 'Save Changes', 'fmedpr' ); ?>" />
		</div>

	</form>


</div>
<div id="load"></div>
<script type="text/javascript">
	jQuery( document ).ready(function() {
		var ajaxurl = '<?php echo admin_url( 'admin-ajax.php'); ?>';
		<?php foreach($rules as $rule) {
			$applied_to = get_post_meta($rule->ID,'_applied_to', true);
			$customer_applied_to = get_post_meta($rule->ID,'_customer_applied_to', true);

	 	?> 

	 		<?php  if($applied_to == 'products') { ?>
	 			
	 			jQuery(".js-data-example-ajax-pro<?php echo $rule->ID; ?>").select2({
			      ajax: {
			        url: ajaxurl,
			        dataType: 'json',
			        delay: 250,
			        type: 'POST',
			        data: function (params) {
			          return {
			            q: params.term, // search term
					    page: params.page,
					    action: "searchData",
					    v:'products'

			          };
			        },
			        processResults: function (data, params) {
			        	

			        	var cata = [];
						var catb = {};

						for(var i in data) {

						    var item = data[i];

						   cata.push({ 
						        id : item.id,
						        name      : item.name,
						        text      : item.name,
						        image      : item.image
						    });
						}

						catb.cata = cata;

			          // parse the results into the format expected by Select2.
			          // since we are using custom formatting functions we do not need to
			          // alter the remote JSON data
			          params.page = params.page || 1;
			          return {
			            results: cata,
			            pagination: {
				          more: (params.page * 30) < data.total_count
				        }
			          };
			        },
			        cache: true
			      },
			      escapeMarkup: function (markup) { return markup; }, // let our custom formatter work
			      minimumInputLength: 3,
			      multiple: true,
			      placeholder: 'Choose Products',
			      templateResult: formatRepo, // omitted for brevity, see the source of this page
			      templateSelection: formatRepoSelection // omitted for brevity, see the source of this page  
			        
			    });

				<?php 
					global $wpdb;
					$ProID = get_post_meta($rule->ID,'_products_applied_to', true);
					
					$proids = explode(',', $ProID);
					for($a = 0; $a < sizeof($proids); $a++) {
						$result = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM ".$wpdb->prefix."posts WHERE post_type = %s AND ID = %d", 'product',$proids[$a]));
					?>
						var option<?php echo $a; ?> = new Option('<?php echo $result->post_title; ?>','<?php echo $result->ID ?>', true);
						jQuery(".js-data-example-ajax-pro<?php echo $rule->ID; ?>").append(option<?php echo $a; ?>);
					<?php } ?>
				
				jQuery(".js-data-example-ajax-pro<?php echo $rule->ID; ?>").trigger('change');


	 		<?php } ?>



	 		//Customer

	 		<?php if($customer_applied_to == 'customers') {  ?>

	 			jQuery(".js-data-example-ajax-cus<?php echo $rule->ID; ?>").select2({
			      ajax: {
			        url: ajaxurl,
			        dataType: 'json',
			        delay: 250,
			        type: 'POST',
			        data: function (params) {
			          return {
			            q: params.term, // search term
					    page: params.page,
					    action: "searchCustomerData",
					    v:'customers'
			          };
			        },
			        processResults: function (data, params) { 
			        	

			        	var cata = [];
						var catb = {};

						for(var i in data) {

						    var item = data[i];

						   cata.push({ 
						        id : item.id,
						        name      : item.name,
						        text      : item.name,
						        title      : item.email
						        
						    });
						}

						catb.cata = cata;

			          // parse the results into the format expected by Select2.
			          // since we are using custom formatting functions we do not need to
			          // alter the remote JSON data
			          params.page = params.page || 1;
			          return {
			            results: cata,
			            pagination: {
				          more: (params.page * 30) < data.total_count
				        }
			          };
			        },
			        cache: true
			      },
			      escapeMarkup: function (markup) { return markup; }, // let our custom formatter work
			      minimumInputLength: 0,
			      multiple: true,
			      placeholder: 'Choose Customers',
			      templateResult: formatRepoCustomers, // omitted for brevity, see the source of this page
			      templateSelection: formatRepoSelectionCustomers // omitted for brevity, see the source of this page  
			        
			    });

				<?php 
					global $wpdb;
					$CusID = get_post_meta($rule->ID,'_customers_applied_to', true);
					
					$cusids = explode(',', $CusID);
					for($a = 0; $a < sizeof($cusids); $a++) {
						$result = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM ".$wpdb->prefix."users WHERE ID = %d", $cusids[$a]));
					?>
						var option<?php echo $a; ?> = new Option('<?php echo $result->user_email; ?>','<?php echo $result->ID ?>', true);
						jQuery(".js-data-example-ajax-cus<?php echo $rule->ID; ?>").append(option<?php echo $a; ?>);
					<?php } ?>
				
				jQuery(".js-data-example-ajax-cus<?php echo $rule->ID; ?>").trigger('change');

	 		<?php }  ?>

	 		


	 	<?php  } ?>
	});
</script>


<script type="text/javascript">

	var ajaxurl = '<?php echo admin_url( 'admin-ajax.php'); ?>';
	function formatRepo (repo) { 
	  if (repo.loading) return "Loading....";

	  var markup = "<div class='select2-result-repository clearfix'>" +
	    "<div class='select2-result-repository__avatar'><img src='" + repo.image + "' width='50' /></div>" +
	    "<div class='select2-result-repository__meta'>" +
	      "<div class='select2-result-repository__title'>#"+ repo.id +" "+ repo.text + "</div>";


	  markup += "<div class='select2-result-repository__statistics'>" +
	  "</div>" +
	  "</div></div>";

	  return markup;
	}

  function formatRepoSelection (repo) { 
    return "#"+ repo.id +" " + repo.text;
  }

	function formatRepoCustomers (repo) {
	  if (repo.loading) return "Loading....";

	  var markup = "<div class='select2-result-repository clearfix'>" +
	    "<div class='select2-result-repository__meta'>" +
	      "<div class='select2-result-repository__title'>#" + repo.id +" <b>("+repo.text+")</b> "+ repo.title + "</div>";


	  markup += "<div class='select2-result-repository__statistics'>" +
	  "</div>" +
	  "</div></div>";

	  return markup;
	}

  function formatRepoSelectionCustomers (repo) {
    return "#"+ repo.id +" <b>("+repo.text+")</b> " + repo.title;
  }

 

	var filter_row_rule = 1;

	function addRule() { 

		html  = '<div class="batches" id="filter-row-rule' + filter_row_rule + '">';
			html += '<div class="option-heading" onClick="getDivs('+filter_row_rule+')">';
				html += '<div class="arrow-up">&#9652;</div>';
				html += '<div class="arrow-down">&#9662;</div>';
				html += '<h4><?php _e("Rule Name","fmedpr"); ?></h4>';
				
			html += '</div>';
			html += '<div class="btt"><a onclick="jQuery(\'#filter-row-rule' + filter_row_rule + '\').remove();" class="button button-danger button-large"><?php _e("Remove Rule","fmedpr"); ?></a></div>';

			html += '<div class="option-content">';
				
				html += '<div class="optdata">';
					html += '<h3><b><?php _e("General Information","fmedpr"); ?></b></h3>';
					html += '<div class="optdataleft"><b><?php _e("Rule Name:","fmedpr"); ?></b></div>';
					html +='<div class="optdataright"><input onchange="changeRuleName('+filter_row_rule+', this.value)" type="text" name="rule_data[' + filter_row_rule + '][name]" value="" placeholder="<?php _e("Enter Rule Name","fmedpr") ?>" class="fmeinputfield" /></div>';
				html += '</div>';

				html += '<div class="optdata">';
					html += '<div class="optdataleft"><b><?php _e("Rule Priority:","fmedpr"); ?></b></div>';
					html +='<div class="optdataright"><input type="number" name="rule_data[' + filter_row_rule + '][priority]" value="0" placeholder="0" min="0" /></div>';
				html += '</div>';

				html += '<div class="optdata">';
					html += '<div class="optdataleft"><b><?php _e("Mode of Discount:","fmedpr"); ?></b></div>';
					html +='<div class="optdataright"><select name="rule_data[' + filter_row_rule + '][mode_of_discount]" onChange="changeMode('+filter_row_rule+', this.value)">';
						html += '<option value="quantity_discount"><?php _e("Quantity Discount","fmedpr"); ?></option>';
						
					html += '</select></div>';
				html += '</div>';

				html += '<div class="optdata">';
					html += '<div class="optdataleft"><b><?php _e("Date From:","fmedpr"); ?></b></div>';
					html +='<div class="optdataright"><input class="datepicker" type="text" name="rule_data[' + filter_row_rule + '][date_from]" value="" placeholder="Date From" /></div>';
				html += '</div>';

				html += '<div class="optdata">';
					html += '<div class="optdataleft"><b><?php _e("Date To:","fmedpr"); ?></b></div>';
					html +='<div class="optdataright"><input class="datepicker" type="text" name="rule_data[' + filter_row_rule + '][date_to]" value="" placeholder="Date To" /></div>';
				html += '</div>';

				html += '<div class="optdata">';
					html += '<div class="optdataleft"><b><?php _e("Status:","fmedpr"); ?></b></div>';
					html +='<div class="optdataright"><select name="rule_data[' + filter_row_rule + '][status]">';
						html += '<option value="publish"><?php _e("Active","fmedpr"); ?></option>';
						html += '<option value="unpublish"><?php _e("Inactive","fmedpr"); ?></option>';
					html += '</select></div>';
				html += '</div>';

				html += '<div class="optdata">';
					html += '<h3><b><?php _e("Conditions","fmedpr"); ?></b></h3>';
				html += '</div>';

				html += '<div class="optdata">';
					html += '<div class="optdataleft"><b><?php _e("Applied To:","fmedpr"); ?></b></div>';
					html +='<div class="optdataright"><select name="rule_data[' + filter_row_rule + '][applied_to]" onChange="changeAppliedTo('+filter_row_rule+', this.value)">';
						
						html += '<option value=""><?php _e("Select Critaria","fmedpr"); ?></option>';
						html += '<option value="products"><?php _e("Products","fmedpr"); ?></option>';
					html += '</select></div>';
				html += '</div>';

				

				html += '<div class="product_selection">';
					html += '<div class="optdata">';
						html += '<div class="optdataleft"><b><?php _e("Select Products:","fmedpr"); ?></b></div>';
						html +='<div class="optdataright"><select style="width:90%" class="js-data-example-ajax-pro'+filter_row_rule+'" name="rule_data[' + filter_row_rule + '][pro_name][]" >';
						html += '</select></div>';
					html += '</div>';
				html += '</div>';

				html += '<div class="optdata special_offers">';
				

				html += '<div class="optdata">';
					html += '<div class="optdataleft"><b><?php _e("Customers","fmedpr"); ?></b></div>';
					html +='<div class="optdataright"><select name="rule_data[' + filter_row_rule + '][customer_customer_roles]" onChange="changeCustomerAppliedTo('+filter_row_rule+', this.value)">';
						
						html += '<option value=""><?php _e("Select Critaria","fmedpr"); ?></option>';
						html += '<option value="customers"><?php _e("Customers","fmedpr"); ?></option>';
						
					html += '</select></div>';
				html += '</div>';

				html += '<div class="customer_selection">';
					html += '<div class="optdata">';
						html += '<div class="optdataleft"><b><?php _e("Select Customers:","fmedpr"); ?></b></div>';
						html +='<div class="optdataright"><select style="width:90%" class="js-data-example-ajax-cus'+filter_row_rule+'" name="rule_data[' + filter_row_rule + '][customer_name][]" >';
						html += '</select></div>';
					html += '</div>';
				html += '</div>';

				

				html += '<div class="optdata">';
					html += '<h3><b><?php _e("Actions","fmedpr"); ?></b></h3>';
				html += '</div>';

				


				//Quantity Discount
				html += '<div class="qty_discount">';

					html += '<div class="optdata">';
						html += '<div class="min_qty">';
							html += '<b><?php _e("Min Quantity","fmedpr"); ?></b>';
						html += '</div>';
					
						html += '<div class="max_qty">';
							html += '<b><?php _e("Max Quantity","fmedpr"); ?></b>';
						html += '</div>';
					
						html += '<div class="dis_type">';
							html += '<b><?php _e("Type of Discount","fmedpr"); ?></b>';
						html += '</div>';
					
						html += '<div class="dis_amount">';
							html += '<b><?php _e("Discount Amount","fmedpr"); ?></b>';
						html += '</div>';
					
						html += '<div class="qty_remove">';
							
						html += '</div>';
					html += '</div>';

					html += '<div class="topfilters" id="beforeqt'+filter_row_rule+'"></div>';
					html += '<div class="optdata">';
						html += '<div class="addbatches">';
							html += '<input type="button" class="btt2 button button-primary button-large" value="Add Quantity" onClick="addQty('+filter_row_rule+')">';
						html += '</div>';
					html += '</div>';

				html += '</div>';



			html += '</div>';

		html += '</div>';

		jQuery('#beforetf').before(html);
	
		filter_row_rule++;

	}
	

	var filter_row_qty = 1;

	function addQty(id) {

		html = '';
		html += '<div class="optdata" id="'+id+'filter-row-qty' + filter_row_qty + '">';
			
			html += '<div class="min_qty">';
				html += '<input type="number" name="rule_data[' + id + '][qty_option][' + filter_row_qty + '][min_qty]" value="0" placeholder="0" min="0" />';
			html += '</div>';
		
			html += '<div class="max_qty">';
				html += '<input type="number" name="rule_data[' + id + '][qty_option][' + filter_row_qty + '][max_qty]" value="0" placeholder="0" min="0" />';
			html += '</div>';
		
			html += '<div class="dis_type">';
				html += '<select name="rule_data[' + id + '][qty_option][' + filter_row_qty + '][qty_type_of_discount]">';
					html += '<option value="percentage"><?php _e("Percentage","fmedpr"); ?></option>';
					html += '<option value="fixed"><?php _e("Fixed","fmedpr"); ?></option>';
				html += '</select>';
			html += '</div>';
		
			html += '<div class="dis_amount">';
				html += '<input type="number" name="rule_data[' + id + '][qty_option][' + filter_row_qty + '][qty_discount_amount]" value="0" placeholder="0" min="0" />';
			html += '</div>';
		
			html += '<div class="qty_remove">';
				html += '<div class="btt"><a onclick="jQuery(\'#'+id+'filter-row-qty' + filter_row_qty + '\').remove();" class="button button-danger button-large"><?php _e("Remove","fmedpr"); ?></a></div>';
			html += '</div>';
			
		html += '</div>';

		jQuery('#beforeqt'+id).before(html);
		filter_row_qty++;

	}


	function getDivs(id) { 
		jQuery('#filter-row-rule'+id).find(".arrow-up, .arrow-down").toggle();
		jQuery('#filter-row-rule'+id+' .option-content').slideToggle('slow');

		jQuery(document).ready(function() {
	    	jQuery('.datepicker').datepicker({ 
	        	dateFormat : 'dd-mm-yy'
	    	});
		});
		
	}

	function changeRuleName(id, value) {
		jQuery('#filter-row-rule'+id+' h4').html(value);
	}


	function changeType(id, value) { 
		changeAppliedTo(id,'products');
	}


	
</script>

<script type="text/javascript">
	function changeAppliedTo(id, value) {
		 
			
			if(value == 'products') {
			jQuery('#filter-row-rule'+id+' .option-content .product_selection').fadeIn('slow');


			jQuery(".js-data-example-ajax-pro"+id).select2({
		      ajax: {
		        url: ajaxurl,
		        dataType: 'json',
		        delay: 250,
		        type: 'POST',
		        data: function (params) {
		          return {
		            q: params.term, // search term
				    page: params.page,
				    action: "searchData",
				    v:value

		          };
		        },
		        processResults: function (data, params) { 
		        	

		        	var cata = [];
					var catb = {};

					for(var i in data) {

					    var item = data[i];

					   cata.push({ 
					        id : item.id,
					        name      : item.name,
					        text      : item.name,
					        image      : item.image 
					    });
					}

					catb.cata = cata;

		          // parse the results into the format expected by Select2.
		          // since we are using custom formatting functions we do not need to
		          // alter the remote JSON data
		          params.page = params.page || 1;
		          return {
		            results: cata,
		            pagination: {
			          more: (params.page * 30) < data.total_count
			        }
		          };
		        },
		        cache: true
		      },
		      escapeMarkup: function (markup) { return markup; }, // let our custom formatter work
		      minimumInputLength: 3,
		      multiple: true,
		      placeholder: 'Choose Products',
		      templateResult: formatRepo, // omitted for brevity, see the source of this page
		      templateSelection: formatRepoSelection // omitted for brevity, see the source of this page  
		        
		    });

		} else {
			
			
		}
	}
</script>


<script type="text/javascript">
	function changeCustomerAppliedTo(id, value) {
		 if(value == 'customers') {
			jQuery('#filter-row-rule'+id+' .option-content .customer_selection').fadeIn('slow');


			jQuery(".js-data-example-ajax-cus"+id).select2({
		      ajax: {
		        url: ajaxurl,
		        dataType: 'json',
		        delay: 250,
		        type: 'POST',
		        data: function (params) {
		          return {
		            q: params.term, // search term
				    page: params.page,
				    action: "searchCustomerData",
				    v:value
		          };
		        },
		        processResults: function (data, params) { 
		        	

		        	var cata = [];
					var catb = {};

					for(var i in data) {

					    var item = data[i];

					   cata.push({ 
					        id : item.id,
					        name      : item.name,
					        text      : item.name,
					        title      : item.email
					        
					    });
					}

					catb.cata = cata;

		          // parse the results into the format expected by Select2.
		          // since we are using custom formatting functions we do not need to
		          // alter the remote JSON data
		          params.page = params.page || 1;
		          return {
		            results: cata,
		            pagination: {
			          more: (params.page * 30) < data.total_count
			        }
		          };
		        },
		        cache: true
		      },
		      escapeMarkup: function (markup) { return markup; }, // let our custom formatter work
		      minimumInputLength: 0,
		      multiple: true,
		      placeholder: 'Choose Customers',
		      templateResult: formatRepoCustomers, // omitted for brevity, see the source of this page
		      templateSelection: formatRepoSelectionCustomers // omitted for brevity, see the source of this page  
		        
		    });


		} else {
			
			
		}
	}
</script>




<script type="text/javascript">
	function changeMode(id, value) {
		changeAdjustAppliedTo(id,'products');
		if(value == 'quantity_discount') {
			jQuery('#filter-row-rule'+id+' .qty_discount').slideDown('slow');
		} 
		
	}

</script>

<script type="text/javascript">
	function deleteRule(id) {
		var ajaxurl = "<?php echo admin_url( 'admin-ajax.php'); ?>";
		if(confirm("Are you sure to delete this Rule? This action can not be undone."))
		{
			jQuery('#load').show();
			jQuery.ajax({
			type: "POST",
			url: ajaxurl,
			data: {"action": "deleteCatalogRule", "rule_id":id},
			success: function() {

				
				jQuery("#filter-row-rule"+id).remove();
				jQuery("#filter-row-rule"+id).fadeOut('slow');
				jQuery('.field_success').html("<div class='updated notice alert succ'>Rule Deleted Sucessfully!</div>");
				window.scrollTo(0, 0);
				
				jQuery('.alert').delay(5000).fadeOut('slow');

				jQuery('#load').hide();
			}
			});

		}
	return false;
	}
</script>

