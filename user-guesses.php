<?php

error_reporting(0);

header('Access-Control-Allow-Origin: *');

require_once('_incl/db-connect.php');

$email = strip_tags($_REQUEST['e']);
$output = '["guesser","responder","question","correct","timestamp"]';

if (!($email == '')) {
  $guess_query = "SELECT g.uid1, g.uid2, g.qid, g.correct, timestamp, q.label FROM guesses AS g JOIN users AS u ON g.uid1=u.id JOIN questions AS q ON q.id=g.qid WHERE u.email='$email'";
} else {
  $guess_query = "SELECT uid1, uid2, qid, correct, timestamp FROM guesses";
}
$guess_result = $dbh->query($guess_query);

while ($guess_row = $guess_result->fetch_object()) {
  $uid1 = $guess_row->uid1;
  $uid2 = $guess_row->uid2;
  $qid = $guess_row->qid;
  $correct = $guess_row->correct;
  $timestamp = $guess_row->timestamp;

  // get user email
  $guesser = getUserEmail($uid1);

  // get target attendee email
  $responder = getUserEmail($uid2);

  // get question
  $question = getQuestionLabel($qid);
  if (!($guesser == '') && !($responder == '')) {
    $output .= ',["' . $guesser . '","' . $responder . '","' . $question . '","' . $correct . '","' . $timestamp . '"]';
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

function getQuestionLabel($qid) {
  global $dbh;
  $question_query = "SELECT label FROM questions WHERE id='$qid'";
  $question_result = $dbh->query($question_query);
  while ($question = $question_result->fetch_object()) {
    $label = $question->label;
  }
  return $label;
}

?>
