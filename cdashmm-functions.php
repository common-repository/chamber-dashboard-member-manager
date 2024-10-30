<?php
  if ( ! defined('ABSPATH') ) {
    die('Please do not load this file directly.');
  }

  //Setting the transiet variable when plugin is active
  function cdashmm_set_plugin_active(){
      set_transient('cdashmm_active', 'true');
  }

  add_action('cdash_technical_details_hook', 'cdashmm_render_technical_details', 20);

  function cdashmm_render_technical_details(){
    echo "<h4>Member Manager Version: " . CDASHMM_VERSION . "</h4>";
  }

  function cdashmm_set_user_transient(){
      set_transient( 'cdashmm_user_logged_in', true );
  }
  //add_action('wp_login', 'cdashmm_set_user_transient', 99, 2);

  function cdashmm_set_user_meta_login($login) {
      //Add my user meta code
      $user = get_user_by('login',$login);
      $user_id = $user->ID;
      //$user_id = $user->ID;
      add_user_meta($user_id, 'cdashmm_user_logged_in', 'logged_in');
  }
//add_action('wp_login', 'cdashmm_set_user_meta_login', 99, 2);

  // ------------------------------------------------------------------------
  // ADDING LOST PASSWORD TO THE LOGIN FORM
  // ------------------------------------------------------------------------

  add_action( 'login_form_middle', 'cdashmm_add_lost_password_link' );
  function cdashmm_add_lost_password_link() {
  	return '<a href="/wp-login.php?action=lostpassword">Lost Password?</a>';
  }


  add_action('wp_logout','cdashmm_auto_redirect_after_logout');
  function cdashmm_auto_redirect_after_logout($user_id){
      wp_redirect( home_url() );
      exit();
  }

  function cdashmm_logout_update_meta($expiration, $user_id) {
    update_user_meta($user_id, 'cdashmm_user_logged_in', 'logged_out');
    return $expiration;
}
//add_filter('auth_cookie_expiration', 'cdashmm_logout_update_meta', 10, 2);

  function cdashmm_logout_user_meta($expiration, $user_id) {
    update_user_meta($user_id, 'cdashmm_user_logged_in', '0');
    return $expiration;
}
//add_filter('auth_cookie_expiration', 'cdashmm_logout_user_meta', 10, 2);


  //Check if user is logged in

    function cdashmm_is_user_logged_in(){
        //if(get_transient('cdashmm_user_logged_in')){
        if(is_user_logged_in()){
            return true;
        }
        return false;
    }

//Adding this to the header to avoid caching issues when user logs in and tries to access another member-only page
    function cdashmm_check_user_login(){
      if ( cdashmm_is_user_logged_in() ) {
        $logged = TRUE;
      } else {
        $logged = FALSE;
      }
  }
  add_action('wp_head', 'cdashmm_check_user_login');

