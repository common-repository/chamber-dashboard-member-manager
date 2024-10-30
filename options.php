<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/* Options Page for Chamber Dashboard Member Manager */

add_action( 'admin_enqueue_scripts', 'cdashmm_admin_enqueue_scripts' );
function cdashmm_admin_enqueue_scripts(){
  // enqueue the thickbox scripts and styles

  wp_enqueue_style( 'admin.css', plugins_url( 'css/admin.css', __FILE__ ));
  wp_enqueue_script( 'admin-js', plugin_dir_url(__FILE__) . 'js/admin.js', array( 'jquery' ) );
}

// --------------------------------------------------------------------------------------
// CALLBACK FUNCTION FOR: register_uninstall_hook(__FILE__, 'cdashmm_delete_plugin_options')
// --------------------------------------------------------------------------------------

// Delete options table entries ONLY when plugin deactivated AND deleted

function cdashmm_delete_plugin_options() {
	//delete_option('cdashmm_options');
}
// ------------------------------------------------------------------------------
// CALLBACK FUNCTION FOR: register_activation_hook(__FILE__, 'cdashmm_add_defaults')
// ------------------------------------------------------------------------------
// Define default option settings
function cdashmm_add_defaults() {
	$tmp = get_option('cdashmm_options');

	if(!isset($tmp['paypal_email']) ){
		$tmp['paypal_email'] = get_bloginfo( 'admin_email' );
	}

  if(!isset($tmp['paypal_return_url'])){
    $tmp['paypal_return_url'] = '';
  }

	if(!isset($tmp['orgname']) ){
		$tmp['orgname'] = get_bloginfo( 'name' );
	}

	if(!isset($tmp['receipt_subject']) ){
		$tmp['receipt_subject'] = __( 'Thank you for your payment!', 'cdashmm' );
	}

	if(!isset($tmp['receipt_message']) ){
		$tmp['receipt_message'] = __( 'We appreciate your support!  Payment details are below.', 'cdashmm' );
	}

	if(!isset($tmp['receipt_from_name']) ){
		$tmp['receipt_from_name'] = get_bloginfo( 'name' );
	}

	if(!isset($tmp['receipt_from_email']) ){
		$tmp['receipt_from_email'] = get_bloginfo( 'admin_email' );
	}

	if(!isset($tmp['check_message']) ){
		$tmp['check_message'] = __( 'We look forward to receiving your payment!', 'cdashmm' );
	}

	if(!isset($tmp['admin_email']) ){
		$tmp['admin_email'] = get_bloginfo( 'admin_email' );
	}

	if(!isset($tmp['no_donation']) ){
		$tmp['no_donation'] ='0';
	}

	if(!isset($tmp['suggested_donation']) ){
		$tmp['suggested_donation'] ='0';
	}

	if(!isset($tmp['lapse_membership']) ){
		$tmp['lapse_membership'] ='1';
	}

	if(!isset($tmp['hide_lapsed_members']) ){
		$tmp['hide_lapsed_members'] ='1';
	}

	if(!isset($tmp['invoice_from']) ){
		$tmp['invoice_from'] ='';
	}

	if(!isset($tmp['invoice_footer']) ){
		$tmp['invoice_footer'] ='';
	}

	if(!isset($tmp['donation_explanation']) ){
		$tmp['donation_explanation'] ='';
	}

  if(!isset($tmp['limit_processing_fee'])){
    $tmp['limit_processing_fee'] = 0;
  }

	if(!isset($tmp['use_processing_fee'])){
		$tmp['use_processing_fee'] = '1';
	}

	if(!isset($tmp['processing_fee_amount'] ) ){
		$tmp['processing_fee_amount'] = '10';
	}

	if(!isset($tmp['charge_tax'])){
		$tmp['charge_tax'] = '0';
    }
    
    if(!isset($tmp['charge_tax_only_on_membership'])){
        $tmp['charge_tax_only_on_membership'] = '0';
    }

	if(!isset($tmp['tax_rate'])){
		$tmp['tax_rate'] = '';
	}

	if(!isset($tmp['submit_button_label']) ){
		$tmp['submit_button_label'] = __('Join Now');
    }

    if(!isset($tmp['terms_statement'])){
        $tmp['terms_statement'] = __('<b>Terms & Conditions Statement:</b>
        Data submitted through this form will be used for the purpose of creating your directory listing. Please see our Privacy Policy for more information on how we protect and manage your data.', 'cdashmm');
    }

    if(!isset($tmp['show_consent_box']) ){
        $tmp['show_consent_box'] = '0';
    }

    if(!isset($tmp['consent_box_text'])){
        $tmp['consent_box_text'] = __('I consent to having ' . get_bloginfo( 'name' ) . ' collect my data via this form.', 'cdashmm');
    }

    if(!isset($tmp['show_referral_dropdown'])){
        $tmp['show_referral_dropdown'] = '0';
    }

    if(!isset($tmp['referral_dropdown_list'])){
        $temp['referral_dropdown_list'] = '';
    }

    if(!isset($tmp['cdashmm_addons'])){
        $tmp['cdashmm_addons'] = '';
    }

    if(!isset($tmp['enable_user_registration'])){
        $tmp['enable_user_registration'] = '0';
    }

    if(!isset($tmp['cdashmm_member_login_form'])){
        $tmp['cdashmm_member_login_form'] = '';
    }

    if(!isset($tmp['cdashmm_rc_info_page'])){
        $tmp['cdashmm_rc_info_page'] = '';
    }

    if(!isset($tmp['cdashmm_login_logout_link'])){
        $tmp['cdashmm_login_logout_link'] = '0';
    }

    if(!isset($tmp['cdashmm_selected_menu'])){
        $tmp['cdashmm_selected_menu'] = array();
    }

    if(!isset($tmp['disable_check_payments'])){
        $tmp['disable_check_payments'] = '0';
    }

    if(!isset($tmp['phone_number_placeholder'])){
        $tmp['phone_number_placeholder'] = '';
    }

    if(!isset($tpm['disable_process_fee_for_free_memb'])){
        $tmp['disable_process_fee_for_free_memb'] = '0';
    }

    if(!isset($tmp['free_memb_level_redirect_url']) ){
        $tmp['free_memb_level_redirect_url'] = '';
    }

    if(!isset($tmp['no_free_member_in_renewal_form'])){
        $tmp['no_free_member_in_renewal_form'] = 0;
    }
	update_option('cdashmm_options', $tmp);
}
//}
// Add menu page
function cdashmm_add_options_page() {
	//add_submenu_page( '/chamber-dashboard-business-directory/options.php', __( 'Member Manager Options', 'cdashmm' ), __( 'Member Manager Options', 'cdashmm' ), 'manage_options', 'cdashmm', 'cdashmm_options_page' );

}
// ------------------------------------------------------------------------------
// REGISTER AND RENDER SETTINGS
// ------------------------------------------------------------------------------
add_action( 'admin_init', 'cdashmm_options_init' );
function cdashmm_options_init(  ) {
	register_setting( 'cdashmm_plugin_options', 'cdashmm_options', 'cdashmm_validate_options' );
	add_settings_section(
		'cdashmm_main_section',
		__( 'Member Manager Settings', 'cdashmm' ),
		'cdashmm_options_section_callback',
		'cdashmm_plugin_options'
	);

    add_settings_section(
		'cdashmm_members_only_section',
		__( 'Members Only Settings', 'cdashmm' ),
		'cdashmm_members_only_section_callback',
		'cdashmm_plugin_options'
	);

    add_settings_section(
		'cdashmm_paypal_section',
		__( 'PayPal Settings', 'cdashmm' ),
		'cdashmm_paypal_section_callback',
		'cdashmm_plugin_options'
	);

  add_settings_section(
		'cdashmm_emails_section',
		__( 'Email Settings', 'cdashmm' ),
		'cdashmm_emails_section_callback',
		'cdashmm_plugin_options'
	);

  add_settings_section(
		'cdashmm_invoice_section',
		__( 'Invoice Settings', 'cdashmm' ),
		'cdashmm_invoice_section_callback',
		'cdashmm_plugin_options'
	);

  add_settings_section(
		'cdashmm_join_now_form_fields_section',
		__( 'Membership Form Fields', 'cdashmm' ),
		'cdashmm_join_now_form_fields_section_callback',
		'cdashmm_plugin_options'
    );

    add_settings_section(
		'cdashmm_join_now_form_payment_fields_section',
		__( 'Membership Form Payment Fields', 'cdashmm' ),
		'cdashmm_join_now_form_payment_fields_section_callback',
		'cdashmm_plugin_options'
    );

    add_settings_field(
		'orgname',
		__( 'Organization Name', 'cdashmm' ),
		'cdashmm_orgname_render',
		'cdashmm_plugin_options',
		'cdashmm_main_section',
		array(
			__( 'This will appear on your invoice and on the PayPal payment page.', 'cdashmm') ,
		)
	);

	add_settings_field(
		'phone_number_placeholder',
		__( 'Placeholder for Phone Number', 'cdashmm' ),
		'cdashmm_phone_placeholder_render',
		'cdashmm_plugin_options',
		'cdashmm_main_section',
		array(
			__( 'You can add a placeholder for the phone number that will show on the membership form.', 'cdashmm') ,
		)
    );

    add_settings_field(
		'lapse_membership',
		__( 'Automatically Lapse Membership?', 'cdashmm' ),
		'cdashmm_lapse_membership_render',
		'cdashmm_plugin_options',
		'cdashmm_main_section',
		array(
			__( 'If this box is checked, businesses\' membership status will be marked as lapsed if they have an overdue membership invoice.', 'cdashmm' )
		)
	);

	add_settings_field(
		'hide_lapsed_members',
		__( 'Hide lapsed members?', 'cdashmm' ),
		'cdashmm_hide_lapsed_members_render',
		'cdashmm_plugin_options',
		'cdashmm_main_section',
		array(
			__( 'If this box is checked, businesses whose membership is not current will not be shown anywhere on the site.', 'cdashmm' )
		)
	);

    add_settings_field(
        'free_memb_level_redirect_url',
        __( 'Redirect Page for the Free Members', 'cdashmm' ),
        'cdashmm_free_memb_level_redirect_url_page_render',
        'cdashmm_plugin_options',
        'cdashmm_main_section',
        array(
            __( 'Select the page to redirect the free members.', 'cdashmm' )
        )
    );

    add_settings_field(
        'no_free_member_in_renewal_form',
        __( 'Do not display the free membership level in the renewal form.', 'cdashmm' ),
        'cdashmm_no_free_member_in_renewal_form_render',
        'cdashmm_plugin_options',
        'cdashmm_main_section',
        array(
            __( 'Check this box if you want to remove the free membership level from the renewal form.', 'cdashmm' )
        )
    );

	add_settings_field(
		'paypal_email',
		__( 'PayPal Email Address', 'cdashmm' ),
		'cdashmm_paypal_email_render',
		'cdashmm_plugin_options',
		'cdashmm_paypal_section',
		array(
			__( 'Email address associated with your PayPal account.  Payments will be sent to this email address.  If you leave this blank, users will not have an option to pay by PayPal.', 'cdashmm')
		)
	);

  add_settings_field(
    'paypal_return_url',
    __( 'PayPal Return URL', 'cdashmm' ),
    'cdashmm_paypal_return_url_render',
    'cdashmm_plugin_options',
    'cdashmm_paypal_section',
    array(
      __( 'The url that you want your users to come back to after they have made a payment with PayPal.  If you leave this blank, the url will be set to the page where your Join Now form is placed.', 'cdashmm')
    )
  );

	
    
	add_settings_field(
		'receipt_subject',
		__( 'Receipt Email Subject', 'cdashmm' ),
		'cdashmm_receipt_subject_render',
		'cdashmm_plugin_options',
		'cdashmm_emails_section',
		array(
			__( 'Subject of email to be sent when an invoice is paid.', 'cdashmm') ,
		)
	);

	add_settings_field(
		'receipt_from_name',
		__( 'Receipt Email From Name', 'cdashmm' ),
		'cdashmm_receipt_from_name_render',
		'cdashmm_plugin_options',
		'cdashmm_emails_section',
		array(
			__( 'Name to appear in the "From" field on the receipt email and at the top of your invoice.', 'cdashmm' )
		)
	);

	add_settings_field(
		'receipt_from_email',
		__( 'Receipt Reply-To Email', 'cdashmm' ),
		'cdashmm_receipt_from_email_render',
		'cdashmm_plugin_options',
		'cdashmm_emails_section',
		array(
			__( 'Name to appear in the "From" field on the receipt email.', 'cdashmm' )
		)
	);

	add_settings_field(
		'receipt_message',
		__( 'Receipt Email Message', 'cdashmm' ),
		'cdashmm_receipt_message_render',
		'cdashmm_plugin_options',
		'cdashmm_emails_section',
		array(
			__( 'Body of email to be sent when an invoice is paid.  This message will be followed by the transaction details.', 'cdashmm' )
		)
	);

	add_settings_field(
		'check_message',
		__( 'Pay By Check Email Message', 'cdashmm' ),
		'cdashmm_check_message_render',
		'cdashmm_plugin_options',
		'cdashmm_emails_section',
		array(
			__( 'Body of email to be sent when a member agrees to pay by check.  This message will be followed by a link to the invoice.', 'cdashmm' )
		)
	);

	add_settings_field(
		'admin_email',
		__( 'Admin Notification Email', 'cdashmm' ),
		'cdashmm_admin_email_render',
		'cdashmm_plugin_options',
		'cdashmm_emails_section',
		array(
			__( 'Email address that will receive notification of membership payment.', 'cdashmm' )
		)
	);

	add_settings_field(
		'invoice_from',
		__( 'Invoice Contact Information', 'cdashmm' ),
		'cdashmm_invoice_from_render',
		'cdashmm_plugin_options',
		'cdashmm_invoice_section',
		array(
			__( 'Enter your organization\'s name and contact information as you would like to to appear at the top of your invoices.', 'cdashmm' )
		)
	);

	add_settings_field(
		'invoice_footer',
		__( 'Invoice Footer', 'cdashmm' ),
		'cdashmm_invoice_footer_render',
		'cdashmm_plugin_options',
		'cdashmm_invoice_section',
		array(
			__( 'Payment information, or anything else you want to appear at the bottom of your invoice.', 'cdashmm' )
		)
	);

	add_settings_field(
		'no_donation',
		__( 'Remove Donation Field', 'cdashmm' ),
		'cdashmm_no_donation_render',
		'cdashmm_plugin_options',
		'cdashmm_join_now_form_payment_fields_section',
		array(
			 __( 'By default, the membership form includes an optional donation field.  Check this box if you do not want the donation field.', 'cdashmm' )
		)
	);

	add_settings_field(
		'suggested_donation',
		__( 'Suggested Donation Amount', 'cdashmm' ),
		'cdashmm_suggested_donation_render',
		'cdashmm_plugin_options',
		'cdashmm_join_now_form_payment_fields_section',
		array(
			 __( 'If you want the suggested donation field to be pre-filled with a donation amount, enter that amount here (number only, no currency symbols).', 'cdashmm' )
		)
	);

	add_settings_field(
		'donation_explanation',
		__( 'Suggested Donation Text', 'cdashmm' ),
		'cdashmm_donation_explanation_render',
		'cdashmm_plugin_options',
		'cdashmm_join_now_form_payment_fields_section',
		array(
			__( 'This text will appear on the membership form next to the suggested donation field.  It can include information about how your donation will be used, such as "Your donation supports local schools."', 'cdashmm' )
		)
	);

    add_settings_field(
        'limit_processing_fee',
        'Enable Processing fee for new members only',
        'cdashmm_limit_processing_fee_render',
        'cdashmm_plugin_options',
        'cdashmm_join_now_form_payment_fields_section',
        array(__('This option will allow you to set the processing fee only for new members. You can set up a one time fee when members join.', 'cdashmm')
        )
    );

	add_settings_field(
		'use_processing_fee',
		__( 'Processing Fee', 'cdashmm' ),
		'cdashmm_use_processing_fee_render',
		'cdashmm_plugin_options',
		'cdashmm_join_now_form_payment_fields_section',
		array(
			__( 'Check this box if you want to add a processing fee to membership transactions. Processing fee will appear on both new & renewing memberships.', 'cdashmm' )
		)
	);

	add_settings_field(
		'processing_fee_amount',
		__( 'Processing Fee Amount', 'cdashmm' ),
		'cdashmm_processing_fee_amount_render',
		'cdashmm_plugin_options',
		'cdashmm_join_now_form_payment_fields_section',
		array(
			__( 'Enter the amount of the processing fee (number only, no currency symbols).', 'cdashmm' )
		)
    );
    
    add_settings_field(
        'disable_process_fee_for_free_memb',
        __( 'Disable Processing Fee for free members', 'cdashmm' ),
        'cdashmm_disable_process_fee_for_free_memb_render',
        'cdashmm_plugin_options',
        'cdashmm_join_now_form_payment_fields_section',
        array(
            __( 'By default, processing fee is charged for all membership levels if it is set above. Check this box if you do not want to charge processing fee for free members.', 'cdashmm' )
        )
    );

	add_settings_field(
		'charge_tax',
		__( 'Tax', 'cdashmm' ),
		'cdashmm_charge_tax_render',
		'cdashmm_plugin_options',
		'cdashmm_join_now_form_payment_fields_section',
		array(
			__( 'Check this box if you want to charge tax on membership.', 'cdashmm' )
		)
    );

	add_settings_field(
		'tax_rate',
		__( 'Tax Rate', 'cdashmm' ),
		'cdashmm_tax_rate_render',
		'cdashmm_plugin_options',
		'cdashmm_join_now_form_payment_fields_section',
		array(
			__( 'Enter the tax rate (number only, no currency symbols or percentage signs).', 'cdashmm' )
		)
	);

    add_settings_field(
		'charge_tax_only_on_membership',
		__( 'Charge Tax only on the Membership Level Price', 'cdashmm' ),
		'cdashmm_charge_tax_only_on_membership_render',
		'cdashmm_plugin_options',
		'cdashmm_join_now_form_payment_fields_section',
		array(
			__( 'Check this box if you want to charge tax only on membership level.', 'cdashmm' )
		)
    );

	add_settings_field(
		'disable_check_payments',
		__( 'Disable Check Payments', 'cdashmm' ),
		'cdashmm_disable_check_pay_render',
		'cdashmm_plugin_options',
		'cdashmm_join_now_form_payment_fields_section',
		array(
			__( 'Check this box if you want to disable check payments.', 'cdashmm' )
		)
	);




	add_settings_field(
		'submit_button_label',
		__( 'Submit Button Label', 'cdashmm' ),
		'cdashmm_submit_button_label_render',
		'cdashmm_plugin_options',
		'cdashmm_join_now_form_fields_section',
		array(
			__( 'This text will appear on the submit button on the membership form.', 'cdashmm' )
		)
	);

    add_settings_field(
      'terms_statement',
      __( 'Terms and Conditions Statement', 'cdashmm' ),
      'cdashmm_terms_statement_render',
      'cdashmm_plugin_options',
      'cdashmm_join_now_form_fields_section',
      array(
        __( 'Enter the message you would like to show in the terms and conditions box.', 'cdashmm' )
      )
    );

  	add_settings_field(
  		'show_consent_box',
  		__( 'Show Consent Box', 'cdashmm' ),
  		'cdashmm_show_consent_box_render',
  		'cdashmm_plugin_options',
  		'cdashmm_join_now_form_fields_section',
  		array(
  			__( 'Check this box if you want to show a consent box with the Join Now form.', 'cdashmm' )
  		)
  	);

    add_settings_field(
      'consent_box_text',
      __( 'Consent Box Text', 'cdashmm' ),
      'cdashmm_consent_box_text_render',
      'cdashmm_plugin_options',
      'cdashmm_join_now_form_fields_section',
      array(
        __( 'Enter the message you would like to show next to the consent box.', 'cdashmm' )
      )
    );

    add_settings_field(
  		'show_referral_dropdown',
  		__( 'Show Referral Dropdown', 'cdashmm' ),
  		'cdashmm_show_referral_dropdown_render',
  		'cdashmm_plugin_options',
  		'cdashmm_join_now_form_fields_section',
  		array(
  			__( 'Check this box if you want to add a referral dropdown to the Join Now form.', 'cdashmm' )
  		)
  	);

    add_settings_field(
  		'referral_dropdown_list',
  		__( 'Referral Dropdown List', 'cdashmm' ),
  		'cdashmm_referral_dropdown_list_render',
  		'cdashmm_plugin_options',
  		'cdashmm_join_now_form_fields_section',
  		array(
  			__( 'Enter the choices you would like to add to the referral dropdown. Please enter each choice in a single line.', 'cdashmm' )
  		)
  	);

    add_settings_field(
  		'enable_user_registration',
  		__( 'Enable Members Only', 'cdashmm' ),
  		'cdashmm_enable_user_registration_render',
  		'cdashmm_plugin_options',
  		'cdashmm_members_only_section',
  		array(
  			__( 'Check this box if you want to enable user registration and restrict posts and pages based on Membership Level', 'cdashmm' )
  		)
  	);

    add_settings_field(
      'cdashmm_rc_info_page',
      __('Information Page', 'cdashmm'),
      'cdashmm_rc_info_page_render',
  		'cdashmm_plugin_options',
  		'cdashmm_members_only_section',
  		array(
  			__( 'Select the page with the information about access to member only pages and account creation.', 'cdashmm' )
  		)
    );

	add_settings_field(
  		'cdashmm_member_login_form',
  		__( 'Member Account Page', 'cdashmm' ),
  		'cdashmm_enable_member_login_page_render',
  		'cdashmm_plugin_options',
  		'cdashmm_members_only_section',
  		array(
  			__( 'Select the member login/account page.', 'cdashmm' )
  		)
  	);

    add_settings_field(
  		'cdashmm_login_logout_link',
  		__( 'Add login/logout link', 'cdashmm' ),
  		'cdashmm_login_logout_link_render',
  		'cdashmm_plugin_options',
  		'cdashmm_members_only_section',
  		array(
  			__( 'Check this box if you want to add a login/logout link to your menu. Make sure you have a login page selected above.', 'cdashmm' )
  		)
  	);

    add_settings_field(
  		'cdashmm_selected_menu',
  		__( 'Select the menu(s)', 'cdashmm' ),
  		'cdashmm_selected_menu_render',
  		'cdashmm_plugin_options',
  		'cdashmm_members_only_section',
  		array(
  			__( 'Select the menu(s) you want the login/logout link added to. If no menu is selected, the link gets added to all the available menus. ', 'cdashmm' )
  		)
    );
}

function cdashmm_paypal_email_render( $args ) {
	$options = get_option( 'cdashmm_options' );
	?>
	<input id="paypal_email" type='email' name='cdashmm_options[paypal_email]' value='<?php echo $options['paypal_email']; ?>'>
	<br /><span class="description"><?php echo $args[0]; ?></span>
	<?php
}

function cdashmm_paypal_return_url_render( $args ) {
	$options = get_option( 'cdashmm_options' );
	?>
	<input id="paypal_return_url" type='url' name='cdashmm_options[paypal_return_url]' value='<?php echo $options['paypal_return_url']; ?>'>
	<br /><span class="description"><?php echo $args[0]; ?></span>
	<?php
}

function cdashmm_orgname_render( $args ) {
	$options = get_option( 'cdashmm_options' );
	?>
	<input type='text' name='cdashmm_options[orgname]' value='<?php echo $options['orgname']; ?>'>
	<br /><span class="description"><?php echo $args[0]; ?></span>
	<?php
}

function cdashmm_phone_placeholder_render($args){
	$options = get_option( 'cdashmm_options' );
	if(isset($options['phone_number_placeholder'])){
		$options['phone_number_placeholder'] = $options['phone_number_placeholder'];
	}else{
		$options['phone_number_placeholder'] = '111-111-1111';
	}
	?>
	<input type='text' name='cdashmm_options[phone_number_placeholder]' value='<?php echo $options['phone_number_placeholder']; ?>'>
	<br /><span class="description"><?php echo $args[0]; ?></span>
	<?php
}

function cdashmm_receipt_subject_render( $args ) {
	$options = get_option( 'cdashmm_options' );
	?>
	<input type='text' name='cdashmm_options[receipt_subject]' value='<?php echo $options['receipt_subject']; ?>'>
	<br /><span class="description"><?php echo $args[0]; ?></span>
	<?php
}

function cdashmm_receipt_from_name_render( $args ) {
	$options = get_option( 'cdashmm_options' );
	?>
	<input type='text' name='cdashmm_options[receipt_from_name]' value='<?php echo $options['receipt_from_name']; ?>'>
	<br /><span class="description"><?php echo $args[0]; ?></span>
	<?php
}
function cdashmm_receipt_from_email_render( $args ) {
	$options = get_option( 'cdashmm_options' );
	?>
	<input type='text' name='cdashmm_options[receipt_from_email]' value='<?php echo $options['receipt_from_email']; ?>'>
	<br /><span class="description"><?php echo $args[0]; ?>
	<?php
}

function cdashmm_receipt_message_render( $args ) {
	$options = get_option( 'cdashmm_options' );
	?>
 	<span class="description"><?php echo $args[0]; ?></span><br />
	<?php
		$args = array("wpautop" => true, "media_buttons" => true, "textarea_name" => "cdashmm_options[receipt_message]", "textarea_rows" => "5");
		wp_editor( $options['receipt_message'], "receipt", $args );
	?>
	<?php
}

function cdashmm_check_message_render( $args ) {
	$options = get_option( 'cdashmm_options' );
	?>
 	<span class="description"><?php echo $args[0]; ?></span><br />
	<?php
		$args = array("wpautop" => true, "media_buttons" => true, "textarea_name" => "cdashmm_options[check_message]", "textarea_rows" => "5");
		wp_editor( $options['check_message'], "check", $args );
	?>
	<?php
}

function cdashmm_admin_email_render( $args ) {
	$options = get_option( 'cdashmm_options' );
	?>
	<input type='text' name='cdashmm_options[admin_email]' value='<?php echo $options['admin_email']; ?>'>
	<br/ ><span class="description"><?php echo $args[0]; ?></span>
	<?php
}

function cdashmm_invoice_from_render( $args ) {
	$options = get_option( 'cdashmm_options' );
	?>
 	<span class="description"><?php echo $args[0]; ?></span><br />
		<?php
			$args = array("textarea_name" => "cdashmm_options[invoice_from]", "textarea_rows" => "5", "media_buttons" => true, "wpautop" => true);
			wp_editor( $options['invoice_from'], "from", $args );
		?>
	<?php
}

function cdashmm_invoice_footer_render( $args ) {
	$options = get_option( 'cdashmm_options' );
	?>
 	<span class="description"><?php echo $args[0]; ?></span><br />
	<?php
		$args = array("textarea_name" => "cdashmm_options[invoice_footer]", "textarea_rows" => "5", "media_buttons" => true, "wpautop" => true);

		wp_editor( $options['invoice_footer'], "footer", $args );

	?>

	<?php
	}

	function cdashmm_no_donation_render( $args ) {
    $options = get_option( 'cdashmm_options' );
    if(!isset($options['no_donation'])){
      $options['no_donation'] = 0;
    }
	?>
	<input id="cdashmm_no_donation" type='checkbox' name='cdashmm_options[no_donation]' <?php checked( $options['no_donation'], 1 ); ?> value='1'>
	<span class="description"><?php echo $args[0]; ?></span>
	<?php

}

function cdashmm_suggested_donation_render( $args ) {

	$options = get_option( 'cdashmm_options' );
	?>
	<input id="cdashmm_suggested_donation" type='number' name='cdashmm_options[suggested_donation]' value='<?php echo $options['suggested_donation']; ?>'>
	<br /><span class="description"><?php echo $args[0]; ?></span>
	<?php

}

function cdashmm_donation_explanation_render( $args ) {

	$options = get_option( 'cdashmm_options' );
	?>
	<textarea id="cdashmm_donation_explanation" cols='50' rows='3' name='cdashmm_options[donation_explanation]'><?php echo $options['donation_explanation']; ?></textarea>
 	<br /><span class="description"><?php echo $args[0]; ?></span>
	<?php

	}

  function cdashmm_limit_processing_fee_render($args){
    $options = get_option('cdashmm_options');

    if(!cdash_payment_options_active()){
			$disabled = "disabled";
			$description = __("This is a pro feature. Install and activate the Chamber Dashboard Payment Options plugin to use this feature. You can purchase the plugin <a href='https://chamberdashboard.com/downloads/payment-options/' target='_blank'>here</a>.", "cdashmm");
		}else{
			$disabled = "";
			$description = __("Set one time processing fee.", "cdashmm");
		}

    if(isset($options['limit_processing_fee'])){
      $limit_processing_fee = $options['limit_processing_fee'];
    }
    else{
      $limit_processing_fee = 0;
    }
    ?>
      <input id="cdashmm_limit_processing_fee" type='checkbox' name='cdashmm_options[limit_processing_fee]' <?php checked( $limit_processing_fee, 1 ); ?> value='1' <?php echo $disabled; ?>>
      <span class="description"><?php echo $description; ?></span>
    <?php
  }

function cdashmm_use_processing_fee_render( $args ) {

	$options = get_option( 'cdashmm_options' );
  if(!isset($options['use_processing_fee'])){
    $options['use_processing_fee'] = 0;
  }
	?>
	<input id="cdashmm_use_processing_fee" type='checkbox' name='cdashmm_options[use_processing_fee]' <?php checked( $options['use_processing_fee'], 1 ); ?> value='1'>
	<span class="description"><?php echo $args[0]; ?></span>
	<?php
}

function cdashmm_processing_fee_amount_render( $args ) {

	$options = get_option( 'cdashmm_options' );
	?>
	<input id="cdashmm_processing_fee_amount" type='number' name='cdashmm_options[processing_fee_amount]' value='<?php echo $options['processing_fee_amount']; ?>'>
	<br /><span class="description"><?php echo $args[0]; ?></span>
	<?php

}

function cdashmm_charge_tax_render( $args ) {

    $options = get_option( 'cdashmm_options' );
    if(!isset($options['charge_tax'])){
        $options['charge_tax'] = '0';
    }
	?>
	<input id="cdashmm_charge_tax" type='checkbox' name='cdashmm_options[charge_tax]' <?php checked( $options['charge_tax'], 1 ); ?> value='1'>
	<span class="description"><?php echo $args[0]; ?></span>
	<?php
}

function cdashmm_charge_tax_only_on_membership_render($args){
    $options = get_option( 'cdashmm_options' );
    if(!isset($options['charge_tax_only_on_membership'])){
        $options['charge_tax_only_on_membership'] = '0';
    }
	?>
	<input id="cdashmm_charge_tax_only_on_membership" type='checkbox' name='cdashmm_options[charge_tax_only_on_membership]' <?php checked( $options['charge_tax_only_on_membership'], 1 ); ?> value='1'>
	<span class="description"><?php echo $args[0]; ?></span>
	<?php
}

function cdashmm_tax_rate_render( $args ) {

	$options = get_option( 'cdashmm_options' );
	?>
	<input id="cdashmm_tax_rate" type='number' id="options_tax_rate" step='.01' name='cdashmm_options[tax_rate]' value='<?php echo $options['tax_rate']; ?>'>
	<br /><span class="description"><?php echo $args[0]; ?></span>
	<?php

}

function cdashmm_disable_check_pay_render( $args ) {
	$options = get_option( 'cdashmm_options' );
	$check_payments = '';
	if(!isset($options['disable_check_payments'])){
		$options['disable_check_payments'] = '0';
	}
	if(!isset($options['paypal_email']) || $options['paypal_email'] == ''){
		?>
		<input id="cdashmm_disable_check_pay" type='checkbox' name='cdashmm_options[disable_check_payments]' <?php checked( $options['disable_check_payments'], 1 ); ?> value='1' disabled="disabled">
		<span class="description"><?php echo __("Please enable PayPal by adding your PayPal email. You will then be able to disable the check payments.", "cdashmm") ?></span>
		<?php
	}else{
		?>
		<input id="cdashmm_disable_check_pay" type='checkbox' name='cdashmm_options[disable_check_payments]' <?php checked( $options['disable_check_payments'], 1 ); ?> value='1'>
		<span class="description"><?php echo $args[0]; ?></span>
		<?php
	}
	?>
	
	<?php

}

function cdashmm_lapse_membership_render( $args ) {
	$options = get_option( 'cdashmm_options' );
    if(!isset($options['lapse_membership']) ){
		$options['lapse_membership'] = 0;
	}
	?>
	<input type='checkbox' name='cdashmm_options[lapse_membership]' <?php checked( $options['lapse_membership'], 1 ); ?> value='1'>
	<span class="description"><?php echo $args[0]; ?></span>
	<?php
}

function cdashmm_hide_lapsed_members_render( $args ) {

	$options = get_option( 'cdashmm_options' );
    if(!isset($options['hide_lapsed_members']) ){
		$options['hide_lapsed_members'] = 0;
	}
	?>
	<input type='checkbox' name='cdashmm_options[hide_lapsed_members]' <?php checked( $options['hide_lapsed_members'], 1 ); ?> value='1'>
	<span class="description"><?php echo $args[0]; ?></span>
	<?php
}

function cdashmm_no_free_member_in_renewal_form_render( $args ) {

	$options = get_option( 'cdashmm_options' );
    if(!isset($options['no_free_member_in_renewal_form']) ){
		$options['no_free_member_in_renewal_form'] = 0;
	}
	?>
	<input type='checkbox' name='cdashmm_options[no_free_member_in_renewal_form]' <?php checked( $options['no_free_member_in_renewal_form'], 1 ); ?> value='1'>
	<span class="description"><?php echo $args[0]; ?></span>
	<?php
}

function cdashmm_submit_button_label_render( $args ) {
	$options = get_option( 'cdashmm_options' );
	?>
	<input type='text' name='cdashmm_options[submit_button_label]' value='<?php echo $options['submit_button_label']; ?>'>
	<br/ ><span class="description"><?php echo $args[0]; ?></span>
	<?php
}

function cdashmm_terms_statement_render( $args ) {
	$options = get_option( 'cdashmm_options' );
  ?>
  <span class="description"><?php echo $args[0]; ?></span><br />
  <?php
  $args = array("textarea_name" => "cdashmm_options[terms_statement]", "textarea_rows" => "5", "media_buttons" => true, "wpautop" => true);

  wp_editor( $options['terms_statement'], "terms_statement", $args );
	?>
	<?php
}

function cdashmm_show_consent_box_render( $args ) {
	$options = get_option( 'cdashmm_options' );
	?>
	<input type='checkbox' class="cdashmm_show_consent_box" name='cdashmm_options[show_consent_box]' <?php if(isset($options['show_consent_box'])) { checked( $options['show_consent_box'], 1 ); } ?> value='1'>
	<span class="description"><?php echo $args[0]; ?></span>
	<?php
}

function cdashmm_consent_box_text_render( $args ) {
	$options = get_option( 'cdashmm_options' );
  ?>
  <span class="description"><?php echo $args[0]; ?></span><br />
  <?php
  $args = array("textarea_name" => "cdashmm_options[consent_box_text]", "textarea_rows" => "5", "media_buttons" => true,  "wpautop" => true);

  wp_editor( $options['consent_box_text'], "consent_box_text", $args );
	?>
	<?php
}

function cdashmm_show_referral_dropdown_render( $args ) {
	$options = get_option( 'cdashmm_options' );
  if(!isset($options['show_referral_dropdown'])){
    $options['show_referral_dropdown'] = '0';
  }
	?>
	<input type='checkbox' class="cdashmm_show_referral_dropdown" name='cdashmm_options[show_referral_dropdown]' <?php checked( $options['show_referral_dropdown'], 1 ); ?> value='1'>
	<span class="description"><?php echo $args[0]; ?></span>
	<?php
}

function cdashmm_referral_dropdown_list_render( $args ) {
	$options = get_option( 'cdashmm_options' );
  if(!isset($options['referral_dropdown_list'])){
    $options['referral_dropdown_list'] = '';
  }
	?>
  <span class="description"><?php echo $args[0]; ?></span>
  <textarea cols='50' rows='10' name='cdashmm_options[referral_dropdown_list]' class='cdashmm_referral_dropdown_list' ><?php echo $options['referral_dropdown_list']; ?></textarea>
	<?php
}

function cdashmm_enable_user_registration_render( $args ) {
	$options = get_option( 'cdashmm_options' );
  if(!isset($options['enable_user_registration'])){
    $enable_user_registration = "0";
  }
  $url = admin_url();
  $link = "plugin-install.php?tab=plugin-information&amp;parent_plugin_id=170&amp;plugin=chamber-dashboard-crm&amp;TB_iframe=true&amp;width=772&amp;height=700";

  $plugins = cdash_get_active_plugin_list();
  if( !cdash_check_crm_active() ) {
    $disabled = "disabled";
    $description = __("Please install and activate the Chamber Dashboard CRM plugin to use this feature. You can download the plugin <a href='$url.$link' class=' thickbox fs-overlay'>here</a>.", "cdashmm");
  }else{
    $disabled = "";
    $description = __("Check this box if you want to enable user registration and restrict posts and pages based on Membership Level", "cdashmm");
  }
  ?>
	<input id="enable_user_registration" type='checkbox' class="cdashmm_enable_rc" name='cdashmm_options[enable_user_registration]' <?php if(isset($options['enable_user_registration'])) { checked( $options['enable_user_registration'], 1 ); } ?> value='1' <?php echo $disabled; ?>>
	<span class="description"><?php echo $description; ?></span>
	<?php
}
function cdashmm_enable_member_login_page_render( $args ) {
	$options = get_option( 'cdashmm_options' );
    if(cdash_check_cdashmu_active()){
        $mu_options = get_option('cdashmu_options');
    }
	?>
  <select name="cdashmm_options[cdashmm_member_login_form]" id="member_login_page_url">
    <option value=""><?php echo esc_attr( __( 'Select page' ) ); ?></option>
    <?php
        $selected_page = $options['cdashmm_member_login_form'];
        $pages = get_pages();
        foreach ( $pages as $page ) {
            $option = '<option value="' . $page->ID . '" ';
            $option .= ( $page->ID == $selected_page ) ? 'selected="selected"' : '';
            $option .= '>';
            $option .= $page->post_title;
            $option .= '</option>';
            echo $option;
        }
    ?>
</select>
	<br/ ><span class="description"><?php echo $args[0]; ?></span>
	<?php
}

function cdashmm_rc_info_page_render( $args ) {
	$options = get_option( 'cdashmm_options' );
  if(!isset($options['cdashmm_rc_info_page'])){
    $options['cdashmm_rc_info_page'] = '';
  }
  $plugins = cdash_get_active_plugin_list();
  if( !cdash_check_crm_active() ) {
    $disabled = "disabled";
  }else{
    $disabled = "";
  }
	?>
  <select name="cdashmm_options[cdashmm_rc_info_page]" id="member_rc_info_page_url" <?php echo $disabled; ?>>
    <option value=""><?php echo esc_attr( __( 'Select page' ) ); ?></option>
    <?php
        $selected_page = $options['cdashmm_rc_info_page'];
        $pages = get_pages();
        foreach ( $pages as $page ) {
            $option = '<option value="' . $page->ID . '" ';
            $option .= ( $page->ID == $selected_page ) ? 'selected="selected"' : '';
            $option .= '>';
            $option .= $page->post_title;
            $option .= '</option>';
            echo $option;
        }
    ?>
</select><br />
 	<span class="description"><?php echo $args[0]; ?></span><br />

	<?php
}

function cdashmm_login_logout_link_render($args){
  $options = get_option( 'cdashmm_options' );
  if(!isset($options['cdashmm_login_logout_link'])){
    $options['cdashmm_login_logout_link'] = '0';
  }
	?>
  <input id="cdashmm_login_logout_link" type='checkbox' class="cdashmm_login_logout_link" name="cdashmm_options[cdashmm_login_logout_link]" <?php checked( $options['cdashmm_login_logout_link'], 1 ); ?> value='1'>
	<span class="description"><?php echo $args[0]; ?></span>
  <?php
}

function cdashmm_selected_menu_render($args){
    $options = get_option( 'cdashmm_options' );

    if(isset($options['cdashmm_selected_menu'])){
        $cdashmm_selected_menu = $options['cdashmm_selected_menu'];
    }else{
        $cdashmm_selected_menu = array();
    }
    $nav_menus = get_registered_nav_menus();
    if(!empty($nav_menus) && $nav_menus != ''){
        foreach($nav_menus as $menu_name => $menu){
        ?>
        <input id="cdashmm_selected_menu" type="checkbox" class="cdashmm_selected_menu" name="cdashmm_options[cdashmm_selected_menu][]" value=<?php echo $menu_name; ?> <?php checked( in_array( $menu_name, $cdashmm_selected_menu ), 1 ); ?> /><label name="cdashmm_selected_menu_label"><?php echo $menu; ?></label><br />
        <?php
        }
        ?>
        <br />
        <span class="description"><?php echo $args[0]; ?></span>
        <?php
    }
}

function cdashmm_free_memb_level_redirect_url_page_render($args){
    $options = get_option( 'cdashmm_options' );
    if(!isset($options['free_memb_level_redirect_url']) ){
        $options['free_memb_level_redirect_url'] = '';
      }
	?>
  <select name="cdashmm_options[free_memb_level_redirect_url]" id="member_login_page_url">
    <option value=""><?php echo esc_attr( __( 'Select page' ) ); ?></option>
    <?php
        $selected_page = $options['free_memb_level_redirect_url'];
        $pages = get_pages();
        foreach ( $pages as $page ) {
            $option = '<option value="' . $page->ID . '" ';
            $option .= ( $page->ID == $selected_page ) ? 'selected="selected"' : '';
            $option .= '>';
            $option .= $page->post_title;
            $option .= '</option>';
            echo $option;
        }
    ?>
</select>
	<br /><span class="description"><?php echo $args[0]; ?></span>
	<?php
}




function cdashmm_options_section_callback(  ) {
	echo __('<span class="desc"></span>', 'cdashmm');
}

function cdashmm_members_only_section_callback(){
    echo __('<span class="desc"></span>', 'cdashmm');
}

function cdashmm_paypal_section_callback(){
  echo __('<span class="desc"></span>', 'cdashmm');
}

function cdashmm_emails_section_callback(){
  echo __('<span class="desc"></span>', 'cdashmm');
}

function cdashmm_invoice_section_callback(){
  echo __('<span class="desc"></span>', 'cdashmm');
}

function cdashmm_join_now_form_fields_section_callback(){
  echo __('<span class="desc"></span>', 'cdashmm');
}

function cdashmm_join_now_form_payment_fields_section_callback(){
    echo __('<span class="desc"></span>', 'cdashmm');
}

function cdashmm_free_memb_section_callback(){
    echo __('<span class="desc"></span>', 'cdashmm');
}

function cdashmm_validate_options( $input ) {

	$allowed = array(
	    'a' => array(
	        'href' => array(),
	        'title' => array()
	    ),
	    'br' => array(),
	    'p' => array(),
	    'em' => array(),
	    'strong' => array(),
	    'li' => array(),
	    'ul' => array(),
	    'ol' => array(),
	    'h1' => array(),
	    'h2' => array(),
	    'h3' => array(),
	    'h4' => array(),
	);

    if( isset( $input['paypal_email'] ) ) {
    	$input['paypal_email'] = strip_tags( stripslashes( $input['paypal_email'] ) );
    }

    if( isset( $input['paypal_return_url'] ) ) {
    	$input['paypal_return_url'] = strip_tags( stripslashes( $input['paypal_return_url'] ) );
    }

    if( isset( $input['orgname'] ) ) {
    	$input['orgname'] = strip_tags( stripslashes( $input['orgname'] ) );
    }

    if( isset( $input['receipt_subject'] ) ) {
    	$input['receipt_subject'] = strip_tags( stripslashes( $input['receipt_subject'] ) );
    }

    if( isset( $input['receipt_from_name'] ) ) {
    	$input['receipt_from_name'] = strip_tags( stripslashes( $input['receipt_from_name'] ) );
    }

    if( isset( $input['receipt_from_email'] ) ) {
    	$input['receipt_from_email'] = strip_tags( stripslashes( $input['receipt_from_email'] ) );
    }

    if( isset( $input['receipt_message'] ) ) {
    	//$input['receipt_message'] = wp_kses( $input['receipt_message'], $allowed );
      $input['receipt_message'] = wp_kses_post( $input['receipt_message'] );
    }

    if( isset( $input['check_message'] ) ) {
    	$input['check_message'] = wp_kses_post( $input['check_message'], $allowed );
    }

    if( isset( $input['admin_email'] ) ) {
    	$input['admin_email'] = strip_tags( stripslashes( $input['admin_email'] ) );
    }

    if( isset( $input['invoice_from'] ) ) {
    	$input['invoice_from'] = wp_kses_post( $input['invoice_from'], $allowed );
    }

    if( isset( $input['invoice_footer'] ) ) {
    	$input['invoice_footer'] = wp_kses_post( $input['invoice_footer'], $allowed );
    }

    if( isset( $input['suggested_donation'] ) ) {
    	$input['suggested_donation'] = strip_tags( stripslashes( $input['suggested_donation'] ) );
    }

    if( isset( $input['donation_explanation'] ) ) {
    	$input['donation_explanation'] = strip_tags( stripslashes( $input['donation_explanation'] ) );
    }
    if( isset( $input['processing_fee_amount'] ) ) {
    	$input['processing_fee_amount'] = strip_tags( stripslashes( $input['processing_fee_amount'] ) );
    }

    if( isset( $input['invoice_cc'] ) ) {
    	$input['invoice_cc'] = strip_tags( stripslashes( $input['invoice_cc'] ) );
    }

    if( isset( $input['invoice_subject'] ) ) {
    	$input['invoice_subject'] = strip_tags( stripslashes( $input['invoice_subject'] ) );
    }

    if( isset( $input['invoice_message'] ) ) {
    	$input['invoice_message'] = wp_kses( $input['invoice_message'], $allowed );
    }

    if( isset( $input['reminder_frequency'] ) ) {
    	$input['reminder_frequency'] = strip_tags( stripslashes( $input['reminder_frequency'] ) );
    }

		if( isset( $input['reminder_subject'] ) ) {
    	$input['reminder_subject'] = strip_tags( stripslashes( $input['reminder_subject'] ) );
    }

		if( isset( $input['reminder_message'] ) ) {
    	$input['reminder_message'] = wp_kses( $input['reminder_message'], $allowed );
    }

    if(isset($input['terms_statement'])){
      $input['terms_statement'] = wp_kses_post($input['terms_statement'], $allowed );
    }

    if(isset($input['consent_box_text'])){
      $input['consent_box_text'] = wp_kses_post($input['consent_box_text'], $allowed );
    }

		if( isset( $input['submit_button_label'] ) ) {
    	$input['submit_button_label'] = strip_tags( stripslashes( $input['submit_button_label'] ) );
    }

    if( isset( $input['recurring_payments_license'] ) ) {
		$options = get_option( 'cdashmm_options' );
	    $old = $options['recurring_payments_license'];
		if( $old && $old != $input['recurring_payments_license'] ) {
			delete_option( 'recurring_payments_license_status' ); // new license has been entered, so must reactivate
		}
		$input['recurring_payments_license'] = strip_tags( stripslashes( $input['recurring_payments_license'] ) );
	}

    return $input;
}

function cdashrp_show_rp_settings(){
  settings_fields( 'cdashrp_plugin_options' );
	do_settings_sections( 'cdashrp_plugin_options' );
	submit_button();
}


function cdashmmpro_settings(){
  settings_fields( 'cdashmmpro_plugin_options' );
	do_settings_sections( 'cdashmmpro_plugin_options' );
	submit_button();
}

function cdashmm_addon_settings(){
  settings_fields('cdash_addons_settings_page');
  do_settings_sections('cdash_addons_settings_page');
  submit_button();
}

add_action( 'cdash_settings_tab', 'cdash_mm_tab', 20 );
function cdash_mm_tab(){
	global $cdash_active_tab; ?>
    <a class="nav-tab <?php echo $cdash_active_tab == 'payments' ? 'nav-tab-active' : ''; ?>" href="<?php echo admin_url( 'admin.php?page=cd-settings&tab=payments' ); ?>"><?php _e( 'Payments', 'cdash' ); ?> </a>
	<?php
}

add_action( 'cdash_settings_content', 'cdash_mm_settings' );
function cdash_mm_settings(){
    global $cdash_active_tab;

	switch($cdash_active_tab){
		case 'payments':
		cdashmm_options_page();
		break;
	}
}

function cdashmm_options_page(){
$active_plugins = cdash_get_active_plugin_list();
?>
<div class="wrap">
    <?php
    $page = sanitize_text_field($_GET['page']);
    if(isset($_GET['tab'])){
        $tab = sanitize_text_field($_GET['tab']);
    }
    if(isset($_GET['section'])){
        $section = sanitize_text_field($_GET['section']);
    }else{
        $section = "cdashmm";
    }
    ?>
    <div class="icon32" id="icon-options-general"><br></div>
    <h1><?php _e( 'Chamber Dashboard Member Manager', 'cdashmm' ); ?></h1>
    <div id="main" class="cd_settings_tab_group" style="width: 100%; float: left;">
        <div class="cdash section_group">
            <ul>
                <li class="<?php echo $section == 'cdashmm' ? 'section_active' : ''; ?>">
                    <a href="?page=cd-settings&tab=payments&section=cdashmm" class="<?php echo $section == 'cdashmm' ? 'section_active' : ''; ?>"><?php esc_html_e( 'Member Manager Settings', 'cdashmm' ); ?></a><span>|</span>
                </li>
                <?php
                if(function_exists('cdashmm_check_mmpro_active') && (cdashmm_check_mmpro_active()) ){
                  ?>
                  <li class="<?php echo $section == 'cdash_mmpro' ? 'section_active' : ''; ?>">
                      <a href="?page=cd-settings&tab=payments&section=cdash_mmpro" class="<?php echo $section == 'cdash_mmpro' ? 'section_active' : ''; ?>"><?php esc_html_e( 'Member Manager Pro Options', 'cdashmm' ); ?></a><span>|</span>
                  </li>
                  <?php
                }
                if(cdashmm_addons_active() ){
                  ?>
                  <li class="<?php echo $section == 'cdash_mm_addons' ? 'section_active' : ''; ?>">
                      <a href="?page=cd-settings&tab=payments&section=cdash_mm_addons" class="<?php echo $section == 'cdash_mm_addons' ? 'section_active' : ''; ?>"><?php esc_html_e( 'Additional Member Manager Options', 'cdashmm' ); ?></a><span>|</span>
                  </li>
                <?php
                }
                 ?>
                 <li class="<?php echo $section == 'cdashrp' ? 'section_active' : ''; ?>">
                     <a href="?page=cd-settings&tab=payments&section=cdashrp" class="<?php echo $section == 'cdashrp' ? 'section_active' : ''; ?>"><?php esc_html_e( 'Recurring Payment Options', 'cdashmm' ); ?></a><span>|</span>
                 </li>
                 <li class="<?php echo $section == 'cdash_payments_docs' ? 'section_active' : ''; ?>">
                     <a href="?page=cd-settings&tab=payments&section=cdash_payments_docs" class="<?php echo $section == 'cdash_payments_docs' ? 'section_active' : ''; ?>"><?php esc_html_e( 'Shortcodes', 'cdashmm' ); ?></a>
                 </li>
            </ul>
        </div><!--end of section_group-->
        <div class="cdash_section_content">
            <?php
            if( $section == 'cdashmm' )
            {
                cdashmm_settings();

            }else if($section == 'cdashrp'){
                cdash_rp_settings();
            }else if($section == 'cdash_mm_addons'){
                cdash_mm_addons_settings();
            }else if($section == 'cdash_mmpro'){
                cdash_mmpro_settings();
            }else if($section == 'cdash_payments_docs'){
                cdashmm_quick_access();
            }
          ?>
        </div><!--end of section_content-->
    </div>
</div>
<?php
}

function cdashmm_settings(){
    $file = plugin_dir_path( __FILE__ );
    ?>
    <div id="member_manager_settings" class="cdash_plugin_settings">
        <form action='options.php' method='post'>
    <?php
        settings_fields( 'cdashmm_plugin_options' );
        ?>
        <div class="settings_sections">
        <?php
        do_settings_sections( 'cdashmm_plugin_options' );
        ?>
        </div>
        <?php
        submit_button();
    ?>
</form>
</div>
<?php
}

function cdash_rp_settings(){
    $active_plugins = cdash_get_active_plugin_list();
    ?>
    <div id="recurring_payments_settings" class="cdash_plugin_settings">
        <form action='options.php' method='post' class="cdash_settings">
        <?php
        if( in_array( 'cdash-recurring-payments.php', $active_plugins )){
            settings_fields( 'cdashrp_plugin_options' );
            ?>
            <div class="settings_sections">
            <?php
            do_settings_sections( 'cdashrp_plugin_options' );
            ?>
            </div>
            <?php
          	submit_button();
        }else{
            echo '<h2>' . __('The Recurring Payments addon lets you send invoices & renewal reminders automatically!', 'cdashmm') . '</h2>
            <h3>' . __('Recurring Payments is not currently activated.', 'cdashmm') . '</h3>
            <p>' . __('If you have purchased the Recurring Payments plugin, invoice reminders are not currently being sent.', 'cdashmm') . '</p>

            <p>' . __('To start automatically sending renewal reminders to your members please activate your Recurring Payments plugin or purchase one ', 'cdashmm'). '<a href="https://chamberdashboard.com/downloads/recurring-payments/">' . __('here', 'cdashmm') . '</a>.</p>';
        }
        ?>
        </form>
    </div>
    <?php
}

function cdash_mm_addons_settings(){
    echo '<h2>' . __('Member Manager Addons Options', 'cdashmm') . '</h2>';
      if(cdash_check_wc_payments_active()){
        $cdash_wc_connection = cdashwc_get_wc_connection();
        if(  $cdash_wc_connection == 1){
            cdashwc_insert_memb_level_as_wc_products();
            //echo "Member Manager is now connected to WooCommerce.";
          }
      }
    ?>
    <form action='options.php' method='post'>
    <?php
        cdashmm_addon_settings();
    ?>
    </form>
<?php
}

function cdash_mmpro_settings(){
    $options = get_option('cdashmmpro_options');
    if(isset($options['mmpro_wc_connection'])){
      $mmpro_wc_connection = $options['mmpro_wc_connection'];
    }
    else{
      $mmpro_wc_connection = 0;
    }
    //echo "MM Pro WooCommerce Connection:" .   $mmpro_wc_connection . "<br /><br />";
    if(  $mmpro_wc_connection == 1){
      cdashmm_insert_memb_level_as_wc_products();
      //echo "Member Manager is now connected to WooCommerce.";
    }else{
      //echo "Member Manager is not connected to WooCommerce.  Paypal is currently the only payment gateway available.";
    }
    ?>
                <form action='options.php' method='post'>
                <?php
        cdashmmpro_settings();
                ?>
                </form>
                <?php
}

function cdashmm_options_page_old( ) {
$active_plugins = cdash_get_active_plugin_list();
?>

<div class="wrap">
	<!-- Display Plugin Icon, Header, and Description -->
	<div class="icon32" id="icon-options-general"><br></div>

	<h1><?php _e( 'Chamber Dashboard Member Manager', 'cdashmm' ); ?></h1>
	<?php settings_errors(); ?>
</div><!--end of wrap-->
<?php
   $active_tab = isset( $_GET[ 'tab' ] ) ? sanitize_text_field($_GET[ 'tab' ]) : 'cdashmm_plugin_options';
?>
<h2 class="nav-tab-wrapper">
	<a href="?page=cdashmm&tab=cdashmm_plugin_options" class="nav-tab <?php echo $active_tab == 'cdashmm_plugin_options' ? 'nav-tab-active' : ''; ?>"><?php _e( 'Member Manager Options', 'cdashmm' ); ?></a>
  <?php
  if(function_exists('cdashmm_check_mmpro_active') && (cdashmm_check_mmpro_active()) ){
    ?>
    <a href="?page=cdashmm&tab=cdashmmpro_plugin_options" class="nav-tab <?php echo $active_tab == 'cdashmmpro_plugin_options' ? 'nav-tab-active' : ''; ?>"><?php _e( 'Member Manager Pro Options', 'cdashmm' ); ?></a>
    <?php
  }
  if(cdashmm_addons_active() ){
    ?>
    <a href="?page=cdashmm&tab=cdashmm_addon_options" class="nav-tab <?php echo $active_tab == 'cdashmm_addon_options' ? 'nav-tab-active' : ''; ?>"><?php _e( 'Additional Member Manager Options', 'cdashmm' ); ?></a>
  <?php
  }
   ?>
	<a href="?page=cdashmm&tab=cdashrp_plugin_options" class="nav-tab <?php echo $active_tab == 'cdashrp_plugin_options' ? 'nav-tab-active' : ''; ?>"><?php _e( 'Recurring Payment Options', 'cdashmm' ); ?></a>
	</h2>
	<div id="main" style="width: 90%; min-width: 350px; float: left;">
		<div class="cdash_column_left">
			<?php
						if( $active_tab == 'cdashmm_plugin_options' ) {
              $file = plugin_dir_path( __FILE__ );
              //echo $file;
			?>
				<form action='options.php' method='post'>
			<?php
				settings_fields( 'cdashmm_plugin_options' );
				do_settings_sections( 'cdashmm_plugin_options' );
				submit_button();
			?>
		</form>

			<?php
			}elseif( $active_tab == 'cdashrp_plugin_options' ) {
        ?>
					<form action='options.php' method='post' class="cdash_settings">
					<?php
					if( in_array( 'cdash-recurring-payments.php', $active_plugins )){
						cdashrp_show_rp_settings();
					}else{
            echo '<h2>' . __('The Recurring Payments addon lets you send invoices & renewal reminders automatically!', 'cdashmm') . '</h2>
            <h3>' . __('Recurring Payments is not currently activated.', 'cdashmm') . '</h3>
            <p>' . __('If you have purchased the Recurring Payments plugin, invoice reminders are not currently being sent.', 'cdashmm') . '</p>

            <p>' . __('To start automatically sending renewal reminders to your members please activate your Recurring Payments plugin or purchase one', 'cdashmm'). '<a href="https://chamberdashboard.com/downloads/recurring-payments/">' . __('here', 'cdashmm') . '</a>.</p>';
					}
					?>
					</form>
					<?php
			}elseif( $active_tab == 'cdashmmpro_plugin_options' ) {
        $options = get_option('cdashmmpro_options');
        if(isset($options['mmpro_wc_connection'])){
          $mmpro_wc_connection = $options['mmpro_wc_connection'];
        }
        else{
          $mmpro_wc_connection = 0;
        }
        //echo "MM Pro WooCommerce Connection:" .   $mmpro_wc_connection . "<br /><br />";
        if(  $mmpro_wc_connection == 1){
          cdashmm_insert_memb_level_as_wc_products();
          //echo "Member Manager is now connected to WooCommerce.";
        }else{
          //echo "Member Manager is not connected to WooCommerce.  Paypal is currently the only payment gateway available.";
        }
        ?>
					<form action='options.php' method='post'>
					<?php
            cdashmmpro_settings();
					?>
					</form>
					<?php
			}elseif($active_tab == 'cdashmm_addon_options'){
        echo '<h2>' . __('Member Manager Addons Options', 'cdashmm') . '</h2>';
          if(cdash_check_wc_payments_active()){
            $cdash_wc_connection = cdashwc_get_wc_connection();
          }
        ?>
					<form action='options.php' method='post'>
					<?php
            cdashmm_addon_settings();
					?>
					</form>
					<?php
      }
			?>
		</div><!--end of cdash_column_left-->

		<div class="cdash_sidebar">
			<?php
        if(!cdashmm_check_mmpro_active() ){
            if( $active_tab == 'cdashmm_plugin_options' ) {
              cdashmm_quick_access();
              cdashmm_feature_updates();
              //cdashmm_news();
    				}/*else if($active_tab == 'cdashmmpro_plugin_options'){
              cdashmmpro_updates();
            }*/
        }else{
          if( $active_tab == 'cdashmm_plugin_options' ) {
            cdashmm_quick_access();
            cdashmmpro_updates();
          }else if($active_tab == 'cdashmmpro_plugin_options'){
            cdashmmpro_updates();
          }
        }
        if( $active_tab == 'cdashrp_plugin_options' ) {
          if( in_array( 'cdash-recurring-payments.php', $active_plugins )){
            cdashrp_feature_updates();
          }
        }
			?>
		</div><!-- end of cdash_sidebar-->

		</div><!-- main-->
		<?php
	}
    function cdashmm_quick_access(){
        $active_plugins = cdash_get_active_plugin_list();
      ?>
      <div id="sidebar">
      	<div class="cdash_top_blocks">
      		<div class="cdash_block">
                <h3><?php echo __('Display Membership Form', 'cdashmm'); ?></h3>
    			<p><span class="bold">[membership_form]</span> - <?php echo __('Displays the Join Now form on your page', 'cdashmm'); ?><br />
    			</p>
    			<p><a target="_blank" href="https://chamberdashboard.com/docs/plugin-features/online-payments/new-member-form/"><?php echo __('Membership Form Docs', 'cdash');?></a></p>
            </div>
            <div class="cdash_block">
                <h3><?php echo __('Display Renewal Form', 'cdashmm'); ?></h3>
    			<p><span class="bold">[membership_form action="renewal]</span> - <?php echo __('Displays the Renewal form on your page.', 'cdashmm'); ?><br />
    			</p>
    			<p><a target="_blank" href="https://chamberdashboard.com/docs/plugin-features/online-payments/membership-renewal/"><?php echo __('Renewal Form Docs', 'cdash');?></a></p>
            </div>
            <div class="cdash_block">
                <h3><?php echo __('Membership Levels', 'cdashmm'); ?></h3>
    			<p><span class="bold">[membership_levels]</span> - <?php echo __('Displays the membership level details.', 'cdashmm'); ?><br />
    			</p>
    			<p><a target="_blank" href="https://chamberdashboard.com/docs/plugin-features/online-payments/new-member-form/#step-1-%E2%80%93-create-display-membership-levels"><?php echo __('Membership Level Docs', 'cdash');?></a></p>
            </div>
            <div class="cdash_block">
                <h3><?php echo __('Membership Login Form', 'cdashmm'); ?></h3>
    			<p><span class="bold">[cdashmm_member_login_form]</span> - <?php echo __('Displays the member login form and member account details after logging in.', 'cdashmm'); ?><br />
    			</p>
    			<p><a target="_blank" href="https://chamberdashboard.com/docs/plugin-features/members-only-content/"><?php echo __('Member Only Content Docs', 'cdash');?></a></p>
            </div>
            <?php
            if( in_array( 'cdash-wc-payments.php', $active_plugins )){
                ?>
                <div class="cdash_block">
                    <h3><?php echo __('Payment Gateways', 'cdashmm'); ?></h3>
        			<p><?php echo __('Connect to additional payment gateways with the Member Manager Pro plugin.', 'cdashmm'); ?><br />
        			</p>
        			<p><a target="_blank" href="https://chamberdashboard.com/docs/plugin-features/payment-gateways/"><?php echo __('Additional Payment Gateways Docs', 'cdash');?></a></p>
                </div>
                <?php
            }
            ?>
            <?php
            if( in_array( 'cd-mailchimp-addon.php', $active_plugins )){
                ?>
                <div class="cdash_block">
                    <h3><?php echo __('Mailchimp', 'cdashmm'); ?></h3>
        			<p><?php echo __(' The Mailchimp addon makes it easier than ever for your members to get connected to your newsletter.', 'cdashmm'); ?><br />
        			</p>
        			<p><a target="_blank" href="https://chamberdashboard.com/docs/plugin-features/mailchimp/"><?php echo __('Mailchimp Addon Docs', 'cdash');?></a></p>
                </div>
                <?php
            }
            ?>
        </div>
    </div>
    <?php
    }

function cdashmm_news(){
	//$cdashmm_news = '';
	//echo $cdashmm_news;
  //require_once( plugin_dir_path( __FILE__ ) . 'includes/aside.php' );
}

function cdashmm_feature_updates(){
  $cdashmm_feature_updates = '';
  $cdashmm_feature_updates .=
  '<h2>' . __('Recent updates in Member Manager', 'cdashmm') . '</h2>
  <p>' . __('You can enable Members only content and restrict the pages or posts based on membership level', 'cdashmm') . '</p>
  <p>' . __('Setup a member account page to display member information', 'cdashmm') . '</p>
     <h4><a href="https://chamberdashboard.com/docs/plugin-features/online-payments/" target="_blank">' . __('Member Manager documentation', 'cdashmm') . '</a>
  </h4>';
  echo $cdashmm_feature_updates;
}

function cdashmmpro_updates(){
  $cdashmm_pro_updates = '';
  $cdashmm_pro_updates .= '
    <h2>Member Manager Pro version ' . CDASHMM_PRO_VERSION . '</h2>';

    $cdashmm_pro_updates .= '<p>You can connect to WooCommerce and use multiple payment gateways provided by WooCommerce.</p>
    Full documentation for the pro version can be found here: <a href="https://chamberdashboard.com/document_category/member-manger-pro-docs/" target="_blank">Member Manager Pro Documentation</a><br /><br />';

    $cdashmm_pro_updates .= '<p>Member Manager Documention can be found here: <a href="https://chamberdashboard.com/document/member-manager-docs/" target="_blank">Setting up Member Manager Options</a>
    </p>';
    echo $cdashmm_pro_updates;
}
?>
