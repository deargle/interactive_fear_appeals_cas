<?php

/**
 * Grab a POST request, sanitize it, and log it to the DB
 **/

$db_name = 'break_the_glass';
$db_username = 'break_the_glass';
$db_password = 'gi-thy-fuwh-zoz';

$mysqli = new mysqli('localhost', $db_username, $db_password, $db_name);

if (mysqli_connect_errno()) {
	error_log("DB connection failed: %s\n", mysqli_connect_error());
	exit();
}

// Set of data we should be receiving from the extension
$netId         = $_POST['nid'];
$attemptedUrl  = $_POST['aurl'];
$promptGuid    = $_POST['pguid'];
$group         = $_POST['group'];
$state         = $_POST['state'];
$result        = $_POST['result'];
$justification = $_POST['just'];

//echo "NetID: $netId, Attempted URL: $attemptedUrl, Experimental Group: $group, State: $state, Result: $result, Justification: $justification";
//exit();

// Attempt to insert the data into the database
$query = "INSERT INTO results (netId, attemptedUrl, promptGuid, expGroup, state, result, justification, timestamp) VALUES (?, ?, ?, ?, ?, ?, ?, NOW())";

if ($stmt = $mysqli->prepare($query)) {
	$stmt->bind_param('sssssss', $netId, $attemptedUrl, $promptGuid, $group, $state, $result, $justification);
	$stmt->execute();
	$stmt->close();
} else {
	printf("Prepared statement error: %s\n", $mysqli->error);
}

// Should be done.

?>