//Disable the admin bar for non-admin users
  cdashmm_remove_admin_bar();

  //Creating a new user
  function cdashmm_user_registration($first_name, $last_name, $username, $password, $email, $business_id, $show_registration_message, $role, $content){
  	$userdata = array(
  	'first_name'    =>   $first_name,
  	'last_name'     =>   $last_name,
  	'user_login'    =>   $username,
  	'user_pass'     =>   $password,
  	'user_email'    =>   $email,
  	'role'          =>   $role
  	);
  	$user = wp_insert_user( $userdata );
    $name = $first_name . ' ' . $last_name;

    //If CRM is active, create person record and connect user to person
    $plugins = cdash_get_active_plugin_list();
    if( cdash_check_crm_active() ) {
      $person_details = array(
  			'post_type' => 'person',
  		  'post_title' => $name,
  	    'post_content' => $content,
  	    'post_status' => 'pending'
  		);

  		$person = wp_insert_post( $person_details );
      p2p_type('businesses_to_people')->connect($business_id, $person, array('date' => current_time('mysql')));
      cdashmm_connect_user_to_people($user, $person);
    }
		return $user;
  }

  //Check if user is connected to a business
  function cdashmm_check_bus_connected_to_user($business_id, $user_id){
  	if(!$business_id){
  			$person_id = cdashmm_get_person_id_from_user_id($user_id, true);
  			$business_id = cdashmm_get_business_id_from_person_id($person_id, true);
  			if($business_id){
  					//echo __("Your connection to the business has not been approved yet. Please contact your Chamber of Commerce.", "cdashmm");
  					return true;
  			}else{
  					//echo __("You are not connected to a business. Please contact your Chamber of Commerce.", "cdashmm");
  					return false;
  			}
  			//return $message;
  	}
  	return true;
  	//exit();
  }


  function cdashmm_connect_user_to_people($user, $people) {
      p2p_type('people_to_user')->connect($people, $user, array('date' => current_time('mysql')));
  }

  // ------------------------------------------------------------------------
  // Connect Users to People
  // https://github.com/scribu/wp-posts-to-posts/blob/master/posts-to-posts.php
  // ------------------------------------------------------------------------

  if( defined( 'CDASH_PATH' ) ) {
      // Create the connection between users and people
      function cdashmm_user_and_people() {
          p2p_register_connection_type( array(
              'name' => 'people_to_user',
              'from' => 'person',
              'to' => 'user',
              'reciprocal' => true,
              'admin_column' => 'any',
              'admin_box' => array(
  			      'context' => 'side'
  			  	),
  	        'title' => array(
  			    'from' => __( 'Connected Users', 'cdcrm' ),
  			    'to' => __( 'Connected People', 'cdcrm' )
  			  	)
          ) );
      }
      add_action( 'p2p_init', 'cdashmm_user_and_people' );
  }

  //get businesses connected to the person and get the first business id
  function cdashmm_get_person_id_from_business_id($business_id, $include_pending) {
      if($business_id == null){
          return null;
      }
      // Find connected people
      $connection_params = array(
  	  'connected_type' => 'businesses_to_people',
  	  'connected_items' => $business_id,
  	  'nopaging' => true
  	);

      if($include_pending){
          $connection_params['connected_query'] = array('post_status' => 'any');
      }
      $connected = new WP_Query( $connection_params);


      // Get the person ID
      if ( $connected->have_posts() ) :

      while ( $connected->have_posts() ) : $connected->the_post();
          //get the business connected to the person
          $person_id = get_the_ID();
          break;
      endwhile;

      // Prevent weirdness
      wp_reset_postdata();
      else:
          $person_id = null;
      endif;
      return $person_id;
  }

  //get user connected to the person
  function cdashmm_get_user_id_from_people_id($person_id, $include_pending){
      if($person_id == null){
          return null;
      }

      //Find connected user
      $users = get_users( array(
        'connected_type' => 'people_to_user',
        'connected_items' => $person_id
      ) );
	if(!$users){
        $user_id = '';
      }else{
      //Get the person ID
      foreach ( $users as $user ) {
      	$user_id = $user->ID;
        break;
      }
	}
      return $user_id;
  }

/**
 * Check if WooCommerce is active
 **/
function cdashmm_check_woocommerce_active(){
	if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
    	// Put your plugin code here
		return true;
	}else{
		return false;
	}
}

//Member Manager Pro
//Check if MM Pro is active, WooCommerce is active and MM is connected to WC
function cdashmm_check_mm_connected_to_wc(){
  if(cdashmm_check_mmpro_active()){
    $options = get_option('cdashmmpro_options');
    if(isset($options['mmpro_wc_connection'])){
      $mmpro_wc_connection = $options['mmpro_wc_connection'];
    }
    else{
      $mmpro_wc_connection = 0;
    }
    $options = get_option('cdashmmpro_options');
    if( (cdashmm_check_mmpro_active() && cdashmm_check_woocommerce_active() && $mmpro_wc_connection == 1  ) ){
      return true;
    }else{
      return false;
    }
  }
  return false;
}

