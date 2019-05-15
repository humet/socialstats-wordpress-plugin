<?php 
/*
YouTube Get Number Of Subscribers
*/
function smt_youtube_get_number_of_subscribers($api_key,$channel_id){
	$api_response = @file_get_contents('https://www.googleapis.com/youtube/v3/channels?part=statistics&id='.$channel_id.'&fields=items/statistics/subscriberCount&key='.$api_key);
	$api_response_decoded = json_decode($api_response, true);
 	return $api_response_decoded['items'][0]['statistics']['subscriberCount'];
}

function smt_youtube_get_age_gender($access_token,$channel_id){
	$api_response = @file_get_contents('https://www.googleapis.com/youtube/analytics/v1/reports?metrics=viewerPercentage&dimensions=gender,ageGroup&end-date=2017-11-11&start-date=2005-11-11&ids=channel=='.$channel_id.'&access_token='.$access_token);
	return json_decode($api_response);	
}