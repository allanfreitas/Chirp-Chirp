<?php
//include './twitter.php';

// http://zuzara.com/blog/2010/01/20/complete-working-sample-for-services_twitter-oauth-in-php-pear/

if (isset($_GET['clear'])) { // sometimes you need to clear session, just access ?clear=1
    session_start();
    $_SESSION = array();
    session_destroy();
    $_SESSION['is_logged_in'] = false;
    die('session has been cleared.');
}

define('CONSUMER_KEY', 'Gv4dsLXvG7nWp6kLGJqjmg');
define('CONSUMER_SECRET', '1pQCLjPXjEUSSKpuNMnjZlYxfNYTgwwUb0Vlm1GlmTw');
define('CONTENT_PARAM', 'content');
define('USER_PARAM', 'user');

require_once 'Services/Twitter.php';
require_once 'HTTP.php';
require_once 'HTTP/OAuth/Consumer.php';

try {

    session_start();

	// not authorized -- signin link
    if (isset($_GET['signin'])) {

        $oauth = new HTTP_OAuth_Consumer(CONSUMER_KEY, CONSUMER_SECRET);
		// callback URL should be the same as that you registered at http://twitter.com/apps/new
        $oauth->getRequestToken('http://twitter.com/oauth/request_token', 'http://www.tmarchand.com/twitter/'); 

        $_SESSION['oauth_token']        = $oauth->getToken();
        $_SESSION['oauth_token_secret'] = $oauth->getTokenSecret();

        $url = $oauth->getAuthorizeUrl('http://twitter.com/oauth/authorize');

        HTTP::redirect($url);

    } else {

        $twitter = new Services_Twitter;

		// post-initial authorization

		// 1. check if twitter id already exists in your db, 
		//      a. if exists, update the record (just in case the token is changed - not 
		// sure if this will happen, e.g. when user revokes the permission since
		// last visit).
		//      b. If it doesn't exist, insert new record and mark user'
		// status as logged in(e.g. store twitter id somewhere (cookie, session))

	    if (isset($_SESSION['oauth_token'], $_SESSION['oauth_token_secret'], $_GET['oauth_verifier'])) {
	
	        $current_user_timeline = $twitter->statuses->user_timeline();
	        $current_user_name = $current_user_timeline[0]->user->name;
	        $current_user_screen_name = $current_user_timeline[0]->user->screen_name;
	
	        $oauth = new HTTP_OAuth_Consumer(CONSUMER_KEY, CONSUMER_SECRET, $_SESSION['oauth_token'], $_SESSION['oauth_token_secret']);
	        $oauth->getAccessToken('http://twitter.com/oauth/access_token', $_GET['oauth_verifier']);
	        $twitter->setOAuth($oauth);
	
	        $_SESSION['oauth_token2'] = $oauth->getToken();
	        $_SESSION['oauth_token_secret2'] = $oauth->getTokenSecret();
	
	   	    $_SESSION['is_logged_in'] = true;
			$timeline = $current_user_timeline;
		
		// already authorized
	    } else if (isset($_SESSION['oauth_token2'], $_SESSION['oauth_token_secret2'])) {
	
	        $current_user_timeline = $twitter->statuses->user_timeline();
	        $current_user_name = $current_user_timeline[0]->user->name;
	        $current_user_screen_name = $current_user_timeline[0]->user->screen_name;
	
	        $oauth = new HTTP_OAuth_Consumer(CONSUMER_KEY, CONSUMER_SECRET, $_SESSION['oauth_token2'], $_SESSION['oauth_token_secret2']);
	        $twitter->setOAuth($oauth);
	        	
			// content to display
			if (isset($_GET[CONTENT_PARAM])) {
				if ($_GET[CONTENT_PARAM] == 'friends') {
			        $timeline = $twitter->statuses->home_timeline();
				} else if ($_GET[CONTENT_PARAM] == 'me') {
					$timeline = $current_user_timeline;
				} else {
					$timeline = $twitter->users->show(array('user_id' => $_GET[CONTENT_PARAM]));
				}
			}
		
		// not authorized	
	    } else {
	    	
	    	$trends = $twitter->trends()->trends;
	    	
	    }
	}
    
} catch (Exception $e) {
    echo $e->getMessage();
}

