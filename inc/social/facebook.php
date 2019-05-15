<?php

function smt_get_facebook_page_access_token($username,$access_token){
	$getfbpagetoken = @file_get_contents('https://graph.facebook.com/'.$username.'?fields=access_token&access_token='.$access_token);
	$getfbjson = json_decode($getfbpagetoken);
	return $getfbjson->access_token;
}

function smt_get_facebook_raw_data($type,$username){
$options = get_option( 'sma_settings' );
$access_token = $options['sma_text_field_0'];
$facebookpage_at = smt_get_facebook_page_access_token($username,$access_token);

try{
	if($type == "insights"){
	$getfbdata = @file_get_contents('https://graph.facebook.com/'.$username.'/insights/page_fans_gender_age/lifetime?access_token='.$facebookpage_at);
	}
	else{
	$getfbdata = @file_get_contents('https://graph.facebook.com/'.$username.'?access_token='.$facebookpage_at.'&fields=fan_count');
	}
	return json_decode($getfbdata);
}
	catch(Exception $ex){
		return 0;
	}
}

function smt_get_facebook_page_likes($username){
	$get_raw_likes = smt_get_facebook_raw_data('likes',$username);
	if($get_raw_likes){
		return $get_raw_likes->fan_count;
	}
	else{
		return 0;
	}
}

function smt_get_facebook_page_gender_data($username){
	$get_raw_gender = smt_get_facebook_raw_data('insights',$username);
	if($get_raw_gender){
		return $get_raw_gender->data[0]->values[0]->value;
	}
	else{
		return 0;
	}
}

function smt_get_facebook_page_profile_url($username){
	$options = get_option( 'sma_settings' );
	$access_token = $options['sma_text_field_0'];
	$facebookpage_at = smt_get_facebook_page_access_token($username,$access_token);
	$getfbprofile_url = @file_get_contents('https://graph.facebook.com/'.$username.'/picture?height=150&redirect=false&access_token='.$facebookpage_at);
	if($getfbprofile_url){
		return json_decode($getfbprofile_url);
	}
	else{
		return 0;
	}
}