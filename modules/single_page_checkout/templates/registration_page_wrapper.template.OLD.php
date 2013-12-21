<div id="multi-event-registration" class="ui-widget">

<?php if ( ! $empty_cart ) : ?>

	<h2 id="mer-reg-page-steps-big-hdr" class="mer-reg-page-steps-big-hdr"><?php _e(' Steps', 'event_espresso'); ?></h2>
	<div id="mer-reg-page-steps-display-dv">
		<div id="mer-reg-page-step-1-display-dv" class="mer-reg-page-step-display-dv <?php echo $step_display_dv_1_class; ?>">
			<a class="mer-reg-page-step-big-nmbr">1</a> <h2 id="mer-reg-page-step-1-display-hdr" class="mer-reg-page-steps-display-hdr">&nbsp;<?php _e('Attendee<br/>&nbsp;Information', 'event_espresso'); ?></h2>
		</div>
		<div class="mer-reg-page-step-arrow-dv">&raquo;</div>
		<div id="mer-reg-page-step-2-display-dv" class="mer-reg-page-step-display-dv <?php echo $step_display_dv_2_class; ?>">
			<a class="mer-reg-page-step-big-nmbr">2</a> 
			<h2 id="mer-reg-page-step-2-display-hdr" class="mer-reg-page-steps-display-hdr">&nbsp;<?php _e('Payment<br/>&nbsp;Options', 'event_espresso'); ?></h2>
		</div>
		<div class="mer-reg-page-step-arrow-dv">&raquo;</div>
		<div id="mer-reg-page-step-3-display-dv" class="mer-reg-page-step-display-dv <?php echo $step_display_dv_3_class; ?>">
			<a class="mer-reg-page-step-big-nmbr">3</a> <h2 id="mer-reg-page-step-3-display-hdr" class="mer-reg-page-steps-display-hdr">&nbsp;<?php _e('Registration<br/>&nbsp;Confirmation', 'event_espresso'); ?></h2>
		</div>
		<div class="clear-float"></div>
	</div>
	
	<?php do_action('before_reg-page-steps'); ?>
	<?php echo $registration_steps; ?>

<?php else : ?>
	<h2 id="mer-reg-page-empty-cart-hdr" class="mer-reg-page-step-title-hdr"><?php _e('There is currently nothing in the Event Queue', 'event_espresso'); ?></h2>
	<p><?php _e('You need to select at least one event before you can proceed with the registration process', 'event_espresso'); ?></p>
<?php endif; // $! empty_cart ?>
	
</div>
