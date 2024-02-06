<?php

error_reporting(0);

header('Access-Control-Allow-Origin: *');

require_once('_incl/db-connect.php');

$code = strip_tags($_REQUEST['c']);

$user_query = "SELECT email, fname, lname FROM users WHERE code='$code'";
$user_result = $dbh->query($user_query);

while ($user_row = $user_result->fetch_object()) {
  $email_address = $user_row->email;
  $fname = $user_row->fname;
  $lname = $user_row->lname;
}

echo '{"email_address":"' . $email_address . '","fname":"' . $fname . '","lname":"' . $lname . '"}';
