<div class="wrap">
	<h2><?php _e( 'CMWP-Analytics', 'cmwp-analytics' ); ?></h2>
	<form method="post" action="options.php">
	<?php

		settings_fields( $options_group );
		do_settings_sections('cmwp_analytics');
		submit_button();

	?>
	</form>
</div>