<?php

header('Access-Control-Allow-Origin: *');

require_once('_incl/db-connect.php');

$user_email = strip_tags($_REQUEST['e']);
$recognized_attendees = json_decode(strip_tags($_REQUEST['r']));
$talked_to_attendees = json_decode(strip_tags($_REQUEST['t']));
$known_well_attendees = json_decode(strip_tags($_REQUEST['k']));
$date_time = date("Y-m-d H:i:s");

// get user1 ID
$user1_id = getUserID($user_email);

foreach ($recognized_attendees as $attendee) {
  $user2_email = $attendee->email;
  $user2_id = getUserID($user2_email);
  submitRelData($user1_id, $user2_id, '1', $date_time);
}

foreach ($talked_to_attendees as $attendee) {
  $user2_email = $attendee->email;
  $user2_id = getUserID($user2_email);
  submitRelData($user1_id, $user2_id, '2', $date_time);
}

foreach ($known_well_attendees as $attendee) {
  $user2_email = $attendee->email;
  $user2_id = getUserID($user2_email);
  submitRelData($user1_id, $user2_id, '3', $date_time);
}

echo 'success ';
exit;

function getUserID($email) {
  global $dbh;
  $user_query = "SELECT id FROM users WHERE email='$email'";
  $user_result = $dbh->query($user_query);
  while ($user = $user_result->fetch_object()) {
    $uid = $user->id;
  }
  return $uid;
}

function submitRelData($uid1, $uid2, $type, $date_time) {
  global $dbh;

  // check if user has already indicated a relationship with the target attendee
  //$exists_query = "SELECT id FROM relationships WHERE uid1='$uid1' AND uid2='$uid2'";
  //$exists_result = $dbh->query($exists_query);

  // add data to database
  //if ($exists_result->num_rows > 0) {
  //  $rec_query = "UPDATE relationships SET type='$type' WHERE uid1='$uid1' AND uid2='$uid2'";
  //} else {
    $rec_query = "INSERT INTO relationships (uid1, uid2, type, timestamp) values('$uid1','$uid2','$type','$date_time')";
  //}
  $dbh->query($rec_query) or die($dbh->error);
}

?>
