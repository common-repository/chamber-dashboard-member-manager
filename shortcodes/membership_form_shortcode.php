<?php
if ( ! defined( 'ABSPATH' ) ) exit;
// ------------------------------------------------------------------------
// shortcode to display membership sign-up/renewal form
// ------------------------------------------------------------------------
function cdashmm_membership_signup_form($atts) {
	global $woocommerce;
	// Attributes
	extract( shortcode_atts(
		array(
			'action' => 'signup', // options: signup, renewal
			'customFieldsArray' => [],
			'busDetailsSectionTitle'	=> __('Business Details', 'cdashmm'),
			'customFieldsSectionTitle'	=> __('Custom Fields', 'cdashmm'),
            'moveCustomFields'	=> false,
            'addDescriptionField'	=> false,
            'addLogoUpload' => false,
		), $atts )
    );
	// Enqueue stylesheet
	wp_enqueue_style( 'cdashmm-membership', plugin_dir_url( __DIR__) . 'css/cdashmm-membership.css' );
	// Enqueue ajax to make the form work
	wp_enqueue_script( 'html5validate', plugin_dir_url( __DIR__) . 'js/jquery.h5validate.js', array( 'jquery' ) );
	wp_enqueue_script( 'membership-form', plugin_dir_url( __DIR__) . 'js/membership.js', array( 'jquery' ));
  	wp_localize_script( 'membership-form', 'membershipformajax', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) ) );
	$options = get_option( 'cdashmm_options' );
	$paypal = true;
	if( (!isset( $options['paypal_email'] )) || ('' == $options['paypal_email']) ) {
  	$paypal = false;
  }
  $cdash_options = get_option( 'cdash_directory_options' );
  $currency = $cdash_options['currency'];
	global $member_form;
	$member_form = '';

	global $starting_price;
	$starting_price = 0;

	// Display form
	if( !isset( $currency ) ) {
		$member_form .= __( 'You have not entered in your currency settings.  In your WordPress dashboard, go to the Chamber Dashboard settings page to select what currency you accept.', 'cdashmm' );
	} else{
		$member_form .=
        '<form id="membership_form" name="membership_form" action="https://www.paypal.com/cgi-bin/webscr" method="post">
        <p class="explain">' . __( '* = Required') . '</p>
        <input name="cdashmm_membership_nonce" id="cdashmm_membership_nonce" type="hidden" value="' .wp_create_nonce( 'cdashmm_membership_nonce' ) . '">';
            
		$member_form .= '<fieldset><legend>';
		if(isset($busDetailsSectionTitle) && '' !== $busDetailsSectionTitle){
			$member_form .= $busDetailsSectionTitle;
		}else{
            $member_form .= __('Business Details', 'cdashmm');
		}
		$member_form .= '</legend>';
		$member_form .= cdashmm_membership_form_biz_name($action);
        $member_form .= cdashmm_membership_form_biz_category($action);
        $member_form .= cdashmm_membership_form_description_field($action, $addDescriptionField);
        $member_form .= cdashmm_membership_form_logo_field($action, $addLogoUpload);
        $member_form .= cdashmm_membership_form_biz_billing();
        $member_form .= cdashmm_membership_form_biz_contact();
        $member_form .= cdashmm_membership_form_biz_main_address($action);
        $member_form .= cdashmm_display_referral_dropdown($action);
        $member_form .= '</fieldset>';
        $member_form .= cdashmm_display_custom_fields($customFieldsArray, $customFieldsSectionTitle);
        
        //if( (cdash_check_cdashmu_active()) || ( cdashmm_check_mm_connected_to_wc() ) || ( cdashmm_check_mm_connected_to_wc_payments() ) ) {
        if(cdashmm_show_user_registration()){
            $member_form .= cdashmm_membership_form_user_fields($action);
        }
        $member_form .= '<fieldset>
            <legend>Select Membership Level and Pay</legend>';

        $levels = cdashmm_get_levels();
        $membership_level_term = cdashmm_get_selected_membership_level_term($levels);
        $membership_level_id = ($membership_level_term == null) ? null : $membership_level_term->term_id;
        $donation = cdashmm_get_donation_amount();
        $membership_amounts = cdashmm_calculate_amounts($action, $membership_level_id, $donation);

        $member_form .= cdashmm_membership_form_biz_membership_level($membership_level_term, $levels, $membership_amounts, $action);
        $member_form .= cdashmm_membership_form_totals($action, $membership_level_term, $membership_amounts);

        //Check if MM Pro is active, WooCommerce is active and MM is connected to WC
        if(cdashmm_check_mm_connected_to_wc() || cdashmm_check_mm_connected_to_wc_payments()){
            $member_form .= cdashmm_wc_payments_method();
        }else{
            //Display Payment methods and hidden PayPal fields since WC Payments is not active
            $member_form .= cdashmm_membership_form_paypal_fields($paypal, $membership_amounts);
        }
        $member_form .= cdashmm_display_terms_cond();
        $member_form .= cdashmm_display_consent_box();
        $member_form .= cdashmm_mailchimp_subscribe_field($action);
        $member_form .= cdashmm_membership_form_submit_button($action);
        $member_form .= '</fieldset>';
		$member_form .= '</form>';
	}
	return $member_form;
}
add_shortcode( 'membership_form', 'cdashmm_membership_signup_form' );



function cdashmm_membership_form_biz_name($action){
	if(!isset($member_form)){
		$member_form = '';
	}
	$member_form .= '
	<p>
		<label>' . __( 'Business Name', 'cdashmm' ) . ' *</label>
		<input name="name" class="' . $action . '" type="text" id="name" required>
		<input type="hidden" name="cdashmm_form_action" id="cdashmm_form_action" value = "' . $action . '" />
	</p>';
	if($action == 'renewal'){
		$member_form .= '<div id="business-picker"></div>
		<input name="business_id" type="hidden" id="business_id" value="">';
	}
	return $member_form;
}

function cdashmm_membership_form_biz_category($action){
	if(!isset($member_form)){
		$member_form = '';
	}
	if($action == 'signup'){
		$terms = get_terms( array(
				'taxonomy' => 'business_category',
				'hide_empty' => false,
		) );
		$member_form .=
		'<p>
			<label>' . __( 'Business Category', 'cdashmm' ) . ' *</label>
			<select name="business_category" id="business_category" required>
			<option value="">Select One</option>';
			foreach($terms as $single_category){
				$category_id = $single_category->term_id;
				$category_slug = $single_category->slug;
				$category_name = $single_category->name;
				$member_form .= '<option value ="' . $category_id . '">'. $category_name .'</option>';
			}
			$member_form .= '
			</select>
		</p>';
	}
	return $member_form;
}

function cdashmm_membership_form_description_field($action, $addDescriptionField){
    if(!isset($member_form)){
		$member_form = '';
    }
    if(!is_admin()){
        if($action == 'signup'){
            if($addDescriptionField){
                $member_form .= '<p>
                <label>' . __( 'Business Description', 'cdashmm' ) . '</label></p>';
                // Turn on the output buffer
                ob_start();
                $settings = array( "wpautop" => true, 'editor_height' => '200', 'media_buttons' => false );
    
                // Echo the editor to the buffer
                wp_editor( '', 'memb_form_desc', $settings );
    
                // Store the contents of the buffer in a variable
                $editor_contents = ob_get_clean();
                $member_form .= $editor_contents;

                //$member_form .= '</p>';
                
            }
        }
    }
    return $member_form;
}

function cdashmm_membership_form_logo_field($action, $addLogoUpload){
    if(!isset($member_form)){
		$member_form = '';
    }
    if($action == 'signup'){
        if($addLogoUpload){
            $member_form .= '<p>
            <label>' . __( 'Business Logo', 'cdashmm' ) . '</label>';
            $member_form .= '<input id="bus_logo" type="file" name="bus_logo" value=""/>';
            $member_form .= '</p>';
        }
        return $member_form;
    }
}

