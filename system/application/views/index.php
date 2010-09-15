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

			<h2>Trends</h2>
			<ul class="trends">
			<?php
			//$trends_time
			foreach ($trends as $i => $value) {
				$trend_name = $trends[$i]->name;
				$trend_url = $trends[$i]->url;
				echo '<li><a href="' . $trend_url . '" target="_blank">' . $trend_name . '</a></li>';
		  	}
			?>
			</ul>
		</section>
		
		<section>
			<a href="<?= site_url('user') ?>"><img src="<?= base_url() ?>images/twitter_sign_in.png" alt="Sign in with Twitter" width="151" height="24" border="0"></a>
		</section>
		
		<footer>
			<div><small>By Tara Marchand</small></div>
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
