<?php 
//Business Directory Shortcode rendering
function cdashmm_pricing_table_block(){
    if ( function_exists( 'register_block_type' ) ) {
        // Hook server side rendering into render callback
        register_block_type(
            'cdash-bd-blocks/pricing-table', [
                'render_callback' => 'cdashmm_pricing_table_block_callback',
                'attributes'  => array(
                    'showDescription'  => array(
                        'type'  => 'boolean',
                        'default' => 'true',
                    ),
                    'showPerks'  => array(
                        'type'  => 'boolean',
                        'default' => 'true',
                    ),
                    'joinNowFormPage'  => array(
                        'type'  => 'string',
                        'default' => '',
                    ),
                    'levelNameFontSize'  => array(
                        'type'  => 'number',
                        'default' => '30',
                    ),
                    'levelNameFontColor'  => array(
                        'type'  => 'string',
                        'default' => '',
                    ),
                    'levelNameBckColor'  => array(
                        'type'  => 'string',
                        'default' => '',
                    ),
                    'levelDescFontSize'  => array(
                        'type'  => 'number',
                        'default' => '18',
                    ),
                    'levelDescFontColor'  => array(
                        'type'  => 'string',
                        'default' => '',
                    ),
                    'levelDescBckColor'  => array(
                        'type'  => 'string',
                        'default' => '',
                    ),
                    'levelPerksFontSize'  => array(
                        'type'  => 'number',
                        'default' => '18',
                    ),
                    'levelPerksFontColor'  => array(
                        'type'  => 'string',
                        'default' => '',
                    ),
                    'levelPerksBckColor'  => array(
                        'type'  => 'string',
                        'default' => '',
                    ),
                    'levelPerksTextAlign'  => array(
                        'type'  => 'string',
                        'default' => 'center',
                    ),
                    'levelPriceFontSize'  => array(
                        'type'  => 'number',
                        'default' => '25',
                    ),
                    'levelPriceFontColor'  => array(
                        'type'  => 'string',
                        'default' => '',
                    ),
                    'levelPerksBckColor'  => array(
                        'type'  => 'string',
                        'default' => '',
                    ),
                    'buttonName'  => array(
                        'type'  => 'string',
                        'default' => __('Join Now', 'cdashmm'),
                    ),
                    'buttonColor'  => array(
                        'type'  => 'string',
                        'default' => '',
                    ),
                    'buttonFontSize'  => array(
                        'type'  => 'number',
                        'default' => '15',
                    ),
                    'buttonBckColor'  => array(
                        'type'  => 'string',
                        'default' => '',
                    ),
                    'setPopular'  => array(
                        'type'  => 'boolean',
                        'default' => 'false',
                    ),
                    'displaySelectMemberLevels'  => array(
                        'type'  => 'boolean',
                        'default' => 'false',
                    ),
                    'membershipLevelArray'  => array(
                        'type'  => 'array',
                        'default' => '',
                    ),
                ),
            ]
        );
    }
}

add_action( 'init', 'cdashmm_pricing_table_block' );

