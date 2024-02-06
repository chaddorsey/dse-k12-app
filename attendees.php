<?php

error_reporting(0);

header('Access-Control-Allow-Origin: *');

require_once('_incl/db-connect.php');

$user_email = strip_tags($_REQUEST['e']);
$guess = strip_tags($_REQUEST['g']);
$attendee_email = strip_tags($_REQUEST['ae']);
$responses = '';
$attendees = '';

if (!($attendee_email == '') && !($guess == 1)) {
  $responses = getAttendeeResponses($attendee_email);
  echo $responses;
} else if ($guess == 1) {
  $uid1 = getUserID($user_email);
  $attendees = getFamiliarAttendeeList($uid1);
  echo $attendees;
} else {
  $attendees = getAttendeeList();
  echo $attendees;
}

exit;

function getAttendeeResponses($attendee_email) {
  global $dbh;

  $output = '';
  $response_query = "SELECT r.response FROM responses AS r JOIN users AS u ON u.id=r.uid WHERE u.email='$attendee_email'";
  $response_result = $dbh->query($response_query);
  while ($response = $response_result->fetch_object()) {
    $output .= '"' . $response->response . '",';
  }

  $output = preg_replace("/,$/", '', $output);
  return '{' . $output . '}';
}

function getAttendeeList() {
  global $dbh;
  $output = '';
  $attendees_query = 'SELECT email, fname, lname FROM users ORDER BY lname, fname ASC';
  $attendees_result = $dbh->query($attendees_query) or die($dbh->error);
  while ($row = $attendees_result->fetch_object()) {
    $email_address = $row->email;
    $fname = $row->fname;
    $lname = $row->lname;
    $output .= '{"email_address":"' . $email_address . '","name":"' . $fname . ' ' . $lname . '"},';
  }
  $output = preg_replace("/,$/", '', $output);
  return '[' . $output . ']';
}

function getFamiliarAttendeeList($uid1) {
  global $dbh;
  $output = '';
  $matched_already = [];
  // get relationship records limited to most recent record for any given relationship between user and specific attendees
  $rel_query = "SELECT uid2 FROM relationships WHERE uid1='$uid1' AND type='3' ORDER BY timestamp DESC";
  $rel_result = $dbh->query($rel_query) or die($dbh->error);
  while ($rel_row = $rel_result->fetch_object()) {
    $uid2 = $rel_row->uid2;
    if (!(in_array($uid2, $matched_already))) {
      $attendees_query = "SELECT u.id, u.email, u.fname, u.lname FROM users AS u JOIN responses AS resp ON resp.uid=u.id WHERE u.id='$uid2' ORDER BY u.lname, u.fname ASC";
      $attendees_result = $dbh->query($attendees_query) or die($dbh->error);
      while ($row = $attendees_result->fetch_object()) {
        $uid = $row->id;
        $email_address = $row->email;
        $fname = $row->fname;
        $lname = $row->lname;
        $output .= '{"email_address":"' . $email_address . '","name":"' . $fname . ' ' . $lname . '","uid":"' . $uid . '"},';
      }
      $matched_already[] = $uid2;
    }
  }
  // get other attendee's data

  $output = preg_replace("/,$/", '', $output);
  return '[' . $output . ']';
}

function getUserID($email) {
  global $dbh;
  $user_query = "SELECT id FROM users WHERE email='$email'";
  $user_result = $dbh->query($user_query);
  while ($user = $user_result->fetch_object()) {
    $uid = $user->id;
  }
  return $uid;
}

?>