//If Member Manager Pro is active and the payment option is set to Monthly, we need to take the monthly cost value
function cdashmm_get_membership_level_price($levelid){
  //Check if Member Manager Pro is active
  if(cdashmm_monthly_options_enabled()){ //If payment option is set to monthly
    $cost = get_tax_meta( $levelid, 'monthly_cost' );
    //If monthly cost is not set, we are trying to get the yearly cost and divide by 12 to get the monthly cost
    if(empty($cost)){
      $cost = get_tax_meta( $levelid, 'cost' );
      if(!empty($cost)){
        $cost = $cost/12;
        $cost = round($cost, 2);
      }
    }
  }else{
    $cost = get_tax_meta( $levelid, 'cost' );
  }
  return $cost;
}

//Get membership level of the business
function cdashmm_get_bus_memb_level($business_id){
	$level = 'membership_level';
	$terms = wp_get_post_terms( $business_id, $level );
	if ( $terms && !is_wp_error( $terms ) ) {
		foreach ( $terms as $term ) {
			$memb_level = $term->name;
		}
	}else{
		$memb_level = "";
	}
	return $memb_level;
}

//Update Membership Renewal Date based on Monthly or Yearly Payments
function cdashmm_set_mrd(){
  if(cdashmm_monthly_options_enabled()){
    $renewal_date = date('Y-m-d', strtotime('+30 days'));
  }else{
    $renewal_date = date('Y-m-d', strtotime('+1 year'));
  }
  return $renewal_date;
}

function cdashmm_price_term(){
  if(cdashmm_monthly_options_enabled()){
    $price_term = 'mo';
  }else{
    $price_term = 'yr';
  }
  return $price_term;
}

//Addon Plugins

//Check if WC Payments is active, WooCommerce is active and MM is connected to WC
function cdashmm_check_mm_connected_to_wc_payments(){
  $options = get_option('cdash_addon_options');
  if(isset($options['cdash_wc_connection'])){
    $cdash_wc_connection = $options['cdash_wc_connection'];
  }
  else{
    $cdash_wc_connection = 0;
  }
  $options = get_option('cdash_addon_options');
  if( (cdash_check_wc_payments_active() && cdashwc_check_woocommerce_active() && $cdash_wc_connection == 1  ) ){
    return true;
  }else{
    return false;
  }
}

//Get plugin options for MM Pro or Payment Options addon
function cdashmm_get_payment_options(){
  $options = '';
  if(cdashmm_check_mmpro_active()){
    $options = get_option('cdashmmpro_options');
  }else if(cdash_payment_options_active()){
    $options = get_option('cdash_addon_options');
  }
  return $options;
}

//Check if monthly options are enabled
function cdashmm_monthly_options_enabled(){
  $options = cdashmm_get_payment_options();
  if(isset($options['mmpro_payment_option']) && $options['mmpro_payment_option'] == 0){
    return true;
  }else if(isset($options['monthly_payment_option']) && $options['monthly_payment_option'] == 0 ){
    return true;
  }else{
    return false;
  }
}

//Get all the membership levels
function cdashmm_get_membership_levels(){
  $terms = get_terms( array(
    'taxonomy' => 'membership_level',
    'hide_empty' => false,
    ));
  return $terms;
}

//Register addon settings page
function cdash_addons_init(){
  register_setting( 'cdash_addons_settings_page', 'cdash_addon_options', 'cdash_addons_validate_settings');
}

//Check if any Member Manager addons are active
function cdashmm_addons_active(){
  if( (cdashmm_check_mailchimp_addon()) || (cdash_check_wc_payments_active()) || (cdash_payment_options_active()) ){
    return true;
  }else{
    return false;
  }
}

function cdashmm_check_member_manager(){
	if(function_exists('cdashmm_requires_wordpress_version')){
		return true;
	}else if(function_exists('cdashmm_pro_require_business_directory')){
		return true;
	}else{
		return false;
	}
}


function cdash_check_cdashmu_active(){
	if(function_exists('cdash_requires_wordpress_version')){
		$plugins = cdash_get_active_plugin_list();
		if ((in_array('cdash-member-updater.php', $plugins))) {
			return true;
		}else{
			return false;
		}
	}
}

