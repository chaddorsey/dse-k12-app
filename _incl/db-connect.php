<?php

$db_user = 'root'; // add user
$db_pw = 'root'; // add password
$db_name = 'dset-app-mysql2';
$db_host = 'localhost'; // add db host

$dbh = new mysqli($db_host, $db_user, $db_pw, $db_name);
if ($dbh->connect_error) {
  die('Connection failed: ' . $dbh->connect_error);
}

?>
