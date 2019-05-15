<?php 

add_action( 'add_meta_boxes', 'social_media_analytics_logo_image_add_metabox' );
function social_media_analytics_logo_image_add_metabox () {
	add_meta_box( 'logoimagediv', __( 'Logo Image', 'text-domain' ), 'social_media_analytics_logo_image_metabox', 'sma_social_media', 'side', 'low');
}

function social_media_analytics_logo_image_metabox ( $post ) {
	global $content_width, $_wp_additional_image_sizes;

	$image_id = get_post_meta( $post->ID, '_logo_image_id', true );

	$old_content_width = $content_width;
	$content_width = 254;

	if ( $image_id && get_post( $image_id ) ) {

		if ( ! isset( $_wp_additional_image_sizes['post-thumbnail'] ) ) {
			$thumbnail_html = wp_get_attachment_image( $image_id, array( $content_width, $content_width ) );
		} else {
			$thumbnail_html = wp_get_attachment_image( $image_id, 'post-thumbnail' );
		}

		if ( ! empty( $thumbnail_html ) ) {
			$content = $thumbnail_html;
			$content .= '<p class="hide-if-no-js"><a href="javascript:;" id="remove_logo_image_button" >' . esc_html__( 'Remove Logo Image', 'text-domain' ) . '</a></p>';
			$content .= '<input type="hidden" id="upload_logo_image" name="_logo_cover_image" value="' . esc_attr( $image_id ) . '" />';
		}

		$content_width = $old_content_width;
	} else {

		$content = '<img src="" style="width:' . esc_attr( $content_width ) . 'px;height:auto;border:0;display:none;" />';
		$content .= '<p class="hide-if-no-js"><a title="' . esc_attr__( 'Set Logo Image', 'text-domain' ) . '" href="javascript:;" id="upload_logo_image_button" id="set-logo-image" data-uploader_title="' . esc_attr__( 'Choose an image', 'text-domain' ) . '" data-uploader_button_text="' . esc_attr__( 'Set Logo Image', 'text-domain' ) . '">' . esc_html__( 'Set Logo Image', 'text-domain' ) . '</a></p>';
		$content .= '<input type="hidden" id="upload_logo_image" name="_logo_cover_image" value="" />';

	}

	echo $content;
}

add_action( 'save_post', 'social_media_analytics_logo_image_save', 10, 1 );
function social_media_analytics_logo_image_save ( $post_id ) {
	if( isset( $_POST['_logo_cover_image'] ) ) {
		$image_id = (int) $_POST['_logo_cover_image'];
		update_post_meta( $post_id, '_logo_image_id', $image_id );
	}
}



function social_media_analytics_get_meta( $value ) {
	global $post;

	$field = get_post_meta( $post->ID, $value, true );
	if ( ! empty( $field ) ) {
		return is_array( $field ) ? stripslashes_deep( $field ) : stripslashes( wp_kses_decode_entities( $field ) );
	} else {
		return false;
	}
}

function social_media_analytics_add_meta_box() {
	add_meta_box(
		'social_media_analytics-social-media-analytics',
		__( 'Social Media Analytics', 'social_media_analytics' ),
		'social_media_analytics_html',
		'sma_social_media',
		'normal',
		'default'
	);
}
add_action( 'add_meta_boxes', 'social_media_analytics_add_meta_box' );

