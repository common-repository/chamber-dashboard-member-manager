<?php
//Restrict Content Functions
/* Fire our meta box setup function on the post editor screen. */
add_action( 'load-post.php', 'cdashrc_member_level_metabox_setup' );
add_action( 'load-post-new.php', 'cdashrc_member_level_metabox_setup' );

/* Meta box setup function. */
function cdashrc_member_level_metabox_setup(){
  global $post;
  $options = get_option('cdashmm_options');
  if(isset($options['enable_user_registration']) && $options['enable_user_registration'] == 1  && cdash_check_crm_active() ){
    /* Add meta boxes on the 'add_meta_boxes' hook. */
    add_action( 'add_meta_boxes', 'register_display_member_level_metabox' );

    /* Save post meta on the 'save_post' hook. */
    add_action( 'save_post', 'cdashrc_save_member_level_meta' );
  }
}

//Showing membership levels in a metabox on all the pages and posts
function register_display_member_level_metabox() {
  $screens = ['post', 'page'];
    foreach ($screens as $screen) {
        add_meta_box(
            'cdashrc_membership_level_metabox', // Unique ID
            esc_html__( 'Restrict by Membership Level', 'cdashmm' ),
            'render_display_membership_levels',  // Content callback, must be of type callable
            $screen,                   // Post or Page or CPT
            'normal',
            'default'
        );
    }
}

