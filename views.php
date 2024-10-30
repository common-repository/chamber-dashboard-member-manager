<?php
if ( ! defined( 'ABSPATH' ) ) exit;

// ------------------------------------------------------------------------
// Single Invoice View
// ------------------------------------------------------------------------

// Enqueue stylesheet for single invoice
function cdashmm_single_invoice_style() {
	if( is_singular( 'invoice' ) ) {
		wp_enqueue_style( 'cdashmm-invoice', plugin_dir_url(__FILE__) . 'css/invoice.css' );
	}
}
add_action( 'wp_enqueue_scripts', 'cdashmm_single_invoice_style' );

// Filter the title so it just says "invoice"
function cdashmm_single_invoice_title( $title ) {
	global $post;
	/*if( is_singular('invoice') && $title == $post->post_title ) {
        $title = "Invoice";
    }*/
	if( is_singular('invoice') && 'invoice' == get_post_type() && $title == $post->post_title ) {
        $title = "Invoice";
    }
    return $title;
}
add_filter( 'the_title', 'cdashmm_single_invoice_title', 10, 2 );

// Add a class to previous and next post list on the single invoice page so that it can be hidden using CSS
function cdashmm_invoice_posts_link_attributes($output) {
	if(is_singular('invoice') || is_single('invoice')){
		$code = 'class="single_invoice_nav_link"';
    	return str_replace('<a href=', '<a '.$code.' href=', $output);
	}
}
add_filter('next_post_link', 'cdashmm_invoice_posts_link_attributes');
add_filter('previous_post_link', 'cdashmm_invoice_posts_link_attributes');

// add script to footer to print invoice div
// http://stackoverflow.com/a/7532581/1112258
function cdashmm_invoice_footer_scripts() {
    if( is_singular( 'invoice' ) ) { ?>
        <script type="text/javascript">
            function printDiv(divName) {
		     var printContents = document.getElementById(divName).innerHTML;
		     var originalContents = document.body.innerHTML;
		     document.body.innerHTML = printContents;
		     window.print();
		     document.body.innerHTML = originalContents;
}
        </script>
<?php }
}
add_action('wp_footer', 'cdashmm_invoice_footer_scripts');

