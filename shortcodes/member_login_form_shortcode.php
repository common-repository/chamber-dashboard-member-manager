<?php
function cdashmm_member_login_form_shortcode(){
    global $wp;
    $member_info = "";
    $user_logged_in = cdashmm_is_user_logged_in();
    if ( cdashmm_is_user_logged_in() ){
        //redirect to business update page
        $user = wp_get_current_user();
        $user_id = $user->ID;
        $member_options = get_option('cdashmm_options');
        $user = get_userdata( $user_id );
        $member_info .= cdashmm_display_member_info();
    }else{
        $current_slug = add_query_arg( array(), $wp->request );
        if(isset($_GET['redirect']) && $_GET['redirect'] !=''){
            $redirect = sanitize_text_field($_GET['redirect']);
        }else{
            $redirect = $current_slug;
        }
        /* Set up some defaults. */
        $args = array(
            'echo' => false,
            'redirect' => site_url( $redirect ),
            'form_id' => 'cdashmm_loginform',
            'label_username' => __( 'Username/Email' ),
            'label_password' => __( 'Password' ),
            'label_remember' => __( 'Remember Me' ),
            'label_log_in' => __( 'Log In' ),
            'id_username' => 'user_login',
            'id_password' => 'user_pass',
            'id_remember' => 'rememberme',
            'id_submit' => 'wp-submit',
            'remember' => true,
            'value_username' => NULL,
            'value_remember' => false );
            //wp_login_form($args);
          $member_info .= wp_login_form($args);
    }
      return $member_info;
  }
  add_shortcode( 'cdashmm_member_login_form', 'cdashmm_member_login_form_shortcode' );
?>