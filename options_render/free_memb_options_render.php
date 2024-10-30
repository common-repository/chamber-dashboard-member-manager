<?php
function cdashmm_disable_process_fee_for_free_memb_render( $args ) {
	$options = get_option( 'cdashmm_options' );
	?>
	<input type='checkbox' class="cdashmm_disable_process_fee_for_free_memb" name='cdashmm_options[disable_process_fee_for_free_memb]' <?php if(isset($options['disable_process_fee_for_free_memb'])) { checked( $options['disable_process_fee_for_free_memb'], 1 ); } ?> value='1'>
	<span class="description"><?php echo $args[0]; ?></span>
	<?php
}
?>