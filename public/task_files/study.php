<?php
// error_reporting(E_ALL);

	$count = 0;
	$db_name = 'break_the_glass';
	$db_username = 'break_the_glass';
	$db_password = 'gi-thy-fuwh-zoz';

	$mysqli = new mysqli('localhost', $db_username, $db_password, $db_name);

	if (mysqli_connect_errno()) {
		error_log("DB connection failed: %s\n", mysqli_connect_error());
		echo 'Sorry, the study site is down! Please try again later.';
		exit();
	}

	// Process any existing values, if they have been sent
	if (isset($_GET['preferred'])) {
		$count     = (int) $_GET['count'] + 1;
		$netId     = $_COOKIE['byu_behaviorallab_study']['netid'];// $_GET['ni'];
		$img1      = (int) $_GET['img1'];
		$img2      = (int) $_GET['img2'];
		$preferred = (int) $_GET['preferred'];

		// Attempt to insert the data into the database
		$query = "INSERT INTO comparison_data (netId, image1, image2, preferred) VALUES (?, ?, ?, ?)";

		if ($stmt = $mysqli->prepare($query)) {
			$stmt->bind_param('ssss', $netId, $img1, $img2, $preferred);
			$stmt->execute();
			$stmt->close();
		} else {
			error_log(printf("Prepared statement error: %s\n", $mysqli->error));
		}
	}

	// Get new images from the DB to present to the user
	$imageQuery = "SELECT * FROM comparison_images ORDER BY rand() LIMIT 2";
	$imageResults = null;

	// Because we are pulling random records, there is the odd chance that we may pull two of the same. Prevent.
	do {
		if ($result = $mysqli->query($imageQuery)) {
		    while ($row = $result->fetch_assoc()) {
		        $imageResults[] = $row;
		    } 

		    //free result set
		    $result->close();
		}
	} while ($imageResults[0]['id'] == $imageResults[1]['id']);

	// Construct the query string
	$queryString = "/study.php?count=$count&img1=" . $imageResults[0]['id'] . '&img2=' . $imageResults[1]['id'] . '&preferred=';
?>

<!DOCTYPE html>

<html>
<head>
	<title>MBRL Study - Site Comparisons</title>
	<link rel="stylesheet" href="http://twitter.github.com/bootstrap/1.4.0/bootstrap.min.css">
	<style type="text/css">
		body { 
			background: #f0f9ff url(bg.png) top center repeat-y;
		}
		#main-container {
			
			width: 100%;
			height: 100%;
		}
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
					<h1 class="page-header" style="margin-bottom: 40px;">Compare these two pages</h1>

					<p>Below are two website designs. Click on the one that you find more visually pleasing.</p><br />
				</div>
			</div>

			<div class="row" id="images">
				<div class="span8">
					<h2>Site #1</h2>
					<a href="<?php echo $queryString . $imageResults[0]['id']; ?>"><img src="<?php echo $imageResults[0]['path']; ?>" class="span8" /></a>
					<a href="<?php echo $queryString . $imageResults[0]['id']; ?>" class="btn info" style="margin-left:170px;">I prefer Site #1</a>
				</div>
				<div class="span8">
					<h2>Site #2</h2>
					<a href="<?php echo $queryString . $imageResults[1]['id']; ?>"><img src="<?php echo $imageResults[1]['path']; ?>" class="span8" /></a>
					<a href="<?php echo $queryString . $imageResults[1]['id']; ?>" class="btn info" style="margin-left:170px;">I prefer Site #2</a>
				</div>
			</div>
		</div>
	</div>
</body>
</html>