function cdashmm_membership_form_biz_billing(){
	if(!isset($member_form)){
		$member_form = '';
	}
	$member_form .=
	'<p>
		<label>' . __( 'Billing Address', 'cdashmm' ) . ' *</label>
		<input name="address" type="text" id="address" required>
	</p>
	<p>
		<label>' . __( 'City', 'cdashmm' ) . ' *</label>
		<input name="city" type="text" id="city" required>
	</p>
	<p>
		<label>' . __( 'State/Province', 'cdashmm' ) . ' *</label>
		<input name="state" type="text" id="state" required>
	</p>
	<p>
		<label>' . __( 'Zip/Postal Code', 'cdashmm' ) . ' *</label>
		<input name="zip" type="text" id="zip" required>
	</p>
	<p>
		<label>' . __( 'Country', 'cdashmm' ) . ' *</label>';
		if(cdashmm_check_mm_connected_to_wc_payments()){
			$member_form .= cdashwc_countries_dropdown();
		}else{
			$member_form .= '<input name="country" type="text" id="country" required>';
		}

	$member_form .= '</p>';
	return $member_form;
}

function cdashmm_membership_form_biz_contact(){
	if(!isset($member_form)){
		$member_form = '';
	}
	$options = get_option( 'cdashmm_options' );
	if(isset($options['phone_number_placeholder'])){
		$phone_placeholder = $options['phone_number_placeholder'];
	}else{
		$phone_placeholder = '';
	}
	$member_form .= '<p>
		<label>' . __( 'Email', 'cdashmm' ) . ' *</label>
		<input name="email" type="email" id="email" required>
		<span id="email_error"></span>
	</p>
	<p>
		<label>' . __( 'Phone Number', 'cdashmm' ) . ' *</label>
		<input name="phone" placeholder ="'.$phone_placeholder.'" type="text" id="phone" required>
	</p>';
	return $member_form;
}

function cdashmm_membership_form_biz_main_address($action){
	if(!isset($member_form)){
		$member_form = '';
	}
	if($action == 'signup'){
		$member_form .= '<p>
		<input type="checkbox" name="biz_main_address" id="biz_main_address" checked="checked" /> <label  class="label_right">' .__('Use this billing address as my business address.', 'cdashmm') . '</label>';
	}
	return $member_form;
}

// Returns a valid member level term object if $_GET['membership_level'] is set to valid slug
// Otherwise it returns null
function cdashmm_get_selected_membership_level_term($levels) {
    $membership_level = cdashmm_get_membership_level();
	if($membership_level){
        foreach( $levels as $level ) {
            if ($membership_level == $level->slug) {
                return $level;
            }
        }
    }
    return null;
}

function cdashmm_get_membership_level(){
	$get_membership_level = '';
	if( isset( $_GET['membership_level'] ) && $_GET['membership_level'] != '' ) {
		$get_membership_level = sanitize_text_field( $_GET['membership_level'] );
	}
	return $get_membership_level;
}

function cdashmm_get_pre_determined_level($levels){
	$predetermined_level = false;
	$get_membership_level = cdashmm_get_membership_level();
	if($get_membership_level){
	//if( isset( $_GET['membership_level'] ) && $_GET['membership_level'] != '' ) {
		// we have a membership level parameter, so first let's make sure it matches an existing membership level
		$level_slugs = array();
		foreach( $levels as $level ) {
			$levelid = $level->term_id;
			$level_slugs[] = $level->slug;
		}
		//if( in_array( $_GET['membership_level'], $level_slugs ) ) {
			if( in_array( $get_membership_level, $level_slugs ) ) {
			$predetermined_level = true;
		}
	}
	return $predetermined_level;
}

function cdashmm_get_starting_price($get_membership_level){
	$starting_price = 0;
	$levels = cdashmm_get_levels();
	$predetermined_level = cdashmm_get_pre_determined_level($levels);
	if( true == $predetermined_level ) {
		$this_level = get_term_by( 'slug', $get_membership_level, 'membership_level' );
		$levelid = $this_level->term_id;
		$starting_price += cdashmm_get_membership_level_price($levelid);
	}else{
		$starting_price = 0;
	}
	return $starting_price;
}

function cdashmm_get_levels(){
	$args = 'orderby=term_order&order=ASC&hide_empty=0';
	$levels = get_terms( 'membership_level', $args );
	return $levels;
}

function cdashmm_membership_form_biz_membership_level($membership_level_term, $levels, $membership_amounts, $action){
	if(!isset($member_form)){
		$member_form = '';
	}

    $options = get_option('cdashmm_options');
	
	$member_form .= '<p>
		<label>' . __( 'Membership Level', 'cdashmm' ) . ' *</label>';
		
		$predetermined_level = cdashmm_get_pre_determined_level($levels);
		
	if( $membership_level_term ) {
        $member_price = $membership_amounts['membership_fee'];
        $price = cdashmm_display_price( $member_price );
        $member_form .= '<input class="' .$membership_level_term->term_id .'" name="level" type="text" id="level" disabled value="' . $membership_level_term->name . ' - ' . $price . '">';
        $member_form .= '<input type="hidden" value="'.$member_price.'" name="membership_amt" id="membership_amt">';
    } else {
        $member_form .= '<select name="level" id="level" required>';
        $member_form .=	'<option value=""></option>';
        foreach( $levels as $level ) {
            $selected = '';
            $levelid = $level->term_id;
            $price = cdashmm_display_price( cdashmm_get_membership_level_price($levelid) );
            $hide_free_memb_level = cdashmm_hide_free_membership_level($action, cdashmm_get_membership_level_price($levelid));
            if(!$hide_free_memb_level){
                $member_form .= '<option value="' . $level->term_id . '">' . $level->name . ':&nbsp;' . $price . '</option>';
            }
        }
        $member_form .= '</select></p>';
        $member_form .= '<input type="hidden" value="" name="membership_amt" id="membership_amt">';
    }
	return $member_form;
}

function cdashmm_membership_form_totals($action, $membership_level_term, $membership_amounts){
	if(!isset($member_form)){
		$member_form = '';
	}
	$options = get_option('cdashmm_options');
    if (cdashmm_show_processing_fee($action)) {
        $member_form .= '<p>
        <label>' . __( 'Processing Fee', 'cdashmm' ) . '</label>
        <input disabled name="processing" type="number" min="0" id="processing" value="' . $membership_amounts['processing_fee'] . '">';
        $member_form .= '</p>';
    }
	$member_form .= '
	<input name="subtotal" type="hidden" id="subtotal" value="' . $membership_amounts['taxable_amount'] . '">';
    if (!cdashmm_is_option_selected('no_donation')) {
		$member_form .= '<p>
		<label>' . __( 'Optional Donation', 'cdashmm' ) . '</label>
		<input name="donation" type="number" min="0" id="donation" value="' . $membership_amounts['donation'] . '">';
		if( isset( $options['donation_explanation'] ) ) {
			$member_form .= '<br /><span class="donation_explanation">' . $options['donation_explanation'] . '</span>';
		}
		$member_form .= '</p>';
	}

	if( cdashmm_is_option_selected('charge_tax') ) {
		$member_form .= '<p>
					<label>' . __( 'Tax', 'cdashmm' ) . '</label>
					<input type="hidden" name="tax-rate" id="tax-rate" value="' . $membership_amounts['tax_rate'] . '">
					<input disabled name="tax" type="number" step=".01" min="0" id="tax" value="' . $membership_amounts['tax'] . '">';
		$member_form .= '</p>';
	}

    $member_form .= '
	<p class="total">
		<label>' . __( 'Total Due: ', 'cdashmm' ) . '</label>
		<input name="total" id="total" class="total" value="' . $membership_amounts['total'] . '" disabled>
	</p>';
	return $member_form;
}


