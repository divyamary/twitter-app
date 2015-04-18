<?php
session_start();
include_once("config.php");
require_once "autoload.php";
use Abraham\TwitterOAuth\TwitterOAuth;

if (isset($_REQUEST['oauth_token']) && $_SESSION['oauth_token'] !== $_REQUEST['oauth_token']) {

	// if token is old, distroy any session and redirect user to index.php
	session_destroy();
	header('Location: ./index.php');
	
}elseif(isset($_REQUEST['oauth_token']) && $_SESSION['oauth_token'] == $_REQUEST['oauth_token']) {

	/** everything looks good, request access token
	successful response returns oauth_token, oauth_token_secret, user_id, and screen_name*/
	$connection = new TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET, $_SESSION['oauth_token'], $_SESSION['oauth_token_secret']);
	$access_token = $connection->oauth("oauth/access_token", array("oauth_verifier" => $_REQUEST['oauth_verifier']));
	$_SESSION['status'] = 'verified';
	$_SESSION['request_vars'] = $access_token;	
	// unset no longer needed request tokens
	unset($_SESSION['oauth_token']);
	unset($_SESSION['oauth_token_secret']);
		header('Location: ./index.php');
}else{
	if(isset($_GET["denied"]))
	{
		header('Location: ./index.php');
		die();
	}
	//fresh authentication
	$connection = new TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET);
	$request_token = $connection->oauth('oauth/request_token', array('oauth_callback' => OAUTH_CALLBACK));	
	//received token info from twitter
	$_SESSION['oauth_token'] = $request_token['oauth_token'];
	$_SESSION['oauth_token_secret'] = $request_token['oauth_token_secret'];
	//redirect user to twitter
	$twitter_url = $connection->url('oauth/authorize', array('oauth_token' => $request_token['oauth_token']));
	header('Location: ' . $twitter_url); 
}
?>

