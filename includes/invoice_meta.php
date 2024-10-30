<div class="my_meta_control">

<table class="form-table">
	<tr>
		<th scope="row"><?php _e('Invoice Number', 'cdashmm'); ?></th>
		<td>
			<input id="invoice_number" type="text" size="10" name="<?php $metabox->the_name('invoice_number'); ?>" value="<?php $metabox->the_value('invoice_number'); ?>" />
		</td>
	</tr>

	<tr>
		<th scope="row"><?php _e('Membership Level', 'cdashmm'); ?></th>
		<td>
			<?php $metabox->the_field('item_membershiplevel'); ?>
			<?php $selected = ' selected="selected"'; ?>
			<select name="<?php $metabox->the_name(); ?>" id="level">
				<option value=""></option>
				<?php // get all the levels
				$levels = get_terms( 'membership_level', 'hide_empty=0' );
				foreach( $levels as $level ) { ?>
					<option value="<?php echo $level->term_id; ?>" <?php if ($metabox->get_the_value() == $level->term_id) echo $selected; ?>><?php echo $level->name; ?></option>
				<?php } ?>
			</select>
			<?php _e( 'Cost: ', 'cdashmm' ); ?><input id="item_membershipamt" type="text" size="10" name="<?php $metabox->the_name('item_membershipamt'); ?>" value="<?php $metabox->the_value('item_membershipamt'); ?>" />
			<?php $membership_amount = $metabox->the_value('item_membershipamt'); ?>
		</td>
	</tr>

	<tr>
		<th scope="row"><?php _e('Items', 'cdashmm'); ?></th>
		<td id="items">
			<?php while($mb->have_fields_and_multi('items')): ?>
			<?php $mb->the_group_open(); ?>
		 		<div class="item name">
					<?php $mb->the_field('item_name'); ?>
					<label>Item Name/Description</label>
					<p><input type="text" name="<?php $mb->the_name(); ?>" value="<?php $mb->the_value(); ?>"/></p>
				</div>
		 		<div class="item description">
					<?php $mb->the_field('item_amount'); ?>
					<label>Amount</label>
					<p><input type="text" name="<?php $mb->the_name(); ?>" value="<?php $mb->the_value(); ?>" class="item_amount" /></p>
				</div>
				<a href="#" class="dodelete button">Remove Item</a>
			<?php $mb->the_group_close(); ?>
			<?php endwhile; ?>
			<p class="clearfix"><a class="docopy-items button">Add Another Item</a></p>
		</td>
	</tr>

	<tr>
		<th scope="row"><?php _e('Donation', 'cdashmm'); ?></th>
		<td>
			<input id="item_donation" type="text" size="10" name="<?php $metabox->the_name('item_donation'); ?>" value="<?php $metabox->the_value('item_donation'); ?>"/>
			<span>Enter a number only, no currency symbols</span>
		</td>
	</tr>
	<?php $options = get_option( 'cdashmm_options' );
	//if( isset( $options['use_processing_fee'] ) ) { ?>
		<tr>
			<th scope="row"><?php _e('Processing Fee', 'cdashmm'); ?></th>
			<td>
			<?php 
				//$fee = '';
				?>
				<input id="item_processing_fee" type="text" size="10" name="<?php $metabox->the_name('item_processing_fee'); ?>" value="<?php 
				if(isset( $options['use_processing_fee'] ) && isset($options['processing_fee_amount']) && $options['processing_fee_amount'] != '0'){
					$fee = $options['processing_fee_amount'];
				}else{
					$fee = $metabox->the_value('item_processing_fee');
				}
				echo $fee; ?>"/>
				<?php
				if( !isset( $options['use_processing_fee'] ) ) {
					$fee_description = __("Enter a number only, no currency symbols. (Please note that processing fee is currently disabled.)", 'cdashmm');
				} else {
					$fee_description = __("Enter a number only, no currency symbols.", 'cdashmm');
				}
 ?>
				<span><?php echo $fee_description; ?></span>
			</td>
		</tr>
	<?php //} ?>
    <?php $options = get_option( 'cdashmm_options' );
	//if( isset( $options['charge_tax'] ) ) { ?>
		<tr>
			<th scope="row"><?php _e('Tax', 'cdashmm'); ?></th>
			<td>
				<input id="item_tax" type="text" size="10" name="<?php $metabox->the_name('item_tax'); ?>" 
                value="<?php 
				if(isset( $options['charge_tax']) && isset($options['tax_rate']) && $options['tax_rate'] != '0'){
					//$tax = $options['tax_rate'];
					//$total = $membership_amount + $fee;
					//$tax = $tax = round( ( ( $options['tax_rate'] / 100 ) * $total ), 2 );
					$tax = $metabox->the_value('item_tax');
				}else{
					$tax = $metabox->the_value('item_tax');
				}
				echo $tax; ?>"/>
				<?php
					if( !isset( $options['charge_tax'] ) ) {
						$tax_description = __("Enter a number only, no currency symbols. (Please note that tax is currently disabled.", 'cdashmm');
					}else{
						$tax_description = __("Enter a number only, no currency symbols.", 'cdashmm');
					}
				?>
				<span><?php echo $tax_description; ?></span>
			</td>
		</tr>
	<?php //} ?>

	<tr>
		<th scope="row"><?php _e('Total Amount Due', 'cdashmm'); ?></th>
		<td>
			<input id="amount" type="text" size="10" name="<?php $metabox->the_name('amount'); ?>" value="<?php $metabox->the_value('amount'); ?>"/>
			<a id="calculate" class="button">Calculate Total</a>
		</td>
	</tr>

	<tr>
		<th scope="row"><?php _e('Due Date', 'cdashmm'); ?></th>
		<td>
			<input id="duedate" type="text" size="10" name="<?php $metabox->the_name('duedate'); ?>" value="<?php $metabox->the_value('duedate'); ?>"/>
		</td>
	</tr>

	<tr>
		<th scope="row"><?php _e('Total Amount Paid', 'cdashmm'); ?></th>
		<td>
			<input id="paidamt" type="text" size="10" name="<?php $metabox->the_name('paidamt'); ?>" value="<?php $metabox->the_value('paidamt'); ?>"/>
		</td>
	</tr>

	<tr>
		<th scope="row"><?php _e('Date Paid', 'cdashmm'); ?></th>
		<td>
			<input id="paiddate" type="text" size="10" name="<?php $metabox->the_name('paiddate'); ?>" value="<?php $metabox->the_value('paiddate'); ?>"/>
		</td>
	</tr>

	<tr>
		<th scope="row"><?php _e('Payment Method', 'cdashmm'); ?></th>
		<td>
			<input id="paymethod" type="text" size="10" name="<?php $metabox->the_name('paymethod'); ?>" value="<?php $metabox->the_value('paymethod'); ?>"/>
		</td>
	</tr>

	<tr>
		<th scope="row"><?php _e('Transaction ID', 'cdashmm'); ?></th>
		<td>
			<input id="transaction" type="text" size="10" name="<?php $metabox->the_name('transaction'); ?>" value="<?php $metabox->the_value('transaction'); ?>"/>
		</td>
	</tr>
</table>
</div>