function cdashmm_membership_form_submit_button($action){
	if(!isset($member_form)){
		$member_form = '';
	}
	$options = get_option('cdashmm_options');
	if($action == 'renewal'){
		$submit_button_label = __('Renew', 'cdashmm');
	}else{
		if((isset($options['submit_button_label'])) && ($options['submit_button_label'] != '') ){
			$submit_button_label = $options['submit_button_label'];
		}else{
			$submit_button_label = __('Join Now', 'cdashmm');
		}
	}
	$member_form .= '<p>
		<input type="submit" value="' . $submit_button_label . '">
	</p>';
	$member_form .= '<p><input type="hidden" value="'. $action . '"></p>';
	return $member_form;
}

function cdashmm_wc_payments_method(){
	if(!isset($member_form)){
		$member_form = '';
	}
	$member_form .= '<p class="method wc_payments" style="display:none;">
		<label>' . __( 'Payment Method: ', 'cdashmm' ) . '</label>
		<input name="method" type="radio" value="wc_payments" id="pay_wc_payments" class="method" checked>&nbsp;' . __( 'WC Payments', 'cdashmm' ) . '
	</p>';
	do_action( 'cdashmm_recurring_invoice_message', $member_form );
	return $member_form;
}

function cdashmm_membership_form_paypal_fields($paypal, $membership_amounts){
	$cdash_options = get_option( 'cdash_directory_options' );
	$currency = $cdash_options['currency'];
	if(!isset($member_form)){
		$member_form = '';
	}
	$options = get_option('cdashmm_options');
	if( true == $paypal ) {
		$member_form .= '<p class="method">
			<label>' . __( 'Payment Method: ', 'cdashmm' ) . '</label>
			<input name="method" type="radio" value="paypal" id="pay_paypal" class="method" checked>&nbsp;' . __( 'PayPal', 'cdashmm' ) . '<br />';
			if(!isset($options['disable_check_payments']) || $options['disable_check_payments'] == 0){
				$member_form .= '<input name="method" type="radio" value="check" id="pay_check" class="method">&nbsp;' . __( 'Check', 'cdashmm' ) . '
				</p>';
			}
		do_action( 'cdashmm_recurring_payments_fields', $membership_amounts );
	} else {
		$member_form .= '<p class="method">
			<label>' . __( 'Payment Method: ', 'cdashmm' ) . '</label>
			<input name="method" type="radio" value="check" id="pay_check" class="method" checked>&nbsp;' . __( 'Check', 'cdashmm' ) . '
		</p>';
		
	}
	$member_form .= '<div class="hidden-paypal-fields">';
	if(function_exists('cdashrp_requires_wordpress_version')){
		if(function_exists('cdashrp_default_payment_option')){
			$payment_option = cdashrp_default_payment_option();
			cdashrp_paypal_fields($payment_option);
		}else{
			remove_action( 'cdashmm_paypal_hidden_fields', 'cdashmm_paypal_cart_fields', 10 );
		}
	}
	do_action( 'cdashmm_paypal_hidden_fields', $membership_amounts );
	if((isset($options['paypal_return_url'])) && !empty($options['paypal_return_url']) ){
			$paypal_return_url = $options['paypal_return_url'];
	}else{
		$paypal_return_url = get_permalink();
	}


	$member_form .=
	'<input type="hidden" class="paypal business" name="business" value="' . $options['paypal_email'] . '">
	<input type="hidden" class="paypal return" name="return" value="' . $paypal_return_url . '">
	<input type="hidden" class="paypal currency_code" name="currency_code" value="' . $currency . '">
	<input type="hidden" class="paypal rm" name="rm" value="2">
	<input type="hidden" class="paypal no_shipping" name="no_shipping" value="1">
	<input type="hidden" class="paypal custom" name="custom" id="invoice_id" value="' . cdashmm_calculate_invoice_number() . '">
	<input type="hidden" class="paypal cbt" name="cbt" value="Return to ' . $options['orgname'] . '">
	<input type="hidden" class="paypal notify_url" name="notify_url" value="' . home_url() . '/?cdash-member-manager=paypal-ipn">
	</div>';
	return $member_form;
}

function cdashmm_display_consent_box(){
	if(!isset($member_form)){
		$member_form = '';
	}
	$options = get_option('cdashmm_options');
	if(isset($options['show_consent_box']) && $options['show_consent_box'] == 1){
		if(($options['consent_box_text'] != '')) {
			$member_form .= "<p><input id='cdashmm_frontend_consent_box' class='cdashmm_frontend_consent_box' type='checkbox' name='cdashmm_frontend_consent_box' required /> ";
			$member_form .= "<label class='label_right'>" . $options['consent_box_text'] . "</label></p>";
		}
	}
	return $member_form;
}

function cdashmm_display_terms_cond(){
	if(!isset($member_form)){
		$member_form = '';
	}
	$options = get_option('cdashmm_options');
	if( ($options['terms_statement'] != '') ){
		$member_form .= '<p class="cdashmm_terms_statement">'. $options['terms_statement'] . '</p>';
	}
	return $member_form;
}

function cdashmm_display_referral_dropdown($action){
	if(!isset($member_form)){
		$member_form = '';
	}
	$options = get_option('cdashmm_options');

	if($action == "signup"){
		if(isset($options['show_referral_dropdown']) && $options['show_referral_dropdown'] == 1){
			if($options['referral_dropdown_list'] != ''){
				$referral_list = $options['referral_dropdown_list'];
				$referral_list_items = explode("\n", $referral_list);
				$member_form .= "<p>How did you hear about us?</p>";
				$member_form .= "<select name='referral_dropdown' id='referral_dropdown'>";
				$member_form .= "<option name='select_one'>Please select an option</option>";
				foreach ( $referral_list_items as $referral_list_item ) {
					$member_form .= "<option name='" . trim($referral_list_item) . "'>" . trim($referral_list_item) . "</option>";
				}
				$member_form .= "</select>";
			}
		}
	}
	return $member_form;
}

function cdashmm_membership_form_user_fields($action){
	//do this only if it is the sign up form
	if(!isset($member_form)){
		$member_form = '';
	}
	if($action == 'signup'){

			$member_form .= '<fieldset>
				<legend>User Registration</legend>
			<p>
				<label>' . __( 'First Name', 'cdashmm' ) . ' *</label>
				<input name="first_name" type="text" id="first_name" required>
			</p>
			<p>
				<label>' . __( 'Last Name', 'cdashmm' ) . ' *</label>
				<input name="last_name" type="text" id="last_name" required>
			</p>
			<p>
				<label>' . __( 'User Name', 'cdashmm' ) . ' *</label>
				<input name="username" type="text" id="username" required>
				<span id="username_error"></span>
			</p>
			<p>
				<label>' . __( 'Password', 'cdashmm' ) . ' *</label>
				<input name="password" type="password" id="password" required>
				<span id="password_error"></span>
			</p>
			</fieldset>';
	}
	else{
		$member_form .= '';
	}
	return $member_form;
}

