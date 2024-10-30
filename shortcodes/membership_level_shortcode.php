<?php
if ( ! defined( 'ABSPATH' ) ) exit;
// ------------------------------------------------------------------------
// shortcode to display membership levels, perks, and prices
// ------------------------------------------------------------------------
function cdashmm_membership_levels_shortcode( $atts ) {
	// Attributes
	extract( shortcode_atts(
		array(
			'orderby' => 'priority', // options: name, count, priority
			'exclude' => '',
			'order' => 'ASC'
		), $atts )
	);
	// get all of the membership levels
	if( $orderby == 'priority' ) {
		$args = 'orderby=term_order&order='.$order.'&hide_empty=0';
	} elseif( $orderby == 'name' ) {
		$args = 'orderby=name&order='.$order.'&hide_empty=0';
	}
	elseif( $orderby == 'count' ) {
		$args = 'orderby=count&order='.$order.'&hide_empty=0';
	} else {
		$args = 'hide_empty=0';
	}
	if( '' !== 'exclude' ) {
		$args .= '&exclude=' . $exclude;
	}
	$levels = get_terms( 'membership_level', $args );
	$levels_view = '<div id="membership-levels">';
	// go through each level and display the information
	foreach( $levels as $level ) {
		$levelid = $level->term_id;
		$price = cdashmm_get_membership_level_price($levelid);
		$price_term = cdashmm_price_term();
		$levels_view .= '<div class="level">';
		$levels_view .= '<h3>' . $level->name . '</h3>';
		$levels_view .= do_shortcode( stripslashes( wpautop( get_tax_meta( $level->term_id,'perks' ) ) ) );
		$levels_view .= '<p class="price">' . cdashmm_display_price( $price ) .'/'. __(' <span class="price_term">'. $price_term .'</span>', 'cdashmm' ) . '</p>';
		$levels_view .= '</div>';
	}
	$levels_view .= '</div>';
	return $levels_view;
}
add_shortcode( 'membership_levels', 'cdashmm_membership_levels_shortcode' );
?>