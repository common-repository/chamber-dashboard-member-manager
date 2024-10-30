<?php
function cdashmm_login_form_block(){
    if ( function_exists( 'register_block_type' ) ) {
        register_block_type(
            'cdash-bd-blocks/login-form', [
                'render_callback' => 'cdash_login_form_block_callback',
            ]
        );
    }
}
add_action( 'init', 'cdashmm_login_form_block' );

function cdash_login_form_block_callback(){
    $login_form = cdashmm_member_login_form_shortcode();

    return $login_form;
}
?>