//Display subscribe checkbox if MailChimp addon is active
function cdashmm_mailchimp_subscribe_field($action){
	if(!isset($member_form)){
		$member_form = '';
	}
	$options = get_option('cdashmm_options');

	if($action == 'signup'){
		if(cdashmm_check_mailchimp_addon()){
			$cdashmc_options = get_option('cdash_addon_options');
			if(isset($cdashmc_options['cdashmc_list'])){
				$list = $cdashmc_options['cdashmc_list'];
			}
			
			$message = $cdashmc_options['cdashmc_custom_newsletter_message'];
			if($cdashmc_options['cdash_mailchimp_api_key'] != '' && $list != ''){
					//$member_form .= "The subscribe box goes here.";
					$member_form .= cdashmc_subscribe_checkbox($list, $message);
					//$member_form .= $list;
			}
		}
	}
	return $member_form;
}
// ------------------------------------------------------------------------
// Display PayPal hidden fields
// ------------------------------------------------------------------------

function cdashmm_paypal_cart_fields($membership_amounts) {
	global $member_form;
	// note to self - this code is also in membership.js
	$member_form .=
	'<input type="hidden" class="paypal cart cmd" name="cmd" value="_cart">
	<input type="hidden" class="paypal cart upload" name="upload" value="1" />
	<input type="hidden" class="paypal cart item_name_1" name="item_name_1" value="' . __( 'Membership', 'cdashmm' ) . '">
	<input type="hidden" class="paypal cart amount_1" name="amount_1" id="amount_1" value="'.$membership_amounts['membership_fee'].'">
	<input type="hidden" class="paypal cart item_name_2" name="item_name_2" value="' . __( 'Donation', 'cdashmm' ) . '">
	<input type="hidden" class="paypal cart amount_2" name="amount_2" id="amount_2" value="' . $membership_amounts['donation'] . '">
	<input type="hidden" class="paypal cart item_name_3" name="item_name_3" value="' . __( 'Processing Fee', 'cdashmm' ) . '">
	<input type="hidden" class="paypal cart amount_3" name="amount_3" id="amount_3" value="' . $membership_amounts['processing_fee'] . '">
	<input type="hidden" class="paypal cart item_name_4" name="item_name_4" value="' . __( 'Tax', 'cdashmm' ) . '">
  <input type="hidden" class="paypal cart amount_4" name="amount_4" id="amount_4" value="' . $membership_amounts['tax'] . '">';
}
add_action( 'cdashmm_paypal_hidden_fields', 'cdashmm_paypal_cart_fields', 10 );

// ------------------------------------------------------------------------
// AJAX functions for membership form
// ------------------------------------------------------------------------
// AJAX - when a membership level is selected, find the price and send it to the form
function cdashmm_update_total_amount_ajax() {
    if ( !wp_verify_nonce( $_POST['nonce'], "cdashmm_membership_nonce")) {
        exit( "There was an error." );
    }
    $levelid = (int)sanitize_text_field($_POST['level_id']);
    $donation = floatval(sanitize_text_field( $_POST['donation'] ));
    $form_action = sanitize_text_field( $_POST['form_action'] );

    $results = cdashmm_calculate_amounts($form_action, $levelid, $donation);
    die( json_encode($results) );
}
add_action( 'wp_ajax_nopriv_cdashmm_update_total_amount', 'cdashmm_update_total_amount_ajax' );
add_action( 'wp_ajax_cdashmm_update_total_amount', 'cdashmm_update_total_amount_ajax' );

function cdashmm_calculate_amounts($action, $membership_level, $donation) {
    $membership_price = cdashmm_get_membership_level_price($membership_level);
    $cost_no_commas = str_replace( ',', '', $membership_price );
    $cost = floatval( $cost_no_commas );

    $processing_fee = cdashmm_get_processing_fee($action, $cost, $membership_level);

    list($tax_rate, $taxable_amount, $tax) = cdashmm_get_tax($cost, $processing_fee);

    $results = array();
    $results['membership_fee'] = $cost;
    $results['processing_fee'] = $processing_fee;
    $results['tax'] = $tax;
    $results['tax_rate'] = $tax_rate;
    $results['taxable_amount'] = $taxable_amount;
    $results['total'] = round($cost + $processing_fee + $tax + $donation, 2);
    $results['donation'] = $donation;

    return $results;
}

// AJAX - when a business name is entered, check whether the business is already in the database
function cdashmm_find_existing_business() {
    if ( !wp_verify_nonce( $_POST['nonce'], "cdashmm_membership_nonce")) {
        exit( "There was an error." );
    }
    $name = sanitize_text_field($_POST['name']);
    $results = '';
    $args = array(
        'post_type' => 'business',
        'post_title_like' => $name,
        'posts_per_page' => -1,
        'orderby' => 'title',
        'order' => 'ASC',
    );
    $bus_query = new WP_Query( $args );

    // The Loop
    if ( $bus_query->have_posts() ) :
    	$results .= '<div class="alert"><p>' . __( 'It looks like your business is already in our database!  To verify, select your business below:', 'cdashmm' ) . '</p>';
	    while ( $bus_query->have_posts() ) : $bus_query->the_post();
	    	$results .= '<input type="radio" name="business_id" class="business_id" value="' . get_the_id() . '">&nbsp;' . get_the_title() . '<br />';
	    endwhile;
	    $results .= '<input type="radio" name="business_id" class="business_id" value="new">&nbsp;None of the above<br /></div>';
    endif;
    // Reset Post Data
    wp_reset_postdata();
    die($results);
}
add_action( 'wp_ajax_nopriv_cdashmm_find_existing_business', 'cdashmm_find_existing_business' );
add_action( 'wp_ajax_cdashmm_find_existing_business', 'cdashmm_find_existing_business' );

// AJAX - when an existing business is selected, fill in the form
function cdashmm_prefill_membership_form() {
    if ( !wp_verify_nonce( $_POST['nonce'], "cdashmm_membership_nonce")) {
        exit( "There was an error." );
    }
    $business_id = (int) sanitize_text_field($_POST['business_id']);
    if( "new" == $_POST['business_id'] ) {
    	die();
    }
    $results = array();
    $args = array(
        'post_type' => 'business',
        'p' => $business_id,
    );
    $bus_query = new WP_Query( $args );

    // The Loop
    if ( $bus_query->have_posts() ) :
	    while ( $bus_query->have_posts() ) : $bus_query->the_post();
			$results['business_id'] = get_the_id();
			$results['business_name'] = get_the_title();
			global $billing_metabox;

			$billingmeta = $billing_metabox->the_meta();

			if(isset($billingmeta['billing_address']) || isset($billingmeta['billing_city']) || isset($billingmeta['billing_state']) || isset($billingmeta['billing_zip']) || isset($billingmeta['billing_country']) || isset($billingmeta['billing_phone']) || isset($billingmeta['billing_email']) ){
				// the billing address exists, so we'll insert it into the form
				if( isset ( $billingmeta['billing_address'] ) )
					 $results['address'] = $billingmeta['billing_address'];
				if( isset ( $billingmeta['billing_city'] ) )
					$results['city'] = $billingmeta['billing_city'];
				if( isset ( $billingmeta['billing_state'] ) )
					$results['state'] = $billingmeta['billing_state'];
				if( isset ( $billingmeta['billing_zip'] ) )
					$results['zip'] = $billingmeta['billing_zip'];
				if( isset ( $billingmeta['billing_country'] ) )
					$results['country'] = $billingmeta['billing_country'];
				if( isset ( $billingmeta['billing_phone'] ) )
					$results['phone'] = $billingmeta['billing_phone'];
				if( isset ( $billingmeta['billing_email'] ) )
					$results['email'] = $billingmeta['billing_email'];
			} else {
				// the billing address doesn't exist, so let's grab the first address and insert it instead
				global $buscontact_metabox;
				$contactmeta = $buscontact_metabox->the_meta();
				if( isset( $contactmeta['location'] ) ) {
					$locations = $contactmeta['location'];
					if( isset( $location['donotdisplay'] ) && "1" == $location['donotdisplay'] ) {
						continue;
					} else {
						foreach ( $locations as $location ) {
							if( isset( $location['phone'] ) && $location['phone'] != '' ) {
								$phones = $location['phone'];
								foreach($phones as $phone) {
									$results['phone'] = $phone['phonenumber'];
									break; // we only need one, so we'll stop the loop here
								}
							}
							if( isset( $location['email'] ) && $location['email'] != '' ) {
								$emails = $location['email'];
								foreach($emails as $email) {
									$results['email'] = $email['emailaddress'];
									break; // we only need one, so we'll stop the loop here
								}
							}
							if( isset ( $location['address'] ) )
								$results['address'] = $location['address'];
							if( isset ( $location['city'] ) )
								$results['city'] = $location['city'];
							if( isset ( $location['state'] ) )
								$results['state'] = $location['state'];
							if( isset ( $location['zip'] ) )
								$results['zip'] = $location['zip'];
							if( isset ( $location['country'] ) )
								$results['country'] = $location['country'];
							break; // we only need one, so we'll stop the loop here
						}
					}
				}
			}
	    endwhile;
    endif;

    // Reset Post Data
    wp_reset_postdata();
    // $results = json_encode($results);
   	wp_send_json($results);
    die();
}
add_action( 'wp_ajax_nopriv_cdashmm_prefill_membership_form', 'cdashmm_prefill_membership_form' );
add_action( 'wp_ajax_cdashmm_prefill_membership_form', 'cdashmm_prefill_membership_form' );

