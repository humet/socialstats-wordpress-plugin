<?php

function smt_get_insta_follower($fbpageid){
    $options = get_option( 'sma_settings' );   
    $instauserid = smt_get_insta_user_id($fbpageid);
    $response = @file_get_contents("https://graph.facebook.com/" . $instauserid . "?fields=followed_by_count&access_token=".$options['sma_text_field_0']);

    if ($response !== false) {
        $data = json_decode($response);    
        return $data->followed_by_count;
    }

    return 0;
}

function smt_get_insta_profile_url($fbpageid){
    $options = get_option( 'sma_settings' );
    $instauserid = smt_get_insta_user_id($fbpageid); 
    $response = @file_get_contents("https://graph.facebook.com/" . $instauserid . "?fields=profile_pic&access_token=".$options['sma_text_field_0']);

    

    if ($response !== false) {
        $data = json_decode($response);
        return $data->profile_pic;
    }

    return 0;
}

function smt_get_insta_biz_id($fbpageid){
    $options = get_option( 'sma_settings' );
    $response = @file_get_contents("https://graph.facebook.com/" . $fbpageid . "?fields=instagram_business_account&access_token=".$options['sma_text_field_0']);
    $inbizid = json_decode($response);

    if($inbizid->instagram_business_account->id) {
        return $inbizid->instagram_business_account->id;
    }

    return false;
}

function smt_get_insta_user_id($fbpageid){
    $options = get_option( 'sma_settings' );
    $response = @file_get_contents("https://graph.facebook.com/" . $fbpageid . "?fields=access_token&access_token=".$options['sma_text_field_0']);
    $pagedata = json_decode($response);
    $pageaccesstoken = $pagedata->access_token;
    $response = @file_get_contents("https://graph.facebook.com/me/instagram_accounts?fields=id&access_token=".$pageaccesstoken);
    $inuserid_data = json_decode($response);
    $inuserid = $inuserid_data->data[0]->id;

    if($inuserid) {
        return $inuserid;
    }

    return false;
}

function smt_get_insta_demographics($fbpageid){
    $options = get_option( 'sma_settings' );    
    $instabizid = smt_get_insta_biz_id($fbpageid);
    $response = @file_get_contents("https://graph.facebook.com/".$instabizid."/insights/?metric=audience_gender_age&period=lifetime&access_token=".$options['sma_text_field_0']);
    $demographics_temp = json_decode($response);
    return $demographics_temp->data[0]->values[0]->value;
}