function cdash_check_crm_active(){
  if(function_exists('cdash_requires_wordpress_version')){
		$plugins = cdash_get_active_plugin_list();
		if ((in_array('cdash-crm.php', $plugins))) {
			return true;
		}else{
			return false;
		}
	}
}

function cdashmm_check_mailchimp_addon(){
  if(function_exists('cdash_requires_wordpress_version')){
		$plugins = cdash_get_active_plugin_list();
		if ((in_array('cd-mailchimp-addon.php', $plugins))) {
			return true;
		}else{
			return false;
		}
	}
}

//Check if WC Payments is active
function cdash_check_wc_payments_active(){
  if(function_exists('cdash_requires_wordpress_version')){
		$plugins = cdash_get_active_plugin_list();
		if ((in_array('cdash-wc-payments.php', $plugins))) {
			return true;
		}else{
			return false;
		}
	}
}

//Check if Member Manager Pro is active
function cdashmm_check_mmpro_active(){
  if ( in_array( 'chamber-dashboard-member-manager-pro/cdash-member-manager-pro.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
    	// Put your plugin code here
		return true;
	}else{
		return false;
	}
}

//Check if Payment Options is active
function cdash_payment_options_active(){
  if(function_exists('cdash_requires_wordpress_version')){
		$plugins = cdash_get_active_plugin_list();
		if ((in_array('cdash-payment-options.php', $plugins))) {
			return true;
		}else{
			return false;
		}
	}
}

function cdashmm_get_current_user_id(){
    if ( cdashmm_is_user_logged_in() ){
       $user = wp_get_current_user();
       $user_id = $user->ID;
       return $user_id;
   }
   return null;
}

//get people connected to the user, get the first person from the connection
//if there is no such person, logout
function cdashmm_get_person_id_from_user_id($user_id, $include_pending){
    if($user_id == null){
        return null;
    }

    //Find connected people

    $connection_params = array(
        'connected_type' => 'people_to_user',
        'connected_items' => $user_id,
        'nopaging' => true
    );

    if($include_pending){
        $connection_params['post_status'] = 'any';
    }

    $connected = new WP_Query($connection_params);

    //Get the person ID
    if($connected->have_posts()):

    while($connected->have_posts() ): $connected->the_post();
        //get the person connected to the user
        $person_id = get_the_ID();
        break;
    endwhile;

    //Prevent wierdness
    wp_reset_postdata();

    else:
    $person_id = null;
    endif;

    return $person_id;
}

//get businesses connected to the person and get the first business id
function cdashmm_get_business_id_from_person_id($person_id, $include_pending) {
    if($person_id == null){
        return null;
    }
    // Find connected businesses

    $connection_params = array(
	  'connected_type' => 'businesses_to_people',
	  'connected_items' => $person_id,
	  'nopaging' => true
	);

    if($include_pending){
        $connection_params['connected_query'] = array('post_status' => 'any');
    }
    $connected = new WP_Query( $connection_params);


    // Get the business ID
    if ( $connected->have_posts() ) :

    while ( $connected->have_posts() ) : $connected->the_post();
        //get the business connected to the person
        $business_id = get_the_ID();
        break;
    endwhile;

    // Prevent weirdness
    wp_reset_postdata();

    else:
        $business_id = null;
    endif;

    return $business_id;
}

function cdashmm_get_business_memb_level($business_id){
  $terms = get_the_terms( $business_id, 'membership_level' );
  return $terms;
}

function cdashmm_enable_rc(){
  $options = get_option('cdashmm_options');
  if(isset($options['enable_user_registration']) && ($options['enable_user_registration'] == 1)){
    return true;
  }else{
    return false;
  }
}

function cdashmm_show_user_registration(){
  $show_user_registration_fields = false;

  if(cdash_check_cdashmu_active() || cdashmm_check_mm_connected_to_wc() || cdashmm_check_mm_connected_to_wc_payments() ){
    $show_user_registration_fields = true;
  }

  if(cdash_check_crm_active() && cdashmm_enable_rc()){
    $show_user_registration_fields = true;
  }

  return $show_user_registration_fields;
}

function cdashmm_redirect($atts, $content=null){
  $options = get_option('cdashmm_options');
  $login_page_id = $options['cdashmm_member_login_form'];
  $login_page_slug = get_post_field( 'post_name', $login_page_id );
  if(is_page($login_page_slug)){
    if(isset($_GET['redirect']) && $_GET['redirect'] !=''){
      $return = '<p>' . $content . '</p>';
      return $return;
    }
  }
}
add_shortcode('cd_redirect', 'cdashmm_redirect');

function cdashmm_get_login_page_url(){
    //TODO: Use the helper function to the login page url. Test!!
    //return cdashmm_get_option_page_url('cdashmm_member_login_form');

    $options = get_option('cdashmm_options');
    if(isset($options['cdashmm_member_login_form']) && $options['cdashmm_member_login_form'] !='' ){
        $login_page_id = $options['cdashmm_member_login_form'];
        $login_page_slug = get_post_field( 'post_name', $login_page_id );
        $site_url = home_url() . '/' . $login_page_slug;
    }else{
        $site_url = '';
    }
    return $site_url;
}

function cdashmm_get_option_page_url($option_name){
    $page_id = cdashmm_get_option_value($option_name);
    if($page_id){
        $page_slug = get_post_field( 'post_name', $page_id );
        $site_url = home_url() . '/' . $page_slug;
    }else{
      $site_url = '';
    }
    return $site_url;
  }

function cdashmm_enqueue_frontend_scripts(){
    // Enqueue stylesheet
	wp_enqueue_style( 'cdashmm-frontend-css', plugin_dir_url(__FILE__) . 'css/cdashmm_frontend.css', '' );

    wp_register_style('jquery-ui-css', plugins_url() . '/chamber-dashboard-business-directory/css/jquery_ui_base_theme.css');
    wp_enqueue_style( 'jquery-ui-css' );


    wp_enqueue_script( 'cdashmm-frontend-js', plugin_dir_url(__FILE__) . 'js/cdashmm_frontend.js', array( 'jquery', 'jquery-ui-core', 'jquery-ui-tabs' ) );
}
add_action( 'wp_enqueue_scripts', 'cdashmm_enqueue_frontend_scripts' );

function cdashmm_payment_buttons(){
    if(!isset($member_info)){
        $member_info = '';
    }
    /*$member_info .= "<p><a href='#' class='show_payment_button button'>" . __("Show Payment Information", "cdashmm") . "</a></p>
    <p><a href='#' class='hide_payment_button button'>" . __("Hide Payment Information", "cdashmm") . "</a></p>";*/
    $member_info .="<p><button class='show_payment_button button'>". __("Show Payment Information", "cdashmm") . "</button></p>";
    $member_info .="<p><button class='hide_payment_button button'>". __("Hide Payment Information", "cdashmm") . "</button></p>";
    return $member_info;
}


function cdashmm_get_invoice_information($business_id){
    if(!isset($member_info)){
        $member_info = '';
    }
    $cdash_bus_options = get_option('cdash_directory_options');
    if(isset($cdash_bus_options['currency_symbol']) && $cdash_bus_options['currency_symbol'] != '' ){
        $currency_symbol = $cdash_bus_options['currency_symbol'];
    }
    // Find connected people
    $connection_params = array(
    'connected_type' => 'invoices_to_businesses',
    'connected_items' => $business_id,
    //'connected_items' => get_queried_object(),
    'nopaging' => true,
    'suppress_filters' => false
    );

    $connected = new WP_Query( $connection_params);

    // Get the person ID
    if ( $connected->have_posts() ) :
        $member_info .= cdashmm_payment_buttons();
        $member_info .= "<div id='show_payment_info'>";

        $member_info .= "<table><tr><th>Invoice Amount</th><th>Date Paid</th><th>Payment Method</th><th>Actions</th></tr>";
        while ( $connected->have_posts() ) : $connected->the_post();
        global $invoice_metabox;
        $invoicemeta = $invoice_metabox->the_meta();
        //get the invoice connected to the business
        $invoice['id'] = get_the_ID();
        if(isset($invoicemeta['paiddate']) && $invoicemeta['paiddate'] != ''){
            $pay_date = date("M jS, Y", strtotime($invoicemeta['paiddate']));
        }else{
            $pay_date = __('Not Paid', 'cdashmm');
        }
        if( isset( $invoicemeta['amount'] ) && $invoicemeta['amount'] !='' ) {
            $amount = $currency_symbol. $invoicemeta['amount'];
        }else{
            $amount = '';
        }
        if( isset( $invoicemeta['paymethod'] ) && $invoicemeta['paymethod'] !='' ) {
            $pay_method = $invoicemeta['paymethod'];
        }else{
            $pay_method = '';
        }
        //$member_info .= "Invoice ID: ". $invoice['id'] . "<br />";
        $member_info .= "<tr>";
        $member_info .= "<td>". $amount . "</td>";
        $member_info .= "<td>" . $pay_date . "</td>";
        $member_info .= "<td>". $pay_method . "</td>";

        $invoice_url = get_the_permalink($invoice['id']);
        $member_info .= "<td><a target='_blank' href='" . $invoice_url . "'>View</a></td>";
        $member_info .= "</tr>";
    endwhile;
    $member_info .= "</table>";
    $member_info .= "</div>";

    // Prevent weirdness
    wp_reset_postdata();
    else:
        $invoice = null;
    endif;
    //return $member_info;
    return $member_info;
}

function cdashmm_get_wc_order_information($user_id){
  global $woocommerce;
  if(!isset($member_info)){
      $member_info = '';
  }
  //$customer_user_id = get_current_user_id(); // current user ID here for example
  $customer_user_id = $user_id;

  // Getting current customer orders
  if(cdashmm_check_mm_connected_to_wc_payments()){
    $customer_orders = wc_get_orders( array(
      'meta_key' => '_customer_user',
      'meta_value' => $customer_user_id,
      //'post_status' => $order_statuses,
      'numberposts' => -1
  ) );
  }

  if($customer_orders){
      $member_info .= cdashmm_payment_buttons();
      $member_info .= "<div id='show_payment_info'>";
      $wc_my_account_shortcode = do_shortcode('[woocommerce_my_account]');
      $member_info .= $wc_my_account_shortcode;
      $member_info .= "</div>";
  }
  return $member_info;
}


//Remove admin bar on the front end for logged in users
function cdashmm_remove_admin_bar() {
	if(!function_exists('wp_get_current_user')) {
    	include(ABSPATH . "wp-includes/pluggable.php");
	}
	if ( ! current_user_can( 'manage_options' ) ) {
    	add_filter('show_admin_bar', '__return_false');
	}
}

//Creating the custom hook for displaying Member Account Page
function cdashmm_member_account_hook(){
  do_action('cdashmm_member_account_hook');
}

function cdashmm_get_page_items() {

  $args = array (
    'post_status' => 'publish'
  );
 
  $items = array();
 
  if ( $pages = get_pages( $args ) ) {
    foreach ( $pages as $page ) {
      $items[] = array(
        'id' => $page->ID,
        'title' => $page->post_title,
        'slug' => $page->post_name,
        //'author' => get_the_author_meta( 'display_name', $page->post_author ),
        //'content' => apply_filters( 'the_content', $page->post_content ),
        //'teaser' => $page->post_excerpt
      );
    }
  }
  return $items;
}

function cdashm_register_api_endpoints() {
  register_rest_route( 'cdashmm/v2', '/pages', array(
    'methods' => 'GET',
    'callback' => 'cdashmm_get_page_items',
    'permission_callback' => '__return_true',
  ) );
}
 
add_action( 'rest_api_init', 'cdashm_register_api_endpoints' );

?>