// Display single invoice (filter content)
function cdashmm_single_invoice( $content ) {
	if( is_singular('invoice') ) {
		// get options
		$options = get_option( 'cdashmm_options' );
	    $paypal = true;
	    if( !isset( $options['paypal_email'] ) || '' == $options['paypal_email'] ) {
	    	$paypal = false;
	    }
		// get the business associated with this invoice
		$invoice_id = get_the_id();
        $args = array(
            'post_type' => 'business',
            'connected_type' => 'invoices_to_businesses',
            'connected_items' => $invoice_id,
        );
        $thisbusiness = new WP_Query( $args );
        // $msg = "<pre>" . print_r($thisbusiness, true) . "</pre>";
        // wp_die($msg);
        if ( $thisbusiness->have_posts() ) {
            while ( $thisbusiness->have_posts() ) : $thisbusiness->the_post();
	        		$business_id = get_the_id();
	        		$business_billing = cdashmm_get_business_billing($business_id);
            endwhile;
        } else {
        	$error = __('This invoice does not have a business associated with it.  You need to edit the invoice and add a business.', 'cdashmm' );
        	wp_die( $error );
        }
        wp_reset_postdata();

		// get invoice meta
        global $invoice_metabox;
        $invoice_meta = $invoice_metabox->the_meta();
        $statuses = get_the_terms( get_the_id(), 'invoice_status' );
        $this_status = '';
        if( isset( $statuses ) && !is_wp_error( $statuses ) && $statuses != '' ) {
        	foreach( $statuses as $status ) {
        		$this_status = $status->name;
        	}
        }
        if( isset( $invoice_meta['duedate'] ) ) {
        	$duedate = $invoice_meta['duedate'];
        } else {
        	$duedate = __( 'Invoice due on receipt', 'cdashmm' );
        }
        $cdash_options = get_option( 'cdash_directory_options' );
    	$currency = $cdash_options['currency'];
		$invoice_content =
		'<div id="invoice" class="' . $this_status . '">
			<p><input type="button" onclick="printDiv(\'print-invoice\')" value="' . __( 'Print Invoice', 'cdashmm' ) . '" /></p>
			<div id="print-invoice">
				<div class="invoice-header">
					<div class="invoice-header-contact">
						<div class="invoice-from">
							<h4>From: </h4>
							' . do_shortcode( wpautop( $options['invoice_from'] ) ) . '
						</div>
						<div class="invoice-to">
							<h4>To: </h4>
							' . $business_billing . '
						</div>
					</div>
					<div class="invoice-header-details">
						<ul>
							<li><strong>' . __( 'Invoice #: ', 'cdashmm' ) . '</strong><span class="invoice-number"> ' . $invoice_meta['invoice_number'] . '</span></li>
							<li><strong>' . __( 'Issue Date: ', 'cdashmm' ) . '</strong><span class="issue-date"> ' . get_the_time( 'Y-m-d' ) . '</span></li>
							<li><strong>' . __( 'Due Date: ', 'cdashmm' ) . '</strong><span class="due-date"> ' . $duedate . '</span></li>
							<li><strong>' . __( 'Status: ', 'cdashmm' ) . '</strong><span class="status"> ' . $this_status . '</span></li>';
							if( "Paid" == $this_status && isset( $invoice_meta['paiddate'] ) ) {
								$invoice_content .= '<li><strong>' . __( 'Paid Date: ', 'cdashmm' ) . '</strong><span class="status"> ' . $invoice_meta['paiddate'] . '</span></li>';
							}
						$invoice_content .= '</ul>
					</div>
				</div>
				<div class="invoice-description">'
					. $content .
				'</div>
				<table class="invoice-details">
					<tr>
						<th>Item</th>
						<th>Amount</th>
					</tr>';
					if( isset( $invoice_meta['item_membershiplevel'] ) && isset( $invoice_meta['item_membershipamt'] ) ) {
						$level = get_term_by( 'id', $invoice_meta['item_membershiplevel'], 'membership_level' );
						$invoice_content .=
						'<tr>
							<td>' . __( 'Membership: ', 'cdashmm' ) . $level->name . '</td>
							<td>' . cdashmm_display_price( $invoice_meta['item_membershipamt'] ) . '</td>
						</tr>';
					}
					if( isset( $invoice_meta['items'] ) ) {
						$items = $invoice_meta['items'];
						foreach( $items as $item ) {
							$invoice_content .=
							'<tr>
								<td>' . $item['item_name'] . '</td>
								<td>' . cdashmm_display_price( $item['item_amount'] ) . '</td>
							</tr>';
						}
					}
          			//if( isset( $invoice_meta['item_tax'] ) && '' !== $invoice_meta['item_tax'] ) {
					if( isset( $invoice_meta['item_tax'] ) && '0' !== $invoice_meta['item_tax'] ) {	  
						$invoice_content .=
						'<tr>
							<td>' . __( 'Tax', 'cdashmm' ) . '</td>
							<td>' . cdashmm_display_price( $invoice_meta['item_tax'] ) . '</td>
						</tr>';
					}
					//if( isset( $invoice_meta['item_donation'] ) && '' !== $invoice_meta['item_donation'] ) {
					if( isset( $invoice_meta['item_donation'] ) && '0' !== $invoice_meta['item_donation'] ) {
						$invoice_content .=
						'<tr>
							<td>' . __( 'Donation', 'cdashmm' ) . '</td>
							<td>' . cdashmm_display_price( $invoice_meta['item_donation'] ) . '</td>
						</tr>';
					}
					
					//if( isset( $invoice_meta['item_processing_fee'] ) && '' !== $invoice_meta['item_processing_fee'] ) {
					if( isset( $invoice_meta['item_processing_fee'] ) && '0' !== $invoice_meta['item_processing_fee'] ) {
						$invoice_content .=
						'<tr>
							<td>' . __( 'Processing Fee', 'cdashmm' ) . '</td>
							<td>' . cdashmm_display_price( $invoice_meta['item_processing_fee'] ) . '</td>
						</tr>';
					}
					$invoice_content .=
					'<tr class="total">
						<td><strong>' . __( 'Total', 'cdashmm') . '</strong></td>
						<td><strong>' . cdashmm_display_price( $invoice_meta['amount'] ) . '</strong></td>
					</tr>
				</table>

				<div class="invoice-footer">
					' . do_shortcode( wpautop( $options['invoice_footer'] ) ) . '
				</div>
			</div><!-- #print-invoice -->';

			// the invoice hasn't been paid, so we'll include a payment button
			if( 'Paid' !== $this_status && true == $paypal ) {
				$invoice_content .=
				'<div class="payment-form">';
				//<form id="invoice_form" action="https://ipnpb.paypal.com/cgi-bin/webscr" method="post">
					$invoice_content .= '<form id="invoice_form" action="https://www.paypal.com/cgi-bin/webscr" method="post">
						<input type="hidden" name="cmd" value="_cart">
						<input type="hidden" name="upload" value="1" />
						<input type="hidden" name="business" value="' . $options['paypal_email'] . '">
						<input type="hidden" name="return" value="' . get_the_permalink() . '">
						<input type="hidden" name="currency_code" value="' . $currency . '">';
						// membership amount, if needed
						$i = 1;
						if( isset( $invoice_meta['item_membershipamt'] ) ) {
							$invoice_content .=
							'<input type="hidden" name="item_name_'.$i.'" value="Membership">
							<input type="hidden" name="amount_'.$i.'" id="amount_'.$i.'" value="' . $invoice_meta['item_membershipamt'] . '">';
							$i++;
						}
						// other items, if needed
						if( isset( $invoice_meta['items'] ) ) {
							$items = $invoice_meta['items'];
							foreach( $items as $item ) {
								$invoice_content .=
								'<input type="hidden" class="paypal cart item_name_'.$i.'" name="item_name_'.$i.'" value="'.$item['item_name'].'">
								<input type="hidden" class="paypal cart amount_'.$i.'" name="amount_'.$i.'" id="amount_'.$i.'" value="' . $item['item_amount'] . '">';
								$i++;
							}
						}
						// donation amount, if needed
						if( isset( $invoice_meta['item_donation']) ) {
							$invoice_content .=
							'<input type="hidden" class="paypal cart item_name_'.$i.'" name="item_name_'.$i.'" value="Donation">
							<input type="hidden" class="paypal cart amount_'.$i.'" name="amount_'.$i.'" id="amount_'.$i.'" value="' . ($invoice_meta['item_donation']) . '">';
                            $i++;
						}

            			// tax amount, if needed
						if( isset( $invoice_meta['item_tax']) ) {
							$invoice_content .=
							'<input type="hidden" class="paypal cart item_name_'.$i.'" name="item_name_'.$i.'" value="Tax">
							<input type="hidden" class="paypal cart amount_'.$i.'" name="amount_'.$i.'" id="amount_'.$i.'" value="' . $invoice_meta['item_tax'] . '">';
                            $i++;
						}

                        // Processing Fee amount, if needed
						if( isset( $invoice_meta['item_processing_fee']) ) {
							$invoice_content .=
							'<input type="hidden" class="paypal cart item_name_'.$i.'" name="item_name_'.$i.'" value="Processing Fee">
							<input type="hidden" class="paypal cart amount_'.$i.'" name="amount_'.$i.'" id="amount_'.$i.'" value="' . $invoice_meta['item_processing_fee'] . '">';
              $i++;
						}
            //$invoice_content .= '<p><input type="submit" value="' . __( 'Pay Now With PayPal', 'cdashmm' ) . '"></p>';

						$invoice_content .=
						'<input type="hidden" name="rm" value="2">
						<input type="hidden" name="custom" id="invoice_id" value="' . $invoice_meta['invoice_number'] . '">
						<input type="hidden" name="cbt" value="Return to ' . $options['orgname'] . '">
						<input type="hidden" name="notify_url" value="' . home_url() . '/?cdash-member-manager=paypal-ipn">
						<p><input type="submit" value="' . __( 'Pay Now With PayPal', 'cdashmm' ) . '"></p>
					</form>
				</div>';
			}
		$invoice_content .=
		'<p><input type="button" onclick="printDiv(\'print-invoice\')" value="' . __( 'Print Invoice', 'cdashmm' ) . '" /></p>
		</div>';
		$content = $invoice_content;
	}
	return $content;
}
add_filter('the_content', 'cdashmm_single_invoice');

