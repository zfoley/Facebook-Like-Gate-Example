<?php

require 'facebook.php';

$app_id = "YOUR APP ID";
$app_secret = "YOUR APP SECRET";
$loginNextPage = 'YOUR FAN PAGE URL'.'?sk=app_'.$app_id;

$facebook = new Facebook(array(
        'appId' => $app_id,
        'secret' => $app_secret,
        'cookie' => true
));



$signed_request = $facebook->getSignedRequest();

$page_id = $signed_request["page"]["id"];
$like_status = $signed_request["page"]["liked"];


if ($like_status) {
	// FOR FANS
	$session = $facebook->getSession();
    $loginUrl = $facebook->getLoginUrl(
            array(
            'canvas'    => 1,
            'fbconnect' => 0,
			'next' => $loginNextPage,
            'req_perms' => 'publish_stream,photo_upload,user_photos,user_photo_video_tags'			
            )
    );

    $fbme = null;
	
	if (!$session) {
		echo "<script type='text/javascript'>top.location.href = '$loginUrl';</script>";	
        exit;	
	}
	else {
		
		try {
            $access_token = $facebook->getAccessToken();
            $fbme = $facebook->api('/me');
			$user = $facebook->getUser();
			
			$url = "https://graph.facebook.com/".$user;
			$info = file_get_contents($url);
			$info = json_decode($info);	
			$vars = "id=$user&first_name=$info->first_name&last_name=$info->last_name&access_token=$access_token&pathToServer=$pathToServer&appName=$appName";

        } catch (FacebookApiException $e) {
            echo "<script type='text/javascript'>top.location.href = '$loginUrl';</script>";
            exit;
        }
		
		
// Begin Like Gated Content.		
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">	
<html xmlns="http://www.w3.org/1999/xhtml" xmlns:fb="http://www.facebook.com/2008/fbml">
<head>
</head>
<body>
<h1>You Have Liked The Page</h1>
	</body>
</html>
<?
	}	
}
else {
	// FOR NON FANS
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">	
<html xmlns="http://www.w3.org/1999/xhtml" xmlns:fb="http://www.facebook.com/2008/fbml">
<head>
  </head>
  <body>
  <h1>Click Like To View Content</h1>
	</body>
</html>
<?
}
?>