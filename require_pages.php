<?php
/*All the pages that are required for the Member Manager */
// Require options stuff
require_once( plugin_dir_path( __FILE__ ) . 'options.php' );

// Require views
require_once( plugin_dir_path( __FILE__ ) . 'views.php' );

// Require PayPal handler
require_once( plugin_dir_path( __FILE__ ) . 'paypal-ipn.php' );

// Require payment report
require_once( plugin_dir_path( __FILE__ ) . 'payment-report.php' );

// Require payment report
require_once( plugin_dir_path( __FILE__ ) . 'cdashmm-functions.php' );

require_once( plugin_dir_path( __FILE__ ) . 'member_info.php' );

require_once( plugin_dir_path( __FILE__ ) . 'restrict_content/rc_functions.php' );

foreach ( glob( plugin_dir_path( __FILE__ ) . "shortcodes/*.php" ) as $file ) {
    require_once $file;
}

foreach ( glob( plugin_dir_path( __FILE__ ) . "blocks/*.php" ) as $file ) {
    require_once $file;
}

foreach ( glob( plugin_dir_path( __FILE__ ) . "free_membership_level/*.php" ) as $file ) {
    require_once $file;
}

foreach ( glob( plugin_dir_path( __FILE__ ) . "options_render/*.php" ) as $file ) {
    require_once $file;
}

?>