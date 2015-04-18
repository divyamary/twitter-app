<?php
ini_set('display_startup_errors',1);
ini_set('display_errors',1);
error_reporting(-1);

session_start();
include_once("config.php");
require_once "autoload.php";
use Abraham\TwitterOAuth\TwitterOAuth;

if (isset($_REQUEST['oauth_token']) && $_SESSION['oauth_token'] !== $_REQUEST['oauth_token']) {

	// if token is old, distroy any session and redirect user to index.php
	session_destroy();
	header('Location: ./index.php');
	
}elseif(isset($_REQUEST['oauth_token']) && $_SESSION['oauth_token'] == $_REQUEST['oauth_token']) {

	// everything looks good, request access token
	//successful response returns oauth_token, oauth_token_secret, user_id, and screen_name
	//$connection = new TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET, $_SESSION['token'] , $_SESSION['token_secret']);
	$connection = new TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET, $_SESSION['oauth_token'], $_SESSION['oauth_token_secret']);
	//$access_token = $connection->getAccessToken($_REQUEST['oauth_verifier']);
	$access_token = $connection->oauth("oauth/access_token", array("oauth_verifier" => $_REQUEST['oauth_verifier']));
	//if($connection->httpCode=='200')
	//{
		//redirect user to twitter
		$_SESSION['status'] = 'verified';
		$_SESSION['request_vars'] = $access_token;
		
		
		// unset no longer needed request tokens
		unset($_SESSION['oauth_token']);
		unset($_SESSION['oauth_token_secret']);
		header('Location: ./index.php');
	//}else{
		//die("error, try again later!");
	//}
		
}else{

	if(isset($_GET["denied"]))
	{
		header('Location: ./index.php');
		die();
	}

	//fresh authentication
	$connection = new TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET);
	//$request_token = $connection->getRequestToken(OAUTH_CALLBACK);
	$request_token = $connection->oauth('oauth/request_token', array('oauth_callback' => OAUTH_CALLBACK));
	
	//received token info from twitter
	//$_SESSION['token'] 			= $request_token['oauth_token'];
	//$_SESSION['token_secret'] 	= $request_token['oauth_token_secret'];
	$_SESSION['oauth_token'] = $request_token['oauth_token'];
	$_SESSION['oauth_token_secret'] = $request_token['oauth_token_secret'];
	
	// any value other than 200 is failure, so continue only if http code is 200

		//redirect user to twitter
		//$twitter_url = $connection->getAuthorizeURL($request_token['oauth_token']);
		$twitter_url = $connection->url('oauth/authorize', array('oauth_token' => $request_token['oauth_token']));
		header('Location: ' . $twitter_url); 
	
}
?>

