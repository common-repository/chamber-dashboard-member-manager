<?php 
//Business Directory Shortcode rendering
function cdashmm_membership_form_block(){
    //cdashmm_get_custom_fields();
    if ( function_exists( 'register_block_type' ) ) {
        register_block_type(
            'cdash-bd-blocks/membership-form', [
                'render_callback' => 'cdash_membership_form_block_callback',
                'attributes'    => array(
                    'action'  => array(
                        'type'  => 'string',
                        'default' => 'signup',
                    ),
                    'customFields'  => array(
                        'type'  => 'array',
                        'default'   => [],
                    ),
                    'busDetailsSectionTitle'  => array(
                        'type'  => 'string',
                        'default' => __('Business Details', 'cdashmm'),
                    ),
                    'customFieldsSectionTitle'  => array(
                        'type'  => 'string',
                        'default' => __('Custom Fields', 'cdashmm'),
                    ),
                    'addDescriptionField'  => array(
                        'type'  => 'boolean',
                        'default'   => false,
                    ),
                    'addLogoUpload'  => array(
                        'type'  => 'boolean',
                        'default'   => false,
                    ),
                )
            ]
        );
    }
}

add_action( 'init', 'cdashmm_membership_form_block' );

function cdash_membership_form_block_callback($attributes){
    $membership_form = cdashmm_membership_signup_form($attributes);

    return $membership_form;
}