<?php

require_once 'CAS/bootstrap.php';

$settings = array();
// set the cookie with their netid and the treatment
$settings['netid'] = phpCAS::getUser();
$settings['treatment'] = (bool) rand(0,1);
$settings['pseudo_session_id'] = uniqid();
$settings['experiment_phase'] = 2; // 2 is when we encourage them to break the glass
$settings['allowed'] = array();

// expires in 2 hours
setcookie('byu_behaviorallab_study', json_encode($settings), time()+60*60*2, '/', '.byu.edu');
$attributes = phpCAS::getAttributes();
?>
<html>
<head>
	<title>MBRL Study - Site Comparisons</title>
	<!--link rel="stylesheet" href="http://twitter.github.com/bootstrap/1.4.0/bootstrap.min.css"-->
	<link rel="stylesheet" href="/bootstrap.min.css">
	<style type="text/css">
		body { 
			background: #f0f9ff url(bg.png) top center repeat-y;
		}
		#main-container {
			width: 100%;
			height: 100%;
		}
		p { font-size: 120%; }
		#images {
			margin-bottom: 200px;
		}
		#images img { border: 1px solid #000; }
	</style>
</head>

<body>
	<div id="main-container">
		<div class="container">
			<div class="row" style="margin-top:60px;">
				<div class="span16">
					<h1 class="page-header" style="text-align:center;border-bottom:none;">Judgements in Web-Based Tasks</h1>
					<h1 class="page-header" style="margin-bottom: 40px;">Welcome to the study, <?=$attributes['name']?>!</h1>
				</div>
			</div>

			<div class="row">
				<div class="span5">
					<div id="participant_information">
						<h2>Personal Info:</h2>
						<br /><p><strong>For verification purposes, you are logged in as:</strong><br />
						Name: <em><?=$attributes['name']?></em><br />
						NetID: <em><?=phpCAS::getUser()?></em><br />
						BYU Photo: <br />
					    <img src="https://gamma.byu.edu/ry/ae/prod/person/cgi/personPhoto.cgi/" height="150px" width="150px" /></p>
					</div>
				</div>

				<div class="offset1 span10">
					<div id="instructions">
					    <h2>Instructions:</h2>
					    <br /><p>On the next page, you will be presented with two images that are screenshots of the same web page, but with different color schemes applied.</p>
					    <p>After evaluating both images, please click on the one that you find more personally appealing, or click on the button below the image to indicate your preference.</p><br />
					    <div style="text-align:right;">
					        <input type="button" onclick="window.location.href='study'" value="Begin &rarr;" class="btn info" />
					    </div>
					</div>
				</div>
			</div>
		</div>
	</div>
</body>
</html>
