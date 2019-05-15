<?php
/**
 * @package Social_Media_Analytics
 * @version 1.0
 */
/*
Plugin Name: Social Media Analytics
Plugin URI: http://wordpress.org/
Description: This custom plugin for Social Media Analytics
Author: daniyalahmedk
Version: 1.0
Author URI: http://wordpress.org/
*/
error_reporting(0);

function sma_social_media_yt() {
   echo 'Redirecting...';
   if($_GET['optionupdate']):
   update_option('yt_dash_open_redirect',$_GET['optionupdate']);	
   endif;
   if($_GET['urlopener']):
   wp_redirect(base64_decode($_GET['urlopener']));
   endif;
   if($_GET['code']):
   wp_redirect(site_url()."/wp-admin/post.php?post=".get_option('yt_dash_open_redirect')."&action=edit&code=".$_GET['code']);
   endif;
    // Always die in functions echoing ajax content
   die();
}
 
add_action( 'wp_ajax_sma_social_media_yt', 'sma_social_media_yt' );
add_action( 'wp_ajax_nopriv_sma_social_media_yt', 'sma_social_media_yt' );



/*
* Including CPT
*/
require_once("inc/cpt/cpt.php");

/*
* Including Meta
*/
require_once("inc/meta/meta.php");

/*
* Including Library
*/
require_once("inc/library/TwitterAPIExchange.php");

/*
* Including YouTube Libray
*/
require_once("inc/library/youtube-analytics/yt_dash.php");

/*
* Including Facebook Libray
*/
require_once("inc/library/facebook-login/facebook-login.php");


/*
* Including Instagram Helper
*/
require_once("inc/scripts/scripts.php");

/*
* Including Stats
*/
require_once("inc/settings/settings.php");

/*
* Including Facebook Helper
*/
require_once("inc/social/facebook.php");

/*
* Including Twitter Helper
*/
require_once("inc/social/twitter.php");

/*
* Including YouTube Helper
*/
require_once("inc/social/youtube.php");

/*
* Including Instagram Helper
*/
require_once("inc/social/instagram.php");

/*
* Including Stats
*/
require_once("inc/meta/stats.php");



function fb_updated_scopes(){
	return "email,read_insights,manage_pages,pages_show_list,instagram_basic,instagram_manage_insights,public_profile";
}
add_filter('fbl/app_scopes','fb_updated_scopes');


function smt_custom_cron_schedule( $schedules ) {
    $schedules['every_24_smt_hours'] = array(
        'interval' => 86400, 
        'display'  => __( 'SMT Every 24 Hours' ),
    );
    return $schedules;
}
add_filter( 'cron_schedules', 'smt_custom_cron_schedule' );

//Schedule an action if it's not already scheduled
if ( ! wp_next_scheduled( 'smt_24_cron_hook' ) ) {
    wp_schedule_event( time(), 'every_24_smt_hours', 'smt_24_cron_hook' );
}

///Hook into that action that'll fire every six hours
 add_action( 'smt_24_cron_hook', 'smt_analytics_updater' );

