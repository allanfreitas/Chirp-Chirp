<!DOCTYPE html>

<html lang="en">

<head>
	<meta charset="utf-8" />
	<meta name="viewport" content="width=device-width; initial-scale=1.0; maximum-scale=1.0; user-scalable=0;"/>
	<link rel="stylesheet" href="<?= $base_url ?>css/styles.css" type="text/css" media="screen" charset="utf-8">
	<title>Tara's Tweet-o-Rama</title>
</head>

<body>

	<div id="container">

		<header>
			<h1>Tara's Twitter-o-Rama</h1>
		</header>
		
		<section>
		 	<h2>Timeline</h2>

			<?php
			echo '<h3>You: ' . $user_name . '(' . $user_screen_name . ').</h3>';

			// TIMELINE		
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

			// echo '<p><a href="?content=friends">Friends Timeline</a> | <a href="?content=me">My Timeline</a> | <a href="?clear">Sign Out</a></p>';
			if (!isset($_POST['tweet'])) {
				echo '<form id="post" method="post" action="index.php">';
				echo '    <input type="hidden" name="submission" value="true">';
				echo '    <textarea name="tweet" rows="8" cols="40" placeholder="Enter tweet text here." autofocus></textarea>';
				echo '    <div><input type="submit" value="Post"></div>';
				echo '</form>';
			} else {
		        $twitter->statuses->update($_POST['tweet']);
				echo '<p>You\'ve tweeted! Here\'s what you wrote: ' .  $_POST['tweet'] . '</p>';
			}
			?>
		</section>

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
