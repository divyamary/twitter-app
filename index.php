<?php
ini_set('display_startup_errors',1);
ini_set('display_errors',1);
error_reporting(-1);

//start session
session_start();
//just simple session reset on logout click
if (isset($_GET['reset'])) {
if($_GET["reset"]==1)
{
	session_destroy();
	header('Location: ./index.php');
}
}
// Include config file and twitter PHP Library by Abraham Williams (abraham@abrah.am)
include_once("config.php");
require_once "autoload.php";
use Abraham\TwitterOAuth\TwitterOAuth;
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Twitter App</title>
  <meta name="description" content="">
  <meta name="author" content="">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="http://fonts.googleapis.com/css?family=Raleway:400,300,600" rel="stylesheet" type="text/css">
  <link href='http://fonts.googleapis.com/css?family=Fredericka+the+Great' rel='stylesheet' type='text/css'>
  <link rel="stylesheet" href="css/normalize.css">
  <link rel="stylesheet" href="css/skeleton.css">
  <link rel="stylesheet" href="css/twitter.css">
  <link rel="icon" type="image/x-icon" href="favicon.ico">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
  <script type="text/javascript" src="js/script.js"></script>
</head>
<body>
	
<h1>Favorite tweets from your twitter timeline</h1>	
<?php
if(isset($_SESSION['status']) && $_SESSION['status']=='verified') {
	//Success, redirected back from process.php with varified status.
	//retrive variables
	$screenname 		= $_SESSION['request_vars']['screen_name'];
	$twitterid 			= $_SESSION['request_vars']['user_id'];
	$oauth_token 		= $_SESSION['request_vars']['oauth_token'];
	$oauth_token_secret = $_SESSION['request_vars']['oauth_token_secret'];

	//Show welcome message
	echo '<div class="container welcome">';
	echo '<div class="row">';
	echo '<div class="three columns"><h5>@'.$screenname.'</h5></div><div class="nine columns right"><a class="button logout" href="index.php?reset=1">Logout</a></div>';
	echo '</div></div>';
	$connection = new TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET, $oauth_token, $oauth_token_secret);	
	$owntweets= $connection->get('statuses/home_timeline',array('count' => TWEET_LIMIT));
	
	usort($owntweets,'sort_by_fav');
	echo '<div class="container main">';
	echo '<div class="ten columns offset-by-three">';
	foreach($owntweets as $key => $value){
		$favoriteCount = $value->favorite_count;
		if($favoriteCount>100){
			$oembedTweet = $connection->get('statuses/oembed',array('id' => $value->id,'maxwidth'=>800));
			echo $oembedTweet->html;
		}
	}
	echo '</div></div>';
		
}else{
	//login button
	echo '<div class="container logincontainer">';
	echo '<div class="six columns offset-by-three login">';
	echo "<p>Sign in with twitter to see the most favorited tweets from your timeline.</p>";
	echo '<a class="button signin" href="process.php"><span>Sign in with Twitter</span></a>';
	echo '</div></div>';
}

function sort_by_fav($a, $b){
		if ($a->favorite_count == $b->favorite_count) {
        return 0;
		}
		return ($a->favorite_count > $b->favorite_count) ? -1 : 1;
    }

?>

</body>
</html>
