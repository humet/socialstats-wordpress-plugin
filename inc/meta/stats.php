<?php 

function social_media_stats_get_meta( $value ) {
	global $post;

	$field = get_post_meta( $post->ID, $value, true );
	if ( ! empty( $field ) ) {
		return is_array( $field ) ? stripslashes_deep( $field ) : stripslashes( wp_kses_decode_entities( $field ) );
	} else {
		return false;
	}
}

function social_media_stats_add_meta_box() {
	add_meta_box(
		'social_media_stats-social-media-stats',
		__( 'Social Media Stats', 'social_media_stats' ),
		'social_media_stats_html',
		'sma_social_media',
		'normal',
		'default'
	);
}
add_action( 'add_meta_boxes', 'social_media_stats_add_meta_box' );

function social_media_stats_html( $post) {
	error_reporting(0);
	wp_nonce_field( '_social_media_stats_nonce', 'social_media_stats_nonce' ); ?>
	<?php 
	smt_all_js_css_scripts();
	?>
	<?php 
	$username = basename(get_post_meta($post->ID,'social_media_analytics_fb_media_account_link',true));
	$facebookdata = smt_get_facebook_page_gender_data($username);
	?>
	<div id="facebook_container">	
	<?php if(!$facebookdata){
		?>
		<div class="api_error_smt">
			There is issue with API Details - Please check it!
		</div>
		<?php
	}
	else {
	 ?>
	<div class="smt_profile_user img_user_ig">
		<?php 
			$profileurl = smt_get_facebook_page_profile_url($username);
		?>
		<img src="<?php print_r($profileurl->data->url); ?>" />
	</div>	 
	<table class="smt_table">
		<tr>
			<th class="smt_head">
				Data
			</th>
			<th class="smt_head">
				Value
			</th>
			<tr>
				<td class="smt_td">
					Likes
				</td>
				<td class="smt_td">
					<?php echo smt_get_facebook_page_likes($username); ?>
					<input type="hidden" value="<?php echo smt_get_facebook_page_likes($username); ?>" name="fb_total_likes" />
				</td>
			</tr>
			<?php 
			$get_raw_gender = smt_get_facebook_raw_data('insights',$username);
			?>
			<?php
			$maletotal = 0;
			$femaletotal = 0;
			$genderagedata = json_decode(json_encode($get_raw_gender->data[0]->values[0]->value), true);
			foreach ($genderagedata as $key => $value) {
				$classfic = explode(".", $key);
				if($classfic[0] == "F"){
					$femaletotal += $value;
				}
				else {
					$maletotal += $value;
				}
			}
			$totallikes = $maletotal+$femaletotal;
			$maleperc = ($maletotal*100)/$totallikes;
			$femperc = ($femaletotal*100)/$totallikes;
			?>

			<?php
			$array = json_decode(json_encode($facebookdata), true);
			arsort($array);

			$first_key = key($array);
			$first_value = reset($array);
			?>
			<tr>
				<td>
					Highest Gender
				</td>
				<td>
					<?php $gender = explode(".",$first_key); if($gender[0] == "M"){ echo "Male"; } else{ echo "Female"; } ?>
					<span class="smt_numperctage">Percentage: <strong>(<?php echo round($maleperc) ?>% Male) - (<?php echo round($femperc) ?>% Female)</strong></span>
				</td>
				<input type="hidden" value="<?php $gender = explode(".",$first_key); if($gender[0] == "M"){ echo "Male"; } else{ echo "Female"; } ?>" name="fb_highest_gender" />
				<input type="hidden" value="<?php echo round($maleperc) ?>" name="fb_per_male" />
				<input type="hidden" value="<?php echo round($femperc) ?>" name="fb_pe_female" />
			</tr>
			<tr>
				<td>
					Highest Age Range (Lower)
				</td>
				<td>
					<?php $age = explode(".",$first_key); $age_br = explode("-",$age[1]); echo $age_br[0]; ?>
					<input type="hidden" value="<?php echo $age_br[0]; ?>" name="fb_lowest_gender_age" />
				</td>
			</tr>
			<tr>
					<td>
						Highest Age Range (Higher)
					</td>
					<td>
						<?php echo $age_br[1]; ?>
						<input type="hidden" value="<?php echo $age_br[1]; ?>" name="fb_higest_gender_age" />
					</td>
				</tr>

			</tr>
	</table>
	<?php } ?>
</div>
<!-- IG -->
<?php 
$username = basename(get_post_meta($post->ID,'social_media_analytics_ig_media_account_link',true));
$fbpageid = get_post_meta($post->ID,'social_media_analytics_ig_fb_media_account',true);
?>
<div class="ig_container">
	<?php if(smt_get_insta_follower($fbpageid)) { ?>
		<div class="smt_profile_user img_user_ig">
			<img src="<?php echo smt_get_insta_profile_url($fbpageid); ?>" />
		</div>					
		<table class="smt_table">		
			<tr>
				<th class="smt_head">
					Data
				</th>
				<th class="smt_head">
					Value
				</th>
			</tr>
			<tr>
				<td class="smt_td">
					Likes
				</td>
				<td class="smt_td">
					<?php echo smt_get_insta_follower($fbpageid);  ?>
					<input type="hidden" value="<?php echo smt_get_insta_follower($fbpageid);  ?>" name="ig_total_likes" />
				</td>
			</tr>
			<?php
			if(smt_get_insta_biz_id($fbpageid)) {
				$iggenderdata =	smt_get_insta_demographics($fbpageid);
				$maletotal = 0;
				$femaletotal = 0;
				$genderagedata = json_decode(json_encode($iggenderdata), true);
				foreach ($genderagedata as $key => $value) {
					$classfic = explode(".", $key);
					if($classfic[0] == "F"){
						$femaletotal += $value;
					}
					else {
						$maletotal += $value;
					}
				}
				$totallikes = $maletotal+$femaletotal;
				$maleperc = ($maletotal*100)/$totallikes;
				$femperc = ($femaletotal*100)/$totallikes;
			
			?>	
			<tr>
				<td>
					Highest Gender
				</td>
				<td>
					<?php 
				
				$array = json_decode(json_encode($iggenderdata), true);
				arsort($array);
				$first_key = key($array);
				$first_value = reset($array);	

					?>
				<?php $gender = explode(".",$first_key); if($gender[0] == "M"){ echo "Male"; } else{ echo "Female"; } ?>	
					<span class="smt_numperctage">Percentage: <strong>(<?php echo round($maleperc,2) ?>% Male) - (<?php echo round($femperc,2) ?>% Female)</strong></span>
					<input type="hidden" value="<?php $gender = explode(".",$first_key); if($gender[0] == "M"){ echo "Male"; } else{ echo "Female"; } ?>" name="ig_highest_gender" />
					<input type="hidden" value="<?php echo round($maleperc,2); ?>" name="ig_per_male" />
					<input type="hidden" value="<?php echo round($femperc,2); ?>" name="ig_per_female" />
				</td>
			</tr>
			<tr>
				<td>
					Highest Age Range (Lower)
				</td>
				<td>
					<?php $age = explode(".",$first_key); $age_br = explode("-",$age[1]); echo $age_br[0]; ?>
					<input type="hidden" value="<?php echo $age_br[0]; ?>" name="ig_lowest_gender_age" />
				</td>
			</tr>
			<tr>
				<td>
					Highest Age Range (Higher)
				</td>
				<td>
					<?php echo $age_br[1]; ?>
					<input type="hidden" value="<?php echo $age_br[1]; ?>" name="ig_higest_gender_age" />
				</td>
			</tr>
			<?php } else { ?>
			<tr>
				<td colspan="2">Can't get full demographic information as account is private</td>
			</tr>
			<?php } ?>
		</table>
	<?php }
	else{
		?>
		<div class="api_error_smt">
			There has been an error with the API.
		</div>
		<?php
	}
	 ?>
</div>

<!-- YouTube Container -->
<?php 
$options = get_option( 'sma_settings' );
$username = basename(get_post_meta($post->ID,'social_media_analytics_yt_media_account_link',true));
$thumbdtata = json_decode(file_get_contents('https://www.googleapis.com/youtube/v3/channels?part=snippet&fields=items%2Fsnippet%2Fthumbnails%2Fdefault&id='.$username.'&key=AIzaSyA2q0T0Sr9sW7RBdOIP9rCw0x19KVOMWxI'));
?>
<div id="youtube_container">
	<?php
		$smtaccyttoken = get_post_meta($_GET['post'],'smt_youtube_access_token',true);
		$smtaccyttoken = json_decode($smtaccyttoken);
		$ytaccesstoken = $smtaccyttoken->access_token;

	 ?>
		<div class="smt_profile_user img_user_ig">
					<img src="<?php echo $thumbdtata->items[0]->snippet->thumbnails->default->url; ?>">
		</div>
		<table class="smt_table">
			<tr>
				<th class="smt_head">
					Data
				</th>
				<th class="smt_head">
					Value
				</th>
			</tr>
			<tr>
				<td class="smt_td">
					Subscribers
				</td>
				<td class="smt_td">
					<?php 
	echo smt_youtube_get_number_of_subscribers('AIzaSyA2q0T0Sr9sW7RBdOIP9rCw0x19KVOMWxI',$username);

	?>
	<input type="hidden" value="<?php  echo smt_youtube_get_number_of_subscribers('AIzaSyA2q0T0Sr9sW7RBdOIP9rCw0x19KVOMWxI',$username); ?>" name="yt_total_likes" />
				</td>
			</tr>
	<?php 
	$arrayinfo = "";
	$allgenderrec = smt_youtube_get_age_gender($ytaccesstoken,$username);

	if($allgenderrec){
	$arrayinfo = $allgenderrec->rows;
	usort($arrayinfo, function($a, $b) {
    return $b[2] - $a[2];
	});
	}
	$arrayinfo = $arrayinfo[0];
	$ytmalegen = 0;
	$ytfemalegen = 0;
	$allgendatayt = $allgenderrec->rows;
	?>
	<?php
	foreach ($allgendatayt as $value) {
		if($value[0] == "female"){
			$ytfemalegen += $value[2];
		}
		else{
			$ytmalegen += $value[2];
		}
	}
	?>			
			<tr>
				<td>
					Highest Gender
				</td>
				<td>
					<?php echo $arrayinfo[0]; ?>
					<span class="smt_numperctage">Percentage: <strong>(<?php echo round($ytmalegen) ?>% Male) - (<?php echo round($ytfemalegen) ?>% Female)</strong></span>
					<input type="hidden" value="<?php echo $arrayinfo[0]; ?>" name="yt_highest_gender" />
					<input type="hidden" value="<?php echo $ytmalegen; ?>" name="yt_per_male" />
					<input type="hidden" value="<?php echo $ytfemalegen; ?>" name="yt_per_female" />
				</td>
			</tr>
			<tr>
				<td>
					Highest Age Range (Lower)
				</td>
				<td>
					<?php $highage = explode("e",$arrayinfo[1]); ?>
					<?php $ytagerange = explode("-",$highage[1]);
					echo $ytagerange[0];
					?>
					<input type="hidden" name="yt_lowest_gender_age" value="<?php echo $ytagerange[0]; ?>" />
				</td>
			</tr>
			<tr>
				<td>
					Highest Age Range (Higher)
				</td>
				<td>
					<?php $highage = explode("e",$arrayinfo[1]); ?>
					<?php echo $ytagerange[1]; ?>
					<input type="hidden" name="yt_higest_gender_age" value="<?php echo $ytagerange[1]; ?>" />
				</td>
			</tr>
		</table>
</div>
<?php 
$username = basename(get_post_meta($post->ID,'social_media_analytics_tw_media_account_link',true)); 
?>
<div id="twitter_container">
		<div class="smt_profile_user img_user_ig">
		<?php 
			$profileurl = smt_get_twitter_profile_url($username);
		?>
			<img src="<?php echo $profileurl; ?>" />
		</div>	 
		<table class="smt_table">
			<tr>
				<th class="smt_head">
					Data
				</th>
				<th class="smt_head">
					Value
				</th>
			</tr>
			<tr>
				<td class="smt_td">
					Followers
				</td>
				<td class="smt_td">
					<?php echo smt_get_twitter_follower($username);  ?>
					<input type="hidden" value="<?php echo smt_get_twitter_follower($username);  ?>" name="tw_total_likes" />
				</td>
			</tr>		
			<tr>
				<td>
					Highest Gender
				</td>
				<td>
					<input type="text" value="<?php echo social_media_analytics_get_meta('tw_highest_gender'); ?>" placeholder="Highest Gender" name="tw_highest_gender" />
				</td>
			</tr>
			<tr>
				<td>
					Highest Age Range (Lower)
				</td>
				<td>
					<input type="text" value="<?php echo social_media_analytics_get_meta('tw_lowest_gender_age'); ?>" placeholder="Lowest Age" name="tw_lowest_gender_age" />
				</td>
			</tr>
			<tr>
				<td>
					Highest Age Range (Higher)
				</td>
				<td>
					<input type="text" value="<?php echo social_media_analytics_get_meta('tw_higest_gender_age'); ?>" placeholder="Highest Age" name="tw_higest_gender_age" />
				</td>
			</tr>
		</table>
</div>
<div class="smt_total_subscribers">
	<div class="smt_total_sub_inner">
		Total : <span class="total-sub-s"><?php 
		$totalnum = 0;
		if(social_media_analytics_get_meta( 'social_media_analytics_facebook' ) === 'facebook')
		$totalnum = $totalnum + smt_get_facebook_page_likes(basename(get_post_meta($post->ID,'social_media_analytics_fb_media_account_link',true)));

		if(social_media_analytics_get_meta( 'social_media_analytics_instagram' ) === 'instagram')
		$totalnum = $totalnum + smt_get_insta_follower(basename(get_post_meta($post->ID,'social_media_analytics_ig_media_account_link',true)));

		if(social_media_analytics_get_meta( 'social_media_analytics_youtube' ) === 'youtube')
		$totalnum = $totalnum + smt_youtube_get_number_of_subscribers('AIzaSyA2q0T0Sr9sW7RBdOIP9rCw0x19KVOMWxI',basename(get_post_meta($post->ID,'social_media_analytics_yt_media_account_link',true)));

		if(social_media_analytics_get_meta( 'social_media_analytics_twitter' ) === 'twitter')
		$totalnum = $totalnum + smt_get_twitter_follower( basename(get_post_meta($post->ID,'social_media_analytics_tw_media_account_link',true)) );
		 ?>
		 <strong>
		 <?php 
		 echo $totalnum;
		 ?>	
		 <input type="hidden" name="smt_total_followers" value="<?php echo $totalnum; ?>">
		 </strong>
		 </span>
	</div>
</div>
<?php 
?>
	<?php
}