function render_display_membership_levels( $post ) {
  global $post;
  wp_nonce_field( basename( __FILE__ ), 'display_memb_level_nonce' );
  echo "<p class='cdashrc_memb_level_metabox_text'>". __('Limit access to this content to selected membership levels. (If no roles are selected, everyone can view the content.)
 > Go to Businesses >> membership levels to add levels.', 'cdashmm') ."</p>";

  $membership_levels = cdashrc_display_membership_levels();
  echo $membership_levels;
}

//Get custom member level meta data
function cdash_get_custom_member_level_meta(){
  global $post;
  //check if post is object otherwise you're not in singular post
  if( !is_object($post) )
     return;
  $custom_member_level_meta = array();
  $custom_member_level_meta = maybe_unserialize(get_post_meta($post->ID, 'cdashrc_membership_level_metabox', true));
  return $custom_member_level_meta;
}

//Display membership levels
function cdashrc_display_membership_levels(){
  global $post;
  if(!isset($membership_level_list)){
    $membership_level_list ='';
  }

  $terms = get_terms( array(
    'taxonomy' => 'membership_level',
    'hide_empty' => false,
    ));

  $custom_member_level_meta = cdash_get_custom_member_level_meta();

  $checked = '';

  if($terms){
    $membership_level_list .= '<p>';

    foreach ($terms as $term){
      $memb_level_id = $term->term_taxonomy_id;
      $memb_level_slug = $term->slug;
      $memb_level_name = $term->name;
      $membership_level_list .='';

      if(is_array($custom_member_level_meta) && in_array($memb_level_id, $custom_member_level_meta)){
        $checked = 'checked="checked"';
      }else {
        $checked = null;
      }

      ?>

      <input type="checkbox" name="cdashrc_memb_levels[]" id="<?php echo $memb_level_slug; ?>"value="<?php echo $memb_level_id; ?>" <?php echo $checked; ?>>
      <label><?php echo $memb_level_name;?></label><br />

      <?php
      $membership_level_list .='';
    }
    $membership_level_list .= '</p>';
  }else{
    $membership_level_list = __('There are no membership levels.', 'cdashmm');
  }

  return $membership_level_list;
}

function cdashrc_save_member_level_meta($post){
  global $post;
  if(empty($post)){
    return;
  }else{
    $post_id = $post->ID;
    $is_autosave = wp_is_post_autosave( $post_id );
    $is_revision = wp_is_post_revision( $post_id );
    $is_valid_nonce = ( isset( $_POST[ 'display_memb_level_nonce' ] ) && wp_verify_nonce( $_POST[ 'display_memb_level_nonce' ], basename( __FILE__ ) ) ) ? 'true' : 'false';

    if ( $is_autosave || $is_revision || !$is_valid_nonce ) {
          return;
    }

    /* Get the post type object. */
    $post_type = get_post_type_object( $post->post_type );

    /* Check if the current user has permission to edit the post. */
    if ( !current_user_can( $post_type->cap->edit_post, $post_id ) )
      return $post_id;

      // If the checkbox was not empty, save it as array in post meta
      if ( ! empty( $_POST['cdashrc_memb_levels'] ) ) {
          update_post_meta( $post_id, 'cdashrc_membership_level_metabox', $_POST['cdashrc_memb_levels'] );

      // Otherwise just delete it if its blank value.
      } else {
          //delete_post_meta( $post_id, 'cdashrc_memb_levels' );
          delete_post_meta( $post_id, 'cdashrc_membership_level_metabox' );
      }
  }
}

//Check if membership level is selected on the page or post
function cdash_check_if_member_level_is_selected(){
  include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
  //if ( is_plugin_active( 'chamber-dashboard-member-manager/cdash-member-manager.php' ) ) {
    $terms = cdashmm_get_membership_levels();
  //}
  $custom_member_level_meta = cdash_get_custom_member_level_meta();
  if($terms){
    if($custom_member_level_meta){
      foreach ($terms as $term){
        $memb_level_id = $term->term_taxonomy_id;
        $memb_level_slug = $term->slug;
        $memb_level_name = $term->name;
        if ( in_array($memb_level_id, $custom_member_level_meta) ){
          return true;
        }
      }
    }
    return false;
  }
  return false;
}

//Redirect visitors based on their membership level
add_action('template_redirect', 'cdashrc_display_page');
function cdashrc_display_page(){
  //cdashrc_scripts_styles();

  if((is_front_page() && is_home()) || is_front_page() || is_home() ){
    return;
  }

  $options = get_option('cdashmm_options');
  if(!isset($options['enable_user_registration']) || $options['enable_user_registration'] == 0 ){
    return;
  }

  $person_id = '';
  //I load just before selecting and rendering the template to screen
  if(cdash_check_if_member_level_is_selected()){
    $user_id = cdashmm_get_current_user_id();
    //get the connected business and membership level
    if(!$user_id){
      $current_slug = cdashrc_get_redirect_slug();
      $redirect_url = cdashrc_member_redirect_url($current_slug);
      wp_redirect($redirect_url);
      die();
    }else{
      if(current_user_can('administrator')){
        return;
      }
      $person_id = cdashmm_get_person_id_from_user_id($user_id, false);
      if(!cdashmm_check_if_valid($person_id, 'person')){
        cdashmm_redirect_to_info_page();
      }
      $business_id = cdashmm_get_business_id_from_person_id($person_id, false);
      if(!cdashmm_check_if_valid($business_id, 'business')){
        cdashmm_redirect_to_info_page();
      }

      $membership_levels = cdashmm_get_business_memb_level($business_id);
      if(!cdashmm_check_if_valid($membership_levels, 'member_level')){
          //Membership levels do not match. So redirect to the information page
        cdashmm_redirect_to_info_page();
      }
      if ( $membership_levels != null ){
       foreach( $membership_levels as $membership_level ) {
         $membership_level_slug = $membership_level->slug;
         $membership_level_id = $membership_level->term_id;
       }
     }else{
       $membership_level_id = '';
     }

      //If membership level matches to the one selected, show the page
      $custom_member_level_meta = cdash_get_custom_member_level_meta();
      if(!in_array($membership_level_id, $custom_member_level_meta)){
        cdashmm_redirect_to_info_page();
      }
    }
  }
}

function cdashmm_check_if_valid($id, $type){
  $options = get_option('cdashmm_options');
  if(!$id){
    switch ($type) {
    case 'person':
      $error = true;
        break;
    case 'business':
      $error = true;
        break;
    case 'member_level':
      $error = true;
        break;
    }
    return false;
  }
  return true;
}

function cdash_display_error($error){
  wp_enqueue_script('jquery-ui');
  wp_enqueue_script( 'jquery-ui-dialog' );
  wp_enqueue_script(' jquery-ui-draggable');
  wp_enqueue_script( 'restrict-content-js', plugin_dir_url(__DIR__) . '/js/restrict_content.js', array( 'jquery-ui-dialog' ) );

  wp_enqueue_style('jquery-ui-styles', plugins_url().'/chamber-dashboard-business-directory/css/jquery_ui_smoothness_theme_css.css');
  wp_enqueue_style( 'restrict-content-css', plugin_dir_url(__DIR__) . 'css/restrict_content.css' );
  ?>
  <div class="cdash_page_display_error" id="cdash_page_display_error">
    <p><?php echo __($error); ?></p>
  </div>
  <?php
}

function cdashmm_redirect_to_info_page(){
  $options = get_option('cdashmm_options');
  if(isset($options['cdashmm_rc_info_page']) && $options['cdashmm_rc_info_page'] !=''){
    $cdashmm_rc_info_page_id = $options['cdashmm_rc_info_page'];
    $info_page_slug = get_post_field( 'post_name', $cdashmm_rc_info_page_id );
    $site_url = home_url() . '/' . $info_page_slug;
  }else{
    $site_url = home_url();
    //$site_url = "https://www.google.com";
  }
  wp_redirect($site_url);
  //die();
  exit;
}

function cdashrc_get_redirect_slug(){
  global $wp;
  //Getting the current page slug
  $current_slug = add_query_arg( array(), $wp->request );
    return $current_slug;
}
function cdashrc_member_redirect_url($current_slug){
  $options = get_option('cdashmm_options');
  $site_url = cdashmm_get_login_page_url();
  if($site_url !=''){
    $redirect_url = $site_url . '/?redirect=' . $current_slug;
  }else{
    $redirect_url = home_url();
  }

  return $redirect_url;
}

function cdashrc_add_query_vars_filter( $vars ) {
  $vars[] = "redirect";
  return $vars;
}
add_filter( 'query_vars', 'cdashrc_add_query_vars_filter' );

function cdashmm_enable_login_logout_link(){
  $options = get_option('cdashmm_options');
  if(isset($options['cdashmm_login_logout_link']) && $options['cdashmm_login_logout_link'] == "1"){
      return true;
  }
  return false;
}

function cdashmm_add_login_logout_link($items, $args){
  $site_url = cdashmm_get_login_page_url();
  $options = get_option( 'cdashmm_options' );
  if(isset($options['cdashmm_selected_menu'])){
        $selected_menu_locations = $options['cdashmm_selected_menu'];
  }else{
      $selected_menu_locations = '';
  }

  if(cdashmm_enable_login_logout_link()){
      if($site_url !=''){
          if(isset($selected_menu_locations) && is_array($selected_menu_locations) && $selected_menu_locations != '') {
              foreach($selected_menu_locations as $location){
                  $menu_location = $location;
                  if( $args->theme_location == $menu_location ){
                      $items .= cdashmm_login_logout_link($site_url);
                  }
              }
          }else{
              $items .= cdashmm_login_logout_link($site_url);
          }
      }
  }
  return $items;
}

function cdashmm_login_logout_link($site_url){
    if(!isset($items)){
        $items = '';
    }
    if ( cdashmm_is_user_logged_in() ){
       $items .= '<li class="right cdashmm_login_logout_link"><a href="'. wp_logout_url('index.php') .'">'. __("Log Out") .'</a></li>';
    } else {
       $items .= '<li class="right cdashmm_login_logout_link"><a href="'. $site_url .'">'. __("Log In") .'</a></li>';
    }
    return $items;
}
add_filter('wp_nav_menu_items', 'cdashmm_add_login_logout_link', 10, 2);

function cdashmm_members_only($atts, $content=null){
  $member_levels = array();
  extract( shortcode_atts(
		array(
			'membership_level' => '', // options: membership level slug
		), $atts )
	);
  $user_id = cdashmm_get_current_user_id();
  $person_id = cdashmm_get_person_id_from_user_id($user_id, true);
  $business_id = cdashmm_get_business_id_from_person_id($person_id, true);
  if($business_id){
    $terms = cdashmm_get_business_memb_level($business_id);
    if ( $terms && ! is_wp_error( $terms ) ){
      //$member_levels = array();
      foreach ( $terms as $term ) {
        $member_levels[] = $term->slug;
      }
    }
  }else{
    $member_levels[] = '';
  }
  if(in_array($membership_level, $member_levels)){
      $return = $content;
    if($content)
        $return .= "<br /><br />";

    return $return;
  }
}
add_shortcode('members_only', 'cdashmm_members_only');

?>