function social_media_analytics_html( $post) {
	wp_nonce_field( '_social_media_analytics_nonce', 'social_media_analytics_nonce' ); ?>

	<p>
<label for="" class="smt_label_admin">Select Social Media Account</label>
<div class="smt_social_cb_boxes">
		<input type="checkbox" name="social_media_analytics_facebook" id="social_media_analytics_facebook" value="facebook" <?php echo ( social_media_analytics_get_meta( 'social_media_analytics_facebook' ) === 'facebook' ) ? 'checked' : ''; ?>>
		<label for="social_media_analytics_facebook"><?php _e( 'Facebook', 'social_media_analytics' ); ?></label>	</p>	<p>

		<input type="checkbox" name="social_media_analytics_instagram" id="social_media_analytics_instagram" value="instagram" <?php echo ( social_media_analytics_get_meta( 'social_media_analytics_instagram' ) === 'instagram' OR social_media_analytics_get_meta( 'social_media_analytics_instagram' ) === 'API Error' ) ? 'checked' : ''; ?>>
		<label for="social_media_analytics_instagram"><?php _e( 'Instagram', 'social_media_analytics' ); ?></label>	</p>	<p>

		<input type="checkbox" name="social_media_analytics_youtube" id="social_media_analytics_youtube" value="youtube" <?php echo ( social_media_analytics_get_meta( 'social_media_analytics_youtube' ) === 'youtube' ) ? 'checked' : ''; ?>>
		<label for="social_media_analytics_youtube"><?php _e( 'YouTube', 'social_media_analytics' ); ?></label>	</p>	<p>

		<input type="checkbox" name="social_media_analytics_twitter" id="social_media_analytics_twitter" value="twitter" <?php echo ( social_media_analytics_get_meta( 'social_media_analytics_twitter' ) === 'twitter' ) ? 'checked' : ''; ?>>
		<label for="social_media_analytics_twitter"><?php _e( 'Twitter', 'social_media_analytics' ); ?></label>	</p>
</div>





	<div id="smt_facebook_content_cb">
		<label for="social_media_analytics_fb_media_account_link" class="smt_label_admin fb_color_smt"><?php _e( 'Facebook Account Link', 'social_media_analytics' ); ?></label><br>
		<input type="text" name="social_media_analytics_fb_media_account_link" class="smt_input_links" placeholder="Please enter the URL here." id="social_media_analytics_fb_media_account_link" value="<?php echo social_media_analytics_get_meta( 'social_media_analytics_fb_media_account_link' ); ?>">
	</div>

	<div  id="smt_ig_content_cb">
		<label for="social_media_analytics_ig_media_account_link" class="smt_label_admin ig_color_smt"><?php _e( 'Instagram Account Link', 'social_media_analytics' ); ?></label><br>
		<input type="text" name="social_media_analytics_ig_media_account_link" class="smt_input_links" placeholder="Please enter the URL here." id="social_media_analytics_ig_media_account_link" value="<?php echo social_media_analytics_get_meta( 'social_media_analytics_ig_media_account_link' ); ?>">
		
		<input type="text" name="social_media_analytics_ig_fb_media_account" class="smt_input_links" placeholder="Associated facebook page ID" id="social_media_analytics_ig_fb_media_account" value="<?php echo social_media_analytics_get_meta( 'social_media_analytics_ig_fb_media_account' ); ?>">


	</div>

	<div id="smt_yt_content_cb">
		<label for="social_media_analytics_yt_media_account_link" class="smt_label_admin yt_color_smt"><?php _e( 'YouTube channel Link', 'social_media_analytics' ); ?></label><br>
		<input type="text" name="social_media_analytics_yt_media_account_link" class="smt_input_links" placeholder="Please enter the URL here." id="social_media_analytics_yt_media_account_link" value="<?php echo social_media_analytics_get_meta( 'social_media_analytics_yt_media_account_link' ); ?>">
	</div>

	<div id="smt_tw_content_cb">
		<label for="social_media_analytics_tw_media_account_link" class="smt_label_admin tw_color_smt"><?php _e( 'Twitter Account Link', 'social_media_analytics' ); ?></label><br>
		<input type="text" name="social_media_analytics_tw_media_account_link" class="smt_input_links" placeholder="Please enter the URL here." id="social_media_analytics_tw_media_account_link" value="<?php echo social_media_analytics_get_meta( 'social_media_analytics_tw_media_account_link' ); ?>">
	</div>
	<?php
}

function social_media_analytics_save( $post_id ) {
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
	if ( ! isset( $_POST['social_media_analytics_nonce'] ) || ! wp_verify_nonce( $_POST['social_media_analytics_nonce'], '_social_media_analytics_nonce' ) ) return;
	if ( ! current_user_can( 'edit_post', $post_id ) ) return;

	if ( isset( $_POST['social_media_analytics_social_media_account'] ) )
		update_post_meta( $post_id, 'social_media_analytics_social_media_account', esc_attr( $_POST['social_media_analytics_social_media_account'] ) );
	if ( isset( $_POST['social_media_analytics_update_analytical_data'] ) )
		update_post_meta( $post_id, 'social_media_analytics_update_analytical_data', esc_attr( $_POST['social_media_analytics_update_analytical_data'] ) );
	if ( isset( $_POST['social_media_analytics_fb_media_account_link'] ) )
		update_post_meta( $post_id, 'social_media_analytics_fb_media_account_link', esc_attr( $_POST['social_media_analytics_fb_media_account_link'] ) );
	if ( isset( $_POST['social_media_analytics_ig_media_account_link'] ) )
		update_post_meta( $post_id, 'social_media_analytics_ig_media_account_link', esc_attr( $_POST['social_media_analytics_ig_media_account_link'] ) );
	if ( isset( $_POST['social_media_analytics_yt_media_account_link'] ) )
		update_post_meta( $post_id, 'social_media_analytics_yt_media_account_link', esc_attr( $_POST['social_media_analytics_yt_media_account_link'] ) );
	if ( isset( $_POST['social_media_analytics_tw_media_account_link'] ) )
		update_post_meta( $post_id, 'social_media_analytics_tw_media_account_link', esc_attr( $_POST['social_media_analytics_tw_media_account_link'] ) );
	if ( isset( $_POST['social_media_analytics_ig_fb_media_account'] ) )
		update_post_meta( $post_id, 'social_media_analytics_ig_fb_media_account', esc_attr( $_POST['social_media_analytics_ig_fb_media_account'] ) );

	

	// checkboxes

		update_post_meta( $post_id, 'social_media_analytics_facebook', esc_attr( $_POST['social_media_analytics_facebook'] ) );
		update_post_meta( $post_id, 'social_media_analytics_instagram', esc_attr( $_POST['social_media_analytics_instagram'] ) );
		update_post_meta( $post_id, 'social_media_analytics_youtube', esc_attr( $_POST['social_media_analytics_youtube'] ) );
		update_post_meta( $post_id, 'social_media_analytics_twitter', esc_attr( $_POST['social_media_analytics_twitter'] ) );	


	// auto manually selection

	if ( isset( $_POST['social_media_analytics_fb_select_format'] ) )
		update_post_meta( $post_id, 'social_media_analytics_fb_select_format', esc_attr( $_POST['social_media_analytics_fb_select_format'] ) );
	if ( isset( $_POST['social_media_analytics_ig_select_format'] ) )
		update_post_meta( $post_id, 'social_media_analytics_ig_select_format', esc_attr( $_POST['social_media_analytics_ig_select_format'] ) );
	if ( isset( $_POST['social_media_analytics_tw_select_format'] ) )
		update_post_meta( $post_id, 'social_media_analytics_tw_select_format', esc_attr( $_POST['social_media_analytics_tw_select_format'] ) );
	if ( isset( $_POST['social_media_analytics_yt_select_format'] ) )
		update_post_meta( $post_id, 'social_media_analytics_yt_select_format', esc_attr( $_POST['social_media_analytics_yt_select_format'] ) );	

}
add_action( 'save_post', 'social_media_analytics_save' );