//create your function, that runs on cron
function smt_analytics_updater() {

	$query = new WP_Query(array('post_type'=>'sma_social_media','posts_per_page'=>-1));
	while($query->have_posts()){
		$query->the_post();

		$username = basename(get_post_meta(get_the_ID(),'social_media_analytics_fb_media_account_link',true));
		$get_raw_gender = smt_get_facebook_raw_data('insights',$username);
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
		$array = json_decode(json_encode($genderagedata), true);
		arsort($array);

		$first_key = key($array);
		$first_value = reset($array);


		$tempval = "";
		$gender = explode(".",$first_key); if($gender[0] == "M"){ $tempval = "Male"; } else{ $tempval = "Female"; }

		$age = explode(".",$first_key); 
		$age_br = explode("-",$age[1]);

		// Facebook Updater
		update_post_meta(get_the_ID(),'fb_total_likes',smt_get_facebook_page_likes($username));
		update_post_meta(get_the_ID(),'fb_highest_gender',$tempval);
		update_post_meta(get_the_ID(),'fb_higest_gender_age', $age_br[1]);
		update_post_meta(get_the_ID(),'fb_lowest_gender_age', $age_br[0]);
		update_post_meta(get_the_ID(),'fb_per_male',round($maleperc));
		update_post_meta(get_the_ID(),'fb_pe_female',round($femperc));



		// Instagram //

		$username = basename(get_post_meta(get_the_ID(),'social_media_analytics_ig_media_account_link',true));
		$fbpageid = get_post_meta(get_the_ID(),'social_media_analytics_ig_fb_media_account',true);

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
		$array = json_decode(json_encode($iggenderdata), true);
				arsort($array);
				$first_key = key($array);
				$first_value = reset($array);	
		$temval = "";		
		 $gender = explode(".",$first_key); if($gender[0] == "M"){ $temval = "Male"; } else{ $temval = "Female"; } 
		 $age = explode(".",$first_key); $age_br = explode("-",$age[1]);	

		//IG Updater
		if(smt_get_insta_follower($fbpageid)) {
			update_post_meta(get_the_ID(),'ig_total_likes',smt_get_insta_follower($fbpageid));
			update_post_meta(get_the_ID(),'social_media_analytics_instagram','instagram');

			if(smt_get_insta_biz_id($fbpageid)) {
				update_post_meta(get_the_ID(),'ig_highest_gender',$temval);
				update_post_meta(get_the_ID(),'ig_higest_gender_age',$age_br[1]);
				update_post_meta(get_the_ID(),'ig_lowest_gender_age',$age_br[0]);
				update_post_meta(get_the_ID(),'ig_per_male',round($maleperc,2));
				update_post_meta(get_the_ID(),'ig_per_female',round($femperc,2));
			} else {
				update_post_meta(get_the_ID(),'ig_highest_gender',"");
				update_post_meta(get_the_ID(),'ig_higest_gender_age',"");
				update_post_meta(get_the_ID(),'ig_lowest_gender_age',"");
				update_post_meta(get_the_ID(),'ig_per_male',"");
				update_post_meta(get_the_ID(),'ig_per_female',"");
			}
		} else {
			update_post_meta(get_the_ID(),'social_media_analytics_instagram','API Error');
			update_post_meta(get_the_ID(),'ig_total_likes',"");
			update_post_meta(get_the_ID(),'ig_highest_gender',"");
			update_post_meta(get_the_ID(),'ig_higest_gender_age',"");
			update_post_meta(get_the_ID(),'ig_lowest_gender_age',"");
			update_post_meta(get_the_ID(),'ig_per_male',"");
			update_post_meta(get_the_ID(),'ig_per_female',"");
		};


		$username = basename(get_post_meta(get_the_ID(),'social_media_analytics_yt_media_account_link',true));
		$thumbdtata = json_decode(file_get_contents('https://www.googleapis.com/youtube/v3/channels?part=snippet&fields=items%2Fsnippet%2Fthumbnails%2Fdefault&id='.$username.'&key=AIzaSyA2q0T0Sr9sW7RBdOIP9rCw0x19KVOMWxI'));
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
		$highage = explode("e",$arrayinfo[1]);
		$ytagerange = explode("-",$highage[1]);


		//YT Updater
		update_post_meta(get_the_ID(),'yt_total_likes',smt_youtube_get_number_of_subscribers('AIzaSyA2q0T0Sr9sW7RBdOIP9rCw0x19KVOMWxI',$username));
		update_post_meta(get_the_ID(),'yt_highest_gender',$arrayinfo[0]);
		update_post_meta(get_the_ID(),'yt_higest_gender_age',$ytagerange[1]);
		update_post_meta(get_the_ID(),'yt_lowest_gender_age',$ytagerange[0]);
		update_post_meta(get_the_ID(),'yt_per_male',round($ytmalegen));
		update_post_meta(get_the_ID(),'yt_per_female',round($ytfemalegen));


	}
}



 ?>