?>

<!DOCTYPE html>

<html lang="en">

<head>
	<meta charset="utf-8" />
	<meta name="viewport" content="width=device-width; initial-scale=1.0; maximum-scale=1.0; user-scalable=0;"/>
	<link rel="stylesheet" href="styles.css" type="text/css" media="screen" charset="utf-8">
	<title>Tara's Tweet-o-Rama</title>
</head>

<body>

	<div id="container">

		<header>
			<h1>Tara's Twitter-o-Rama</h1>
		</header>
		
		<section>

			<?php
			echo $_SESSION['is_logged_in'];
			if ($_SESSION['is_logged_in'] == true) {
				echo '<h2>You: ' . $current_user_name . '(' . $current_user_screen_name . ').</h2>';
			}
			?>

			<?php
			// TIMELINE
			if (isset($timeline)) {
				echo '<ul class="tweets">';
				if (count($timeline) > 0) {
					foreach ($timeline as $timeline_obj) {
						$image = $timeline_obj->user->profile_image_url;
						$display_name = $timeline_obj->user->name;
						$tweet = $timeline_obj->text;
						$screen_name = $timeline_obj->user->screen_name;
						$date = $timeline_obj->created_at;
						echo '<li><img src="' . $image . '" height="48" width="48" alt="' . $display_name . '"><p>' . $tweet . '</p><p>By: <a href="?content=' . $screen_name . '">' . $display_name . '</a> on ' . $date . '</p></li>';
				  	}
				}
				echo '</ul>';
			}

			// TRENDS
			if (isset($trends)) {
				$current_time = (int) $twitter->trends->current()->as_of;
				echo '<h2>Trends (as of ' . date('D., M. j, Y', $current_time) . ' at ' . date('g:i A', $current_time) . ')</h2><ul class="trends">';
				if (count($trends) > 0) {

					foreach ($trends as $i => $value) {
						
						$trend_name = $trends[$i]->name;
						$trend_url = $trends[$i]->url;
						
						echo '<li><a href="' . $trend_url . '" target="_blank">' . $trend_name . '</a></li>';
				  	}
				}
				echo '</ul>';
			}
			?>
		</section>
					
		<?php
		if ($_SESSION['is_logged_in'] == true) {
			echo '<p><a href="?content=friends">Friends Timeline</a> | <a href="?content=me">My Timeline</a> | <a href="?clear">Sign Out</a></p>';
		} else {
			echo '<p><a href="?signin"><img src="twitter_sign_in.png" width="151" height="24" alt="Sign in with Twitter" border="0"></a></p>';
		}

		echo $_SESSION['is_logged_in'];

		if ($_SESSION['is_logged_in'] == true) {
			$tweet = $_POST['tweet'];
			if (!isset($tweet)) {
				echo '<form id="post" method="post" action="index.php">';
				echo '    <input type="hidden" name="submission" value="true">';
				echo '    <textarea name="tweet" rows="8" cols="40" placeholder="Enter tweet text here." autofocus></textarea>';
				echo '    <div><input type="submit" value="Post"></div>';
				echo '</form>';
			} else {
		        $twitter->statuses->update($tweet);
				echo '<p>You\'ve tweeted! Here\'s what you wrote: ' .  $tweet . '</p>';
			}
		}
		?>

		<footer>
			<small>By Tara Marchand</small>
		</footer>

	</div>
	
	<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.min.js" type="text/javascript" charset="utf-8"></script>
	<script type="text/javascript" charset="utf-8">
		function init() {			
		}
		$(document).ready(init);
	</script>
</body>
</html>
