<?php

error_reporting(0);

header('Access-Control-Allow-Origin: *');

require_once('_incl/db-connect.php');

$email = strip_tags($_REQUEST['e']);
$output = '["user1","user2","type","timestamp"]';
$rel_query = "SELECT uid1, uid2, type, timestamp FROM relationships";
$rel_result = $dbh->query($rel_query);

while ($rel = $rel_result->fetch_object()) {
  $user1 = getUserEmail($rel->uid1);
  $user2 = getUserEmail($rel->uid2);
  $type = $rel->type;
  $timestamp = $rel->timestamp;
  if (!($user1 == '') && !($user2 == '')) {
    $output .= ',["' . $user1 . '","' . $user2 . '","' . $type . '","' . $timestamp . '"]';
  }
}

$output = preg_replace("/,$/", '', $output);
echo '[' . $output . ']';
exit;

function getUserEmail($uid) {
  global $dbh;
  $user_query = "SELECT email FROM users WHERE id='$uid'";
  $user_result = $dbh->query($user_query);
  while ($user = $user_result->fetch_object()) {
    $email = $user->email;
  }
  return $email;
}

?>