function cdashmm_pricing_table_block_callback($attributes){
    //$levels = get_terms( 'membership_level' );
    $levels = get_terms( array( 
        'taxonomy' => 'membership_level',
        'hide_empty'   => 0
    ) );
    $desc_toggle = '';
    $perks_toggle = ''
;    if($attributes['showDescription'] == "true"){
        $desc_toggle = "show";
    }else{
        $desc_toggle = "hide";
    }

    if($attributes['showPerks'] == "true"){
        $perks_toggle = "show";
    }else{
        $perks_toggle = "hide";
    }
    if(isset($attributes['levelNameFontSize'])){
        $title_font_size = $attributes['levelNameFontSize']."px";
    }

    if(isset($attributes['levelNameFontColor'])){
        $title_font_color = $attributes['levelNameFontColor'];
    }else{
        $title_font_color = "#000";
    }
    
    if(isset($attributes['levelNameBckColor'])){
        $title_bck_color = $attributes['levelNameBckColor'];
    }else{
        $title_bck_color = "";
    }

    if(isset($attributes['levelDescFontSize'])){
        $desc_font_size = $attributes['levelDescFontSize']."px";
    }

    if(isset($attributes['levelDescFontColor'])){
        $desc_font_color = $attributes['levelDescFontColor'];
    }

    if(isset($attributes['levelDescBckColor'])){
        $desc_bck_color = $attributes['levelDescBckColor'];
    }

    if(isset($attributes['levelPerksTextAlign'])){
        $perks_text_align = $attributes['levelPerksTextAlign'];
    }

    if(isset($attributes['levelPerksFontSize'])){
        $perks_font_size = $attributes['levelPerksFontSize']."px";
    }

    if(isset($attributes['levelPerksFontColor'])){
        $perks_font_color = $attributes['levelPerksFontColor'];
    }

    if(isset($attributes['levelPerksBckColor'])){
        $perks_bck_color = $attributes['levelPerksBckColor'];
    }

    if(isset($attributes['levelPriceFontSize'])){
        $price_font_size = $attributes['levelPriceFontSize']."px";
    }

    if(isset($attributes['levelPriceFontColor'])){
        $price_font_color = $attributes['levelPriceFontColor'];
    }

    if(isset($attributes['buttonName'])){
        $button_name = $attributes['buttonName'];
    }

    if(isset($attributes['buttonColor'])){
        $button_color = $attributes['buttonColor'];
    }

    if(isset($attributes['buttonFontSize'])){
        $button_font_size = $attributes['buttonFontSize']."px";
    }

    if(isset($attributes['buttonBckColor'])){
        $button_bck_color = $attributes['buttonBckColor'];
    }

    if(isset($attributes['joinNowFormPage'])){
        $join_now_page = $attributes['joinNowFormPage'];
    }

    $join_url = site_url().'/'.$join_now_page;
     
    $pricing_table = "";
    $pricing_table .= "<div class='cdashmm_pricing_table_grid'>";
    if (!empty($levels)){
        foreach ($levels as $level){
            $levelid = $level->term_id;
            $price = cdashmm_get_membership_level_price($levelid);
		    $price_term = cdashmm_price_term();
            $pricing_table .= "<div class='cdashmm_pricing_table_column'>";

            $pricing_table .= "<div class='memb_level_heading' style='color:".$title_font_color."; background-color:".$title_bck_color."'><p style='font-size:". $title_font_size."'>$level->name</p></div>";

            $pricing_table .= "<div class='memb_level_desc " . $desc_toggle . "'><p style='color:".$desc_font_color."; font-size:".$desc_font_size."; background-color: ".$desc_bck_color."'>$level->description</p></div>";                

            $pricing_table .= "<div class='memb_level_price'><p style='color:".$price_font_color."; font-size:".$price_font_size."'>" . cdashmm_display_price( $price ).'<span class="price_term">/'.$price_term . "</span></p></div>";

            $pricing_table .= "<div class='memb_level_perks " . $perks_toggle . "' style='text-align:".$perks_text_align.";color:".$perks_font_color.";font-size:".$perks_font_size."; background-color: ".$perks_bck_color."'><p>".get_tax_meta( $level->term_id,'perks' )."</p></div>";

            $pricing_table .= "<div class='memb_join_now_button'><a style='color:".$button_color."; font-size:".$button_font_size."; background-color:".$button_bck_color."' href='".$join_url."?membership_level=".$level->slug."' class='button'>$button_name</a></div>";

            $pricing_table .= "</div>";
        }

    }
    $pricing_table .= "</div>";

    if(cdashmm_get_payment_options()){
        return $pricing_table;
    }else{
        return "Please enable Chamber Dashboard Payment Options plugin to use this block.";
    }
    //return $pricing_table;


}