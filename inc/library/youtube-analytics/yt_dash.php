<?php
/* 
Plugin Name: YouTube Analytics Dashboard
Plugin URI: https://deconf.com
Description: This plugin will display YouTube Analytics data and statistics into Admin Dashboard. 
Author: Alin Marcu
Version: 1.1.1
Author URI: https://deconf.com
*/  

function yt_dash_admin() {  
    include('yt_dash_admin.php');  
} 
	

$plugin = plugin_basename(__FILE__);

add_action('wp_dashboard_setup', 'yt_dash_setup');
add_action('admin_menu', 'yt_dash_admin_actions'); 
add_action('admin_enqueue_scripts', 'yt_dash_admin_enqueue_scripts');
add_action('plugins_loaded', 'yt_dash_init');
add_filter("plugin_action_links_$plugin", 'yt_dash_settings_link' );


function yt_dash_content($code,$postid) {
	
	require_once 'functions.php';

    require 'autoload.php';	
	ini_set('display_errors', 1);
	ini_set('display_startup_errors', 1);
	error_reporting(E_ALL);
	$sma_options = get_option( 'sma_settings' );

	$client = new Google_Client();
	$client->setAccessType('offline');
	$client->setApplicationName('YouTube Analytics Dashboard');
	$actual_link = site_url()."/wp-admin/admin-ajax.php?action=sma_social_media_yt";
	$client->setRedirectUri($actual_link);
	

	$client->setClientId(get_post_meta($postid,'social_media_analytics_authentication_yt_cid',true));
	$client->setClientSecret(get_post_meta($postid,'social_media_analytics_authentication_yt_cst',true));
	$client->setDeveloperKey(get_option('AIzaSyA2q0T0Sr9sW7RBdOIP9rCw0x19KVOMWxI'));

	$client->setScopes(array('https://www.googleapis.com/auth/yt-analytics.readonly','https://www.googleapis.com/auth/yt-analytics-monetary.readonly',"https://www.googleapis.com/auth/youtube", "https://www.googleapis.com/auth/youtube.readonly", "https://www.googleapis.com/auth/youtubepartner"));
	
	$service = new Google_Service_YouTubeAnalytics($client);		
		
		if ($code == 'get_url'){
			$authUrl = $client->createAuthUrl();
			return $authUrl;
		}		
		else{
			if ($code){
				$client->authenticate($code);
				return $client->getAccessToken();
			} else{
		
			}	
		}
}	
?>