function social_media_analytics_authentication_get_meta( $value ) {
	global $post;

	$field = get_post_meta( $post->ID, $value, true );
	if ( ! empty( $field ) ) {
		return is_array( $field ) ? stripslashes_deep( $field ) : stripslashes( wp_kses_decode_entities( $field ) );
	} else {
		return false;
	}
}

function social_media_analytics_authentication_add_meta_box() {
	add_meta_box(
		'social_media_analytics_authentication-social-media-analytics-authentication',
		__( 'Social Media Authentication', 'social_media_analytics_authentication' ),
		'social_media_analytics_authentication_html',
		'sma_social_media',
		'side',
		'low'
	);
}
add_action( 'add_meta_boxes', 'social_media_analytics_authentication_add_meta_box' );

function social_media_analytics_authentication_html( $post) {
	wp_nonce_field( '_social_media_analytics_authentication_nonce', 'social_media_analytics_authentication_nonce' ); ?>

	<p>
		<label for="social_media_analytics_authentication_yt_cid"><?php _e( 'YouTube Client ID', 'social_media_analytics_authentication' ); ?></label><br>
		<input type="text" name="social_media_analytics_authentication_yt_cid" id="social_media_analytics_authentication_yt_cid" value="<?php echo social_media_analytics_authentication_get_meta( 'social_media_analytics_authentication_yt_cid' ); ?>">
	</p>

	<p>
		<label for="social_media_analytics_authentication_yt_cst"><?php _e( 'YouTube Client Secret', 'social_media_analytics_authentication' ); ?></label><br>
		<input type="text" name="social_media_analytics_authentication_yt_cst" id="social_media_analytics_authentication_yt_cst" value="<?php echo social_media_analytics_authentication_get_meta( 'social_media_analytics_authentication_yt_cst' ); ?>">
	</p>


	<?php
	if($_GET['post']){	
		if($_GET['code']){
			$access_token = yt_dash_content($_GET['code'],$_GET['post']);
			update_post_meta($_GET['post'],'smt_youtube_access_token',$access_token);
		}
		else{
			?>
			<a href="<?php echo site_url(); ?>/wp-admin/admin-ajax.php?action=sma_social_media_yt&optionupdate=<?php echo $_GET['post']; ?>&urlopener=<?php echo base64_encode(yt_dash_content('get_url',$_GET['post'])); ?>" target="_blank" class="authorize-yt-acc" >Authorize YouTube Account</a>
			<?php
		}
	}
	?>
	<br />
	<em>Please use this URL in Google Redirect : </em> <br />
	<code style="word-break: break-all;margin-top: 10px;display: block;"><?php echo site_url(); ?>/wp-admin/admin-ajax.php?action=sma_social_media_yt</code>
	<?php
}

function social_media_analytics_authentication_save( $post_id ) {
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
	if ( ! isset( $_POST['social_media_analytics_authentication_nonce'] ) || ! wp_verify_nonce( $_POST['social_media_analytics_authentication_nonce'], '_social_media_analytics_authentication_nonce' ) ) return;
	if ( ! current_user_can( 'edit_post', $post_id ) ) return;

	if ( isset( $_POST['social_media_analytics_authentication_yt_cid'] ) )
		update_post_meta( $post_id, 'social_media_analytics_authentication_yt_cid', esc_attr( $_POST['social_media_analytics_authentication_yt_cid'] ) );
	if ( isset( $_POST['social_media_analytics_authentication_yt_cst'] ) )
		update_post_meta( $post_id, 'social_media_analytics_authentication_yt_cst', esc_attr( $_POST['social_media_analytics_authentication_yt_cst'] ) );
}
add_action( 'save_post', 'social_media_analytics_authentication_save' );