function cdashmm_hide_lapsed_members_search_filter( $query ) {
	$member_options = get_option('cdashmm_options');
	if(isset($member_options['hide_lapsed_members']) && '1' == $member_options['hide_lapsed_members'] ) {
		if ( !is_admin() && $query->is_main_query() ) {
			$query->set('tax_query', array(
				array(
					'taxonomy' => 'membership_status',
					'field' => 'slug',
					'terms' => array('lapsed'),
					'operator' => 'NOT IN'
				)
			));
  		}
	}
}

add_action('pre_get_posts', 'cdashmm_hide_lapsed_members_search_filter');

function cdashmm_get_processing_fee_amount(){
	$options = get_option( 'cdashmm_options' );
	if(cdashmm_is_option_selected('use_processing_fee')){
        if(isset($options['processing_fee_amount']) ){
            return +$options['processing_fee_amount'];
        }
	}
	return 0;
}

function cdashmm_is_option_selected($option_name) {
	$options = get_option( 'cdashmm_options' );
    return isset($options[$option_name]) && ($options[$option_name] == 1);
}

function cdashmm_get_option_value($option_name) {
    $options = get_option( 'cdashmm_options' );
    if (!isset($options[$option_name])) {
        return null;
    }
    return $options[$option_name];
}

function cdashmm_show_processing_fee($action) {
    if(!cdashmm_is_option_selected('use_processing_fee')) {
        return false;
    }
    if(!cdashmm_is_option_selected('limit_processing_fee')) {
        return true;
    }
    if($action == 'signup') {
        return true;
    }
    return false;
}