function social_media_stats_save( $post_id ) {
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
	if ( ! isset( $_POST['social_media_stats_nonce'] ) || ! wp_verify_nonce( $_POST['social_media_stats_nonce'], '_social_media_stats_nonce' ) ) return;
	if ( ! current_user_can( 'edit_post', $post_id ) ) return;
	if ( isset( $_POST['social_media_stats_top'] ) )
		update_post_meta( $post_id, 'social_media_stats_top', esc_attr( $_POST['social_media_stats_top'] ) );
	if ( isset( $_POST['yt_gender'] ) )
		update_post_meta( $post_id, 'yt_gender', esc_attr( $_POST['yt_gender'] ) );
	if ( isset( $_POST['yt_age'] ) )
		update_post_meta( $post_id, 'yt_age', esc_attr( $_POST['yt_age'] ) );
	if ( isset( $_POST['age_gender'] ) )
		update_post_meta( $post_id, 'age_gender', esc_attr( $_POST['age_gender'] ) );
	if ( isset( $_POST['male_female'] ) )
		update_post_meta( $post_id, 'male_female', esc_attr( $_POST['male_female'] ) );
	if ( isset( $_POST['ig_gender'] ) )
		update_post_meta( $post_id, 'ig_gender', esc_attr( $_POST['ig_gender'] ) );
	if ( isset( $_POST['ig_age'] ) )
		update_post_meta( $post_id, 'ig_age', esc_attr( $_POST['ig_age'] ) );
	if ( isset( $_POST['ig_followers_man'] ) )
		update_post_meta( $post_id, 'ig_followers_man', esc_attr( $_POST['ig_followers_man'] ) );
	if ( isset( $_POST['tw_gender'] ) )
		update_post_meta( $post_id, 'tw_gender', esc_attr( $_POST['tw_gender'] ) );
	if ( isset( $_POST['tw_age'] ) )
		update_post_meta( $post_id, 'tw_age', esc_attr( $_POST['tw_age'] ) );
	if ( isset( $_POST['yt_gender_man'] ) )
		update_post_meta( $post_id, 'yt_gender_man', esc_attr( $_POST['yt_gender_man'] ) );
	if ( isset( $_POST['yt_age_man'] ) )
		update_post_meta( $post_id, 'yt_age_man', esc_attr( $_POST['yt_age_man'] ) );

	// FB
	if ( isset( $_POST['fb_total_likes'] ) )
	update_post_meta($post_id,'fb_total_likes',$_POST['fb_total_likes']);

	if ( isset( $_POST['fb_highest_gender'] ) )
	update_post_meta($post_id,'fb_highest_gender',$_POST['fb_highest_gender']);

	if ( isset( $_POST['fb_higest_gender_age'] ) )
	update_post_meta($post_id,'fb_higest_gender_age',$_POST['fb_higest_gender_age']);

	if ( isset( $_POST['fb_lowest_gender_age'] ) )
	update_post_meta($post_id,'fb_lowest_gender_age',$_POST['fb_lowest_gender_age']);

	if ( isset( $_POST['fb_per_male'] ) )
	update_post_meta($post_id,'fb_per_male',$_POST['fb_per_male']);

	if ( isset( $_POST['fb_pe_female'] ) )
	update_post_meta($post_id,'fb_pe_female',$_POST['fb_pe_female']);


	
	// IG
	if ( isset( $_POST['ig_total_likes'] ) )
	update_post_meta($post_id,'ig_total_likes',$_POST['ig_total_likes']);

	if ( isset( $_POST['ig_highest_gender'] ) )
	update_post_meta($post_id,'ig_highest_gender',$_POST['ig_highest_gender']);

	if ( isset( $_POST['ig_higest_gender_age'] ) )
	update_post_meta($post_id,'ig_higest_gender_age',$_POST['ig_higest_gender_age']);

	if ( isset( $_POST['ig_lowest_gender_age'] ) )
	update_post_meta($post_id,'ig_lowest_gender_age',$_POST['ig_lowest_gender_age']);

	if ( isset( $_POST['ig_per_male'] ) )
	update_post_meta($post_id,'ig_per_male',$_POST['ig_per_male']);

	if ( isset( $_POST['ig_per_female'] ) )
	update_post_meta($post_id,'ig_per_female',$_POST['ig_per_female']);

	// YT
	if ( isset( $_POST['yt_total_likes'] ) )
	update_post_meta($post_id,'yt_total_likes',$_POST['yt_total_likes']);

	if ( isset( $_POST['yt_highest_gender'] ) )
	update_post_meta($post_id,'yt_highest_gender',$_POST['yt_highest_gender']);

	if ( isset( $_POST['yt_higest_gender_age'] ) )
	update_post_meta($post_id,'yt_higest_gender_age',$_POST['yt_higest_gender_age']);

	if ( isset( $_POST['yt_lowest_gender_age'] ) )
	update_post_meta($post_id,'yt_lowest_gender_age',$_POST['yt_lowest_gender_age']);

	if ( isset( $_POST['yt_per_male'] ) )
	update_post_meta($post_id,'yt_per_male',$_POST['yt_per_male']);

	if ( isset( $_POST['yt_per_female'] ) )
	update_post_meta($post_id,'yt_per_female',$_POST['yt_per_female']);

	//TW
	if ( isset( $_POST['tw_total_likes'] ) )
		update_post_meta( $post_id, 'tw_total_likes', esc_attr( $_POST['tw_total_likes'] ) );
	if ( isset( $_POST['tw_highest_gender'] ) )
		update_post_meta( $post_id, 'tw_highest_gender', esc_attr( $_POST['tw_highest_gender'] ) );
	if ( isset( $_POST['tw_higest_gender_age'] ) )
		update_post_meta( $post_id, 'tw_higest_gender_age', esc_attr( $_POST['tw_higest_gender_age'] ) );
	if ( isset( $_POST['tw_lowest_gender_age'] ) )
		update_post_meta( $post_id, 'tw_lowest_gender_age', esc_attr( $_POST['tw_lowest_gender_age'] ) );
	
}
add_action( 'save_post', 'social_media_stats_save' );

/*
	Usage: social_media_stats_get_meta( 'social_media_stats_top' )
*/

?>