//Check if username is already registered
function cdashmm_check_username(){
	// check for the nonce and get out of here right away if it isn't right
	if ( !wp_verify_nonce( $_POST['nonce'], "cdashmm_membership_nonce")) {
	        exit( "There was an error." );
	}
	$response = array();
	$username = sanitize_text_field($_POST['username']);

	if ( 4 > strlen( $username ) ) {
    	$response['text'] = __('Username too short. At least 4 characters is required', 'cdashmm' );
			$response['error'] = 'ui-state-error';
	}else if(username_exists($username)){
			$response['text'] = __('Sorry, this username is unavailable', 'cdashmm');
			$response['error'] = 'ui-state-error';
	}else if ( ! validate_username( $username ) ) {
			$response['text'] = __( 'Sorry, the username you entered is not valid', 'cdashmm');
			$response['error'] = 'ui-state-error';
	}else{
			$response['text'] = __('The username you chose is available', 'cdashmm');
			$response['error'] = 'ui-state-valid';
	}

	wp_send_json($response);
    die();
}
add_action( 'wp_ajax_nopriv_cdashmm_check_username', 'cdashmm_check_username' );
add_action( 'wp_ajax_cdashmm_check_username', 'cdashmm_check_username' );


//Check if password is more than 5 characters
function cdashmm_check_password(){
	// check for the nonce and get out of here right away if it isn't right
	if ( !wp_verify_nonce( $_POST['nonce'], "cdashmm_membership_nonce")) {
	        exit( "There was an error." );
	}
	$response = array();
	$password = sanitize_text_field($_POST['password']);

	if ( 5 > strlen( $password ) ) {
    	$response['text'] = __('Password too short. At least 5 characters is required', 'cdashmm' );
			$response['error'] = 'ui-state-error';
	}else{
		$response['text'] = ' ';
		$response['error'] = 'ui-state-valid';
	}
	wp_send_json($response);
    die();
}
add_action( 'wp_ajax_nopriv_cdashmm_check_password', 'cdashmm_check_password' );
add_action( 'wp_ajax_cdashmm_check_password', 'cdashmm_check_password' );

//Check if email is valid
function cdashmm_check_email($action){
	// check for the nonce and get out of here right away if it isn't right
	if ( !wp_verify_nonce( $_POST['nonce'], "cdashmm_membership_nonce")) {
	        exit( "There was an error." );
	}
	$response = array();
	$cdashmm_form_action = sanitize_text_field($_POST['cdashmm_form_action']);
	$email = ($_POST['email']);
			if ( email_exists( $email ) ) {
				if($cdashmm_form_action == "signup"){
					$response['text'] = __('This email address already exists. Please use another email to sign up.', 'cdashmm' );
					$response['error'] = 'ui-state-error';
				}
			}elseif(!is_email($email)){
				$response['text'] = __('This email is not valid. Please enter a valid email address.', 'cdashmm');
				$response['error'] = 'ui-state-error';
			}else{
				$response['text'] = ' ';
				$response['error'] = 'ui-state-valid';
			}
			wp_send_json($response);
		    die();
}
add_action( 'wp_ajax_nopriv_cdashmm_check_email', 'cdashmm_check_email' );
add_action( 'wp_ajax_cdashmm_check_email', 'cdashmm_check_email' );

/**
 * Check Image
 * @since 0.1.0
 */
function cdashmm_sanitize_image( $input ){
 
    /* default output */
    $output = '';
 
    /* check file type */
    $filetype = wp_check_filetype( $input );
    $mime_type = $filetype['type'];
 
    /* only mime type "image" allowed */
    if ( strpos( $mime_type, 'image' ) !== false ){
        $output = $input;
    }
 
    return $output;
}

// AJAX - save form data before it gets sent off to PayPal