function cdashmm_get_processing_fee($action, $cost, $membership_level){
    if(cdashmm_is_option_selected('limit_processing_fee') && $action != 'signup'){
        // No processing fee for renewals if the list_processing_fee option is selected
        return 0;
    }
    if(cdashmm_is_option_selected('disable_process_fee_for_free_memb')) {
        if($membership_level != null && $cost == 0) {
            // No processing fee for free memberships if disable_process_fee_for_free_memb option is selected
            return 0;
        }
    }
    return cdashmm_get_processing_fee_amount();
}

function cdashmm_get_tax_rate($member_amt, $processing_fee){
    if(!cdashmm_is_option_selected('charge_tax')){
        return 0;
    }

    $tax_rate = cdashmm_get_option_value('tax_rate');
    if(empty($tax_rate)) {
        return 0;
    }
    return $tax_rate;
}

function cdashmm_get_tax($member_amt, $processing_fee){
    $tax_rate = cdashmm_get_tax_rate($member_amt, $processing_fee);    
    if(cdashmm_is_option_selected('charge_tax_only_on_membership')){
        $taxable_amount = $member_amt;
    }else{
        $taxable_amount = $member_amt + $processing_fee;
    }

	$tax = round(( $tax_rate / 100 ) * $taxable_amount, 2 );
	return array($tax_rate, $taxable_amount, $tax);
}

function cdashmm_get_donation_amount(){
    if (cdashmm_is_option_selected('no_donation')) {
		$donation = 0;
	} else {
		$donation = +cdashmm_get_option_value('suggested_donation');
	}
	return $donation;
}

function cdashmm_mailchimp_subscribe($email, $merge_fields){
		//if(cdashmm_check_mailchimp_addon()){
			$cdashmc_options = get_option('cdash_addon_options');
		//}
			$list_id = $cdashmc_options['cdashmc_list'];
			$api_key = $cdashmc_options['cdash_mailchimp_api_key'];
			$status = 'subscribed'; // "subscribed" or "unsubscribed" or "cleaned" or "pending"
			$email = $email;
			$merge_fields = $merge_fields;
				cdashmc_add_mailchimp_subscriber( $email, $status, $list_id, $api_key, $merge_fields );

}

function cdashmm_get_connected_user_for_existing_business($business_id){
	if( isset( $business_id ) && $business_id !== '' ){
		$person_id = cdashmm_get_person_id_from_business_id($business_id, true);
		$user_id = cdashmm_get_user_id_from_people_id($person_id, true);
		$user_info = get_userdata($user_id);
		//$user_meta = get_user_meta( $user_id );
	}
	return $user_id;
}

function cdashmm_get_business_billing($business_id){
    global $billing_metabox;
    $billingmeta = $billing_metabox->the_meta();
    $business_billing = '<p>' . get_post_field( 'post_title', $business_id ) . '<br />'; // this is a convoluted way to get the title, but it bypasses the title filter
    if( isset ( $billingmeta['billing_address'] ) )
        $business_billing .= $billingmeta['billing_address'] . '<br />';
    if( isset ( $billingmeta['billing_city'] ) )
        $business_billing .= $billingmeta['billing_city'] . ', ';
    if( isset ( $billingmeta['billing_state'] ) )
        $business_billing .= $billingmeta['billing_state'] . ' ';
    if( isset ( $billingmeta['billing_zip'] ) )
        $business_billing .= $billingmeta['billing_zip'] . '<br />';
    if( isset ( $billingmeta['billing_countryp'] ) )
        $business_billing .= $billingmeta['billing_country'] . '<br />';
    if( isset ( $billingmeta['billing_phone'] ) )
        $business_billing .= $billingmeta['billing_phone'] . '<br />';
    if( isset ( $billingmeta['billing_email'] ) )
        $business_billing .= $billingmeta['billing_email'];
    $business_billing .= '</p>';

    return $business_billing;
}
?>
