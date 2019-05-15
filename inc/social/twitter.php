<?php
function smt_get_twitter_follower($username){
	$options = get_option( 'sma_settings' );
	/** Set access tokens here - see: https://dev.twitter.com/apps/ **/
	$settings = array(
	'oauth_access_token' => "2493274604-QAVp0D4mwz1LlwKKNprV3ps4haO7NjO9nLASo5Q",
	'oauth_access_token_secret' => "FHchZAHjhOKM6mRdg5vfjKviykOuaYn5lzhztYMGiPdmh",
	'consumer_key' => "tWQpMNlgDhoOS7VwFKzDevp5M",
	'consumer_secret' => "bHWUHYrQlPyMQDB595YOrpisx6vBuufbH1wy1RaZOhSB3QvOgn"
	);

	$ta_url = 'https://api.twitter.com/1.1/statuses/user_timeline.json';
	$getfield = '?screen_name='.$username;
	$requestMethod = 'GET';
	$twitter = new TwitterAPIExchange($settings);
	$follow_count=$twitter->setGetfield($getfield)->buildOauth($ta_url, $requestMethod)->performRequest();
	$data = json_decode($follow_count, true);
	$followers_count=$data[0]['user']['followers_count'];
	return $followers_count;
}


function smt_get_twitter_profile_url($username){
	$getuser_url = 'https://twitter.com/'.$username.'/profile_image?size=original';
	return $getuser_url;
}