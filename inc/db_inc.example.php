<?php

$dbhost = '';
$dbuser = '';
$dbpass = '';
$mysqli = new mysqli($dbhost, $dbuser, $dbpass);
$mysqli->set_charset("utf8");

if ($mysqli->connect_errno) {
    printf("Connect failed: %s<br />", $mysqli->connect_error);
    exit();
}
