<?php
function cdashmm_disable_process_fee_for_free_memb(){
    $options = get_option( 'cdashmm_options' );
    if(isset($options['disable_process_fee_for_free_memb']) && $options['disable_process_fee_for_free_memb'] == 1){
        return true;
    }else{
        return false;
    }
}
?>