function cdashmm_process_membership_form() {
    $options = get_option( 'cdashmm_options' );
	// check for the nonce and get out of here right away if it isn't right
    if ( !wp_verify_nonce( $_POST['nonce'], "cdashmm_membership_nonce" ) ) {
        exit( "There was an error." );
    }
 	// gather all of the variables
	if(isset($_POST['business_id']) && '' != $_POST['business_id']){
		$business_id = (int) sanitize_text_field($_POST['business_id']);
	}
    $name = sanitize_text_field($_POST['name']);
    if(isset($_POST['business_description'])){
        //$description = sanitize_textarea_field( $_POST['business_description'] );
        $description = $_POST['business_description'];
    }else{
        $description = __( 'This business draft was automatically generated by the membership form.', 'cdashmm' );
    }

    if(isset($_POST['business_category'])){
        $business_category = sanitize_text_field($_POST['business_category']);
    }
    $address = sanitize_text_field($_POST['address']);
    $city = sanitize_text_field($_POST['city']);
	$state = sanitize_text_field($_POST['state']);
	$zip = sanitize_text_field($_POST['zip']);
	$country = sanitize_text_field($_POST['country']);
	$phone = sanitize_text_field($_POST['phone']);
	$email = sanitize_email($_POST['email']);
	if(isset($_POST['biz_main_address'])){
		$set_biz_address = true;
	}else{
		$set_biz_address = false;
	}
    $today = date( 'Y-m-d' );
    $total = sanitize_text_field($_POST['total']);
    $membership_level = sanitize_text_field($_POST['membership_level']);
    $member_amt = sanitize_text_field($_POST['member_amt']);
    
    if( isset( $_POST['donation'] ) ) {
    	$donation = sanitize_text_field( $_POST['donation'] );
    }else{
		$donation = 0;
	}
	$cdashmm_form_action = $_POST['cdashmm_form_action'];
	$processing_fee = cdashmm_get_processing_fee($cdashmm_form_action, $member_amt, $membership_level);
	if(isset($_POST['tax'])){
		$tax = sanitize_text_field($_POST['tax']);
	}else{
		$tax = 0;
	}

	if(cdashmm_check_mm_connected_to_wc() || cdashmm_check_mm_connected_to_wc_payments() ){
		$invoice_id = cdashmm_calculate_invoice_number();
	}else{
		$invoice_id = sanitize_text_field($_POST['invoice_id']);
	}

	if(isset($_POST['first_name'])){
		$first_name = sanitize_text_field($_POST['first_name']);
	}else{
		$first_name = '';
	}
	if(isset($_POST['last_name'])){
		$last_name = sanitize_text_field($_POST['last_name']);
	}else{
		$last_name = '';
	}
	if(isset($_POST['username'])){
		$username = sanitize_user($_POST['username']);
	}else{
		$username = '';
	}
	if(isset($_POST['password'])){
		$password = sanitize_text_field($_POST['password']);
	}else{
		$password = '';
	}
	if(isset($_POST['referral'])){
		$referral = sanitize_text_field($_POST['referral']);
	}else{
		$referral = '';
	}
	if(isset($_POST['cdashmc_subscribe_checkbox'])){
		$mailchimp_subscribe = true;
	}else{
		$mailchimp_subscribe = false;
    }
    $customFieldValues = array();
	if(isset($_POST['custom_field_values']) && is_array($_POST['custom_field_values'])){
        foreach($_POST['custom_field_values'] as $custom_field_value){
            array_push($customFieldValues, sanitize_text_field( $custom_field_value ));
        }
	}else{
		$customFieldValues = '';
    }
    
    $customFieldNames = array();
	if(isset($_POST['custom_field_names']) && is_array($_POST['custom_field_names'])){
        foreach($_POST['custom_field_names'] as $custom_field_name){
            array_push($customFieldNames, sanitize_text_field( $custom_field_name ));
        }
	}else{
		$customFieldNames = '';
    }
    
    $options = get_option( 'cdashmm_options' );

	// Create or update the business
    if( isset( $business_id ) && $business_id !== '' ) {
		// we already have a business, so let's update it
		cdashmm_create_update_business($business_id, $address, $city, $state, $zip, $country, $email, $phone, $set_biz_address, $membership_level, $referral, '', $customFieldNames, $customFieldValues);

		$user_id = cdashmm_get_connected_user_for_existing_business($business_id);
	} else {
		// we need to create a new business
		$business_details = array(
			'post_status'    	=> 'draft',
			'post_type'      	=> 'business',
			'post_title'		=> $name,
			'post_content'  	=> $description,
		);
		$business_id = wp_insert_post( $business_details );
		cdashmm_create_update_business($business_id, $address, $city, $state, $zip, $country, $email, $phone, $set_biz_address, $membership_level, $referral, $business_category, $customFieldNames, $customFieldValues);

		//Create User
		//If cdashmu is active, call cdashmu_complete_user_registration(fields). This will create the user as business editor, people record and connect everything.
		if(cdash_check_cdashmu_active()) {
			$user_id = cdashmu_complete_user_registration($first_name, $last_name, $username, $password, $email, $name, $business_id, false);
		}else if(cdashmm_check_mm_connected_to_wc() || cdashmm_check_mm_connected_to_wc_payments() || cdashmm_enable_rc()){
			$user_id = cdashmm_user_registration($first_name, $last_name, $username, $password, $email, $business_id, false, 'subscriber', 'Created by Chamber Dashboard Member Manager');
		}

	}
    $business_name = get_the_title( $business_id );

		//Create an invoice
		$invoice = cdashmm_create_and_update_invoice($business_id, $business_name, $invoice_id, $total, $today, $membership_level, $member_amt, $tax, $donation, $processing_fee);

		//If MM is connected to WC, create an order and connect the order to the invoice
		if(cdashmm_check_mm_connected_to_wc() || cdashmm_check_mm_connected_to_wc_payments()){
				$order_id = cdashmmpro_create_wc_order($business_id, $first_name, $last_name, $name, $email, $phone, $address, $city, $state, $zip, $country, $membership_level, $total, $invoice, $user_id, $donation, $tax);
		}else{
			$order_id = '';
		}
		//Get the post method
		$method = cdashmm_get_post_method();

		//Set the invoice status cdashmm_set_invoice_status()
		cdashmm_set_invoice_status($method, $invoice);

		cdashmm_emails_to_business_and_admin($invoice, $email, $business_id, $total, $method);

		//If MailChimp addon is active, subscribe to the selected list
		if($mailchimp_subscribe){
			if(isset($first_name) && isset($last_name)){
					$merge_fields = array('FNAME' => $first_name,'LNAME' => $last_name);
			}else{
				$merge_fields = array('FNAME' => '','LNAME' => '');
			}
			cdashmm_mailchimp_subscribe($email, $merge_fields);

		}

		//Get the results page cdashmm_get_results_page()
		$results = cdashmm_get_results_page($method, $invoice, $order_id, $total);

     //die($results);
     die( json_encode($results) );
}//End of process memberhsip form
add_action( 'wp_ajax_nopriv_cdashmm_process_membership_form', 'cdashmm_process_membership_form' );
add_action( 'wp_ajax_cdashmm_process_membership_form', 'cdashmm_process_membership_form' );

function cdashmm_create_update_business($business_id, $address, $city, $state, $zip, $country, $email, $phone, $set_biz_address, $membership_level, $referral, $business_category, $customFieldNames, $customFieldValues){

    cdashmm_add_business_logo($business_id);
	
	$custom_field_names = array();
	$custom_field_values = array();
	if(!empty($customFieldNames) && '' !== $customFieldNames){
		$i = 0;
		foreach($customFieldNames as $customfieldname){
			$customfieldname = substr($customfieldname, 7);
			array_push($custom_field_names, $customfieldname);
			$i++;
		}
	}

	//Update custom fields
	// add a serialised array for wpalchemy to work - see http://www.2scopedesign.co.uk/wpalchemy-and-front-end-posts/
	
	$fields = $customFieldNames;
	//$fields = array('_cdash_Test Field', '_cdash_Second field', '_cdash_Third Field');
	$str = $fields;
	update_post_meta( $business_id, 'custom_meta_fields', $str );

	// Create the array of custom fields for wpalchemy
	$customfields = $custom_field_names;

	//Looping through both the arrays (field names and values) and updating each individual custom field
	if(is_array($customFieldNames) && is_array($customFieldValues)){
		$it = new MultipleIterator();
		$it->attachIterator(new ArrayIterator($customFieldNames));
		$it->attachIterator(new ArrayIterator($customFieldValues));
		//Add more arrays if needed 

		foreach($it as $a) {
			update_post_meta( $business_id, $a[0], $a[1] );
		}
	}
	
	
	// add a serialised array for wpalchemy to work - see http://www.2scopedesign.co.uk/wpalchemy-and-front-end-posts/
	$fields = array('_cdash_billing_address', '_cdash_billing_city', '_cdash_billing_state', '_cdash_billing_zip', '_cdash_billing_country', '_cdash_billing_email', '_cdash_billing_phone' );
	$str = $fields;
	update_post_meta( $business_id, 'billing_meta_fields', $str );

	// Create the array of billing information for wpalchemy
	$billingfields = array(
			array(
			'billing_address',
			'billing_city',
			'billing_state',
			'billing_zip',
			'billing_country',
			'billing_phone',
			'billing_email',
			)
		);

	// update each individual field
	update_post_meta( $business_id, '_cdash_billing_address', $address );
	update_post_meta( $business_id, '_cdash_billing_city', $city );
	update_post_meta( $business_id, '_cdash_billing_state', $state );
	update_post_meta( $business_id, '_cdash_billing_zip', $zip );
	update_post_meta( $business_id, '_cdash_billing_country', $country );
	update_post_meta( $business_id, '_cdash_billing_email', $email );
	update_post_meta( $business_id, '_cdash_billing_phone', $phone );

	if($set_biz_address){
		// Get the geolocation data
		if( isset( $address ) ) {
			// ask Google for the latitude and longitude
			$rawaddress = $address;
			if( isset( $city ) ) {
				$rawaddress .= ' ' . $city;
			}
			if( isset( $state ) ) {
				$rawaddress .= ' ' . $state;
			}
			if( isset( $zip ) ) {
				$rawaddress .= ' ' . $zip;
			}
			if(function_exists('cdash_get_google_maps_api')){
				//$google_map_api_key = cdash_get_google_map_api();
				$google_map_api_key = cdash_get_google_maps_api_key();
			}
			$latitude = '';
			$longitude = '';
			if($google_map_api_key != ''){
				$map_address = urlencode( $rawaddress );
				$json = wp_remote_get( "https://maps.googleapis.com/maps/api/geocode/json?key='.$google_map_api_key.'&address=" . $map_address );
				$json = json_decode($json['body'], true);
				if( is_array( $json ) && $json['status'] == 'OK') {
					$latitude = $json['results'][0]['geometry']['location']['lat'];
					$longitude = $json['results'][0]['geometry']['location']['lng'];
				}
			}
		}

		//Add the address as the main business address
		$fields = array('_cdash_location');
		$str = $fields;
		update_post_meta( $business_id, 'buscontact_meta_fields', $str );

		// Get the phone number and put it in the array format wpalchemy expects
		$phone_numbers = array();
		$phone_numbers[]['phonenumber'] = $phone;

		// Get the email addresse and put it in the array format wpalchemy expects
		$emails = array();
		$emails[]['emailaddress'] = $email;

		//Create Business location fields array
		$locationfields = array(
				array(
				'altname' 	=>'',
				'address'	=> $address,
				'city'		=> $city,
				'state'		=> $state,
				'zip'		=> $zip,
				'country'   => $country,
				'hours'     => '',
				'latitude'	=> $latitude,
				'longitude'	=> $longitude,
				'url'		=> '',
				'phone'		=> $phone_numbers,
				'email'		=> $emails,
				)
			);

			// Add all of the post meta data in one fell swoop
			add_post_meta( $business_id, '_cdash_location', $locationfields );
	}

		//Add Business referral
		//if($referral){
			$referral_fields = array('_cdash_busreferral');
			$ref_str = $referral_fields;
			update_post_meta($business_id, 'busreferral_meta_fields', $ref_str);
			update_post_meta($business_id, '_cdash_busreferral', $referral);
		//}

	// add membership level
	wp_set_post_terms( $business_id, $membership_level, 'membership_level', false );
	if($business_category != ''){
		// add Business Category
		wp_set_post_terms( $business_id, $business_category, 'business_category', false );
	}

	//If Recurring Payments is active, set the Membership Renewal Date to be 1 year from today
	if(function_exists('cdashrp_requires_wordpress_version')){
			cdashrp_set_mrd($business_id);
	}

	/*if(cdash_check_wc_payments_active()){
		cdashrp_set_mrd($business_id);
	}*/
}

