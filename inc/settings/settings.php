<?php
add_action( 'admin_menu', 'sma_add_admin_menu' );
add_action( 'admin_init', 'sma_settings_init' );


function sma_add_admin_menu(  ) {

	add_submenu_page( 'edit.php?post_type=sma_social_media', 'Settings', 'Settings', 'manage_options', 'social_media_analytics', 'sma_options_page' );

}


function sma_settings_init(  ) {

	register_setting( 'pluginPage', 'sma_settings' );

	add_settings_section(
		'sma_pluginPage_section',
		__( 'Please configure social account here', 'sma' ),
		'sma_settings_section_callback',
		'pluginPage'
	);

	add_settings_field(
		'sma_text_field_0',
		__( 'Facebook Access Token', 'sma' ),
		'sma_text_field_0_render',
		'pluginPage',
		'sma_pluginPage_section'
	);




}


function sma_text_field_0_render(  ) {

	$options = get_option( 'sma_settings' );
	?>
	<input type='text' name='sma_settings[sma_text_field_0]' value='<?php echo get_option('fb_access_token'); ?>'>
	<?php

}


function sma_text_field_1_render(  ) {

	$options = get_option( 'sma_settings' );
	?>

	<input type='text' name='sma_settings[sma_text_field_1]' value='<?php echo $options['sma_text_field_1']; ?>'>
<?php
}


function sma_text_field_2_render(  ) {

	$options = get_option( 'sma_settings' );
	?>
	<input type='text' name='sma_settings[sma_text_field_2]' value='<?php echo $options['sma_text_field_2']; ?>'>
	<?php
}


function sma_text_field_3_render(  ) {

	$options = get_option( 'sma_settings' );
	?>
	<input type='text' name='sma_settings[sma_text_field_3]' value='<?php echo $options['sma_text_field_3']; ?>'>
	<?php
}


function sma_settings_section_callback(  ) {

	echo __( '', 'sma' );

}


function sma_options_page(  ) {

	?>
	<form action='options.php' method='post'>

		<h2>Social Media Analytics</h2>
		<?php
		settings_fields( 'pluginPage' );
		do_settings_sections( 'pluginPage' );
		?>
<?php echo do_shortcode('[fbl_login_button redirect="" hide_if_logged=""]'); ?>
		<?php
		submit_button();
		?>

	</form>
	<?php

}

?>
