<?php
function cdashmm_display_member_info(){
    $member_info = '';
    cdashmm_enqueue_frontend_scripts();
    global $wp;
    $user_id = cdashmm_get_current_user_id();
    $options = get_option('cdashmm_options');

    if(!$user_id){
        //return;
    }else{
        $person_id = cdashmm_get_person_id_from_user_id($user_id, false);
    }

    if(!$person_id){
        $business_id = null;
        //return;
    }else{
        $business_id = cdashmm_get_business_id_from_person_id($person_id, false);
    }

    if(!$business_id){
        if ( current_user_can( 'manage_options' ) ) {
            $member_info .= __("<p>You are logged in as the site administrator. To access a business listing, please login as the business editor.</p>", "cdashmm");
            //return;
        }else{
            $member_info .= __("<p>You are not connected to a business. Please contact the site admin.</p>", "cdashmm");
        }
    }else{
      $query = new WP_Query( array( 'post_type' => 'business', 'p' =>  $business_id) );

      if ( $query->have_posts() ) {
        while ( $query->have_posts() ) {
            $query->the_post();
            $bus_title = get_the_title();
            
            $member_info .= "<div class='cdashmm_member_info'>";
            $member_info = apply_filters('cdashmm_member_info_filter', $member_info, $business_id);
            $member_info .= "</div>";
        }
      }
    }
    return $member_info;
  }

//Displaying the Business Name
add_filter('cdashmm_member_info_filter', 'cdashmm_display_member_details', 100, 2);
function cdashmm_display_member_details($member_info, $business_id){
    $bus_title = get_the_title( $business_id );
    $member_info .= "<p class='cdashmm_bus_name'><span>" . __('Business Name: ', 'cdashmm'). '</span>' . $bus_title . "</p>";

    return $member_info;
}

add_filter('cdashmm_member_info_filter', 'cdashmm_display_bus_edit_link', 110, 2);
function cdashmm_display_bus_edit_link($member_info, $business_id){
    $user_id = cdashmm_get_current_user_id();
  //If MU is active, display the business edit link if the user is a business editor
    if(cdash_check_cdashmu_active()){
      if(function_exists('cdashmu_is_business_editor') && cdashmu_is_business_editor($user_id)){
        $member_info .= cdashmu_display_business_edit_link($business_id);
      }
    }
  return $member_info;
}

add_filter('cdashmm_member_info_filter', 'cdashmm_display_member_level_info', 120, 2);
function cdashmm_display_member_level_info($member_info, $business_id){
    $memb_level = cdashmm_get_bus_memb_level($business_id);
    $renewal_date = cdashmm_get_renewal_date($business_id);
    if(!isset($member_info)){
        $member_info = "";
    }
    if($memb_level != ""){
      $member_info .= "<p class='memb_level'><span>" . __('Membership Level: ', 'cdashmm') . '</span>' . $memb_level . "</p>";
      $member_info .= "<p class='cdashmm_renewal_date'><span>" . __('Renewal Date: ', 'cdashmm'). '</span>' . $renewal_date . "</p>";
    }
    return $member_info;
}

add_filter('cdashmm_member_info_filter', 'cdashmm_payment_information', 130, 2);
function cdashmm_payment_information($member_info, $business_id){
    $user_id = cdashmm_get_current_user_id();
    if(cdashmm_check_mm_connected_to_wc_payments()){
        $member_info .= cdashmm_get_wc_order_information($user_id);
    }else{
        $member_info .= cdashmm_get_invoice_information($business_id);
    }
    return $member_info;
}

//Get the membership renewal date
function cdashmm_get_renewal_date($business_id){
global $membership_metabox;
$membership_meta = $membership_metabox->the_meta($business_id);
if(isset($membership_meta['renewal_date']) && $membership_meta['renewal_date'] != ''){
    $renewal_date = $membership_meta['renewal_date'];
    $renewal_date = date("M jS, Y", strtotime($renewal_date));
}else{
    $renewal_date = __("Renewal Date is not set.", "cdashmm");
}
return $renewal_date;
}
  ?>