function cdashmm_add_business_logo($business_id){
    require_once(ABSPATH . "wp-admin" . '/includes/image.php');
    require_once(ABSPATH . "wp-admin" . '/includes/file.php');
    require_once(ABSPATH . "wp-admin" . '/includes/media.php');
    
    if ($_FILES) {
        //UPDATING THE LOGO FIELDS
        if($_FILES['bus_logo']['name']){
            $filename = $_FILES['bus_logo']['name'];

            // Check the type of file. We'll use this as the 'post_mime_type'.
            $filetype = wp_check_filetype( basename( $filename ), null );

            // Get the path to the upload directory.
            $wp_upload_dir = wp_upload_dir();

            // Prepare an array of post data for the attachment.
            $attachment = array(
                'guid'           => $wp_upload_dir['url'] . '/' . basename( $filename ), 
                'post_mime_type' => $filetype['type'],
                'post_title'     => preg_replace( '/\.[^.]+$/', '', basename( $filename ) ),
                'post_content'   => '',
                'post_status'    => 'inherit'
            );

            // Insert the attachment.
            $attach_id = media_handle_upload( 'bus_logo', $_POST['post_id'] );

            
            $fields = array('_cdash_buslogo');
            $str = $fields;
            update_post_meta($business_id, 'buslogo_meta_fields', $str );
            update_post_meta($business_id, '_cdash_buslogo', $attach_id);
        }
    }
}

//Create and Update the invoice
function cdashmm_create_and_update_invoice($business_id, $business_name, $invoice_id, $total, $today, $membership_level, $member_amt, $tax, $donation, $processing_fee){
	// Create an invoice for this transaction
	$invoice_details = array(
	'post_status'    	=> 'publish',
	'post_type'      	=> 'invoice',
	'post_title'		=> __( 'Membership for ', 'cdashmm' ) . $business_name,
	'post_content'  	=> __( 'This invoice was automatically generated by the membership form.', 'cdashmm' ),
	);
	$invoice = wp_insert_post( $invoice_details );
	// add a serialised array for wpalchemy to work
	$invoicefields = array(
		'_cdashmm_invoice_number',
		'_cdashmm_amount',
		'_cdashmm_duedate',
		'_cdashmm_item_membershiplevel',
		'_cdashmm_item_membershipamt',
		'_cdashmm_item_tax',
		'_cdashmm_item_donation',
		'_cdashmm_item_processing_fee',
		'_cdashmm_paidamt',
		'_cdashmm_paiddate',
		'_cdashmm_paymethod',
		'_cdashmm_transaction'
		);
	$str = $invoicefields;
	update_post_meta( $invoice, 'invoice_meta_fields', $str );

	// update the individual fields
	update_post_meta( $invoice, '_cdashmm_invoice_number', $invoice_id );
	update_post_meta( $invoice, '_cdashmm_amount', $total );
	update_post_meta( $invoice, '_cdashmm_duedate', $today );
	update_post_meta( $invoice, '_cdashmm_item_membershiplevel', $membership_level );
	update_post_meta( $invoice, '_cdashmm_item_membershipamt', $member_amt );
	update_post_meta( $invoice, '_cdashmm_item_tax', $tax);
	if( isset( $donation ) ) {
		update_post_meta( $invoice, '_cdashmm_item_donation', $donation );
	}
	if(isset($processing_fee)){
		update_post_meta( $invoice, '_cdashmm_item_processing_fee', $processing_fee );
	}
	
	// connect the invoice to the business
	p2p_type( 'invoices_to_businesses' )->connect( $invoice, $business_id, array(
	    'date' => current_time('mysql')
	) );

	return $invoice;
}//End of create invoice function

//Create WooCommerce order
function cdashmmpro_create_wc_order($business_id, $first_name, $last_name, $name, $email, $phone, $address, $city, $state, $zip, $country, $membership_level, $total, $invoice, $user_id, $donation, $tax){
	$wc_address = array(
	'first_name' => $first_name,
	'last_name'  => $last_name,	
	 'company'    => $name,
	 'email'      => $email,
	 'phone'      => $phone,
	 'address_1'  => $address,
	 'address_2'  => '',
	 'city'       => $city,
	 'state'      => $state,
	 'postcode'   => $zip,
	 'country'    => $country
 );
$options = get_option( 'cdashmm_options' );
$membership_amt = cdashmm_get_starting_price($membership_level);
$processing_fee = cdashmm_get_processing_fee($action, $membership_amt, $membership_level);
$product_id = cdashwc_membership_level_to_product_id($membership_level);
$order_id = cdashwc_create_wc_order($wc_address, $product_id, $total, $user_id, $donation, $tax, $processing_fee);
/*if(cdashmm_check_mm_connected_to_wc_payments()){
	$order_id = cdashwc_create_wc_order($wc_address, $product_id, $total, $user_id, $donation, $tax, $processing_fee);
}else if(cdashmm_check_mm_connected_to_wc()){
	$order_id = cdashmm_create_wc_order($wc_address, $product_id, $total, $user_id, $donation, $tax, $processing_fee);
}*/

// connect the invoice to the business
 p2p_type( 'invoices_to_shop_order' )->connect( $invoice, $order_id, array(
		 'date' => current_time('mysql')
 ) );

 // connect the business to the order
 p2p_type( 'businesses_to_shop_order' )->connect( $business_id, $order_id, array(
	'date' => current_time('mysql')
) );
 return $order_id;
}

//Get the post methods
function cdashmm_get_post_method(){
	if(isset($_POST['method']) && $_POST['method'] != ''){
		$method = sanitize_text_field( $_POST['method'] );
	}
	return $method;
}

//Set the invoice status
function cdashmm_set_invoice_status($method, $invoice){
	if( "paypal" == $method ) {
		// if paying with PayPal, mark invoice status as pending for now
		$pending = get_term_by( 'slug', 'pending', 'invoice_status' );
		wp_set_post_terms( $invoice, $pending->term_id, 'invoice_status', false );
	} elseif( "check" == $method ) {
		// if paying by check, mark invoice status as open
		$open = get_term_by( 'slug', 'open', 'invoice_status' );
		wp_set_post_terms( $invoice, $open->term_id, 'invoice_status', false );
	} elseif("wc_payments" == $method){
		$pending = get_term_by( 'slug', 'pending', 'invoice_status' );
		wp_set_post_terms( $invoice, $pending->term_id, 'invoice_status', false );
	}
}

//Send emails to businesses and admin
function cdashmm_emails_to_business_and_admin($invoice, $email, $business_id, $total, $method){
	// send email receipt to business
	$options = get_option( 'cdashmm_options' );
	
	//Send the emails to business only if the payment method is something other than wc_payments
	if($method !== 'wc_payments'){
		cdashmm_send_business_email($options, $invoice, $email, $business_id, $total, $method);
	}
	
	// send email to site admin
	cdashmm_send_admin_email($options, $invoice, $email, $business_id, $total, $method);
}

//Send email to businesses only if they pay by check
function cdashmm_send_business_email($options, $invoice, $email, $business_id, $total, $method){
	if($method != "check"){
		return;
	}
	$receipt_to = $email;
	$receipt_subject = __( 'Invoice from ', 'cdashmm' ) . $options['orgname'];
	$receipt_message = $options['check_message'];

	$receipt_message .= '<p><strong>' . __( 'View the invoice: ', 'cdashmm' ) . '</strong><a href="' . get_the_permalink( $invoice ) . '">' . get_the_permalink( $invoice ) . '</a></p>';
	
	$receipt_from = "From: " . $options['receipt_from_name'] . " <" . $options['receipt_from_email'] . ">";
    cdashmm_send_email( $receipt_from, $receipt_to, '', $receipt_subject, $receipt_message );
}
// send email to site admin
function cdashmm_send_admin_email($options, $invoice, $email, $business_id, $total, $method){
    $admin_to = $options['admin_email'];
	if($method == "check"){
		$admin_subject = __( 'New Payment By Check', 'cdashmm' );	
		$admin_message = '<p>' . get_the_title( $business_id ) . __( ' has just filled out your membership form, and will pay by check.', 'cdashmm' ) . '</p>';
	}else{
		$admin_subject = __( 'New Payment Received!', 'cdashmm' );
		$admin_message = '<p>' . get_the_title( $business_id ) . __( ' has just filled out your membership form.', 'cdashmm' ) . '</p>';
	}
	if($method == "paypal"){
		$admin_message .= '<p><strong>' . __( 'View the invoice: ', 'cdashmm' ) . '</strong><a href="' . get_the_permalink( $invoice ) . '">' . get_the_permalink( $invoice ) . '</a></p>';
	}

    $admin_message .= '<p><strong>' . __( 'Payment amount: ', 'cdashmm' ) . '</strong>' . cdashmm_display_price( $total ) . '</p>';

    $admin_message .= '<p><strong>' . __( 'View the business: ', 'cdashmm' ) . '</strong><a href="' . get_the_permalink( $business_id ) . '">' . get_the_permalink( $business_id ) . '</a></p>';
    if( "draft" == get_post_status( $business_id ) ) {
        $admin_message .= '<p>' . get_the_title( $business_id ) . __( ' is a new business, so you need to publish the new business before it will appear in your member directory.', 'cdashmm' ) . '</p>';
		$admin_message .= '<p><a href="' . get_edit_post_link( $business_id ) . '">' . __('Click here to approve the business listing.', 'cdashmm') . '</a></p>';
    }

  	$admin_from = "Chamber Dashboard <" . $options['receipt_from_email'] . ">";
    cdashmm_send_email( $admin_from, $admin_to, '', $admin_subject, $admin_message );
}

//Get the results page
function cdashmm_get_results_page($method, $invoice, $order_id, $total_amount){
    $results = array();
    $results['invoice_id'] = $invoice;
    if($total_amount == 0){
        $url = cdashmm_get_option_page_url('free_memb_level_redirect_url');
        if($url){
            $results['redirect'] = $url;
            return $results;
        }
    }

    if( "check" == $method ) {
		$results['redirect'] = get_permalink( $invoice );
	} elseif("wc_payments" == $method){
		$order = wc_get_order($order_id);
		$pay_now_url = $order->get_checkout_payment_url();
		$results['redirect'] = $pay_now_url;
	}	
	return $results;
}

//Display custom fields on the form
function cdashmm_display_custom_fields($customFieldsArray, $customFieldsSectionTitle){
	if(!isset($member_form)){
		$member_form = '';
	}

	if(!empty($customFieldsArray)){
		//$member_form .= '<fieldset>';
		$member_form .= '<fieldset>
			<legend>';
			if(isset($customFieldsSectionTitle) && '' !== $customFieldsSectionTitle){
                $member_form .= $customFieldsSectionTitle;
			}else{
                $member_form .= __('Custom Fields', 'cdashmm');
			}
		$member_form .= '</legend>';
		$member_form .= cdashmm_custom_fields_content($customFieldsArray);
		$member_form .= '</fieldset>';
	}else{
	}
	 
	return $member_form;
}

function cdashmm_custom_fields_content($customFieldsArray){
	if(!isset($member_form)){
		$member_form = '';
	}

	$cdash_options = get_option('cdash_directory_options');
	if(isset($cdash_options['bus_custom']) && is_array($cdash_options['bus_custom']) && array_filter($cdash_options['bus_custom']) != [] ) {
		$field_set = true;
          $customfields = $cdash_options['bus_custom'];
          $i = 1;
          if(!empty($customfields) && is_array($customfields)){
            foreach($customfields as $field){
                $field_name = $cdash_options['bus_custom'][$i]['name'];
                
                $field_type = $cdash_options['bus_custom'][$i]['type'];
                if(in_array($field_name, $customFieldsArray )){
                    $member_form .= $cdash_options['bus_custom'][$i]['name'] . '<br />';
                    if($field_type == "textarea"){
                        $member_form .= '<textarea name="_cdash_'.$field_name.'" class="custom_field" rows="4" columns="50" ></textarea>';
                    }elseif($type = "text"){
                        $member_form .= '<input class="custom_field" type="'.$field_type.'" name="_cdash_'.$field_name.'" />';
                    }
                    $member_form .= "<br />";
                }
                $i++;
            }
          }
		
	}else{
		$member_form .= "No custom fields found";
	}
	return $member_form;
}

function cdashmm_hide_free_membership_level($action, $membership_amount){
    $hide_free_memb_level = false;
    if($action == "signup"){
        return;
    }

    if($membership_amount != 0){
        return;
    }

    if(cdashmm_is_option_selected('no_free_member_in_renewal_form')){
        $hide_free_memb_level = true;
        
    }
    return $hide_free_memb_level;
}

?>