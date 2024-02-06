<?php

error_reporting(0);

// Add allow origin header to enable AJAX call from Google script. make sure this domain will always be the same. use wildcard?
header('Access-Control-Allow-Origin: *', false);

// connect to the database
require_once('_incl/db-connect.php');

// get submitted data -- need to make this dynamically determine which question is being asked
// get email, question label, response value
$their_hash = strip_tags($_REQUEST['h']);
$user_email = strip_tags($_REQUEST['ue']);
$attendee_email = strip_tags($_REQUEST['ae']);
$question = strip_tags($_REQUEST['q']);
$guess = strip_tags(mysqli_real_escape_string($dbh, $_REQUEST['g']));
$date_time = date("Y-m-d H:i:s");
$correct = 0;

if ($user_email === '' || $attendee_email === '' || $question === '' || $guess === '') {
  echo "error: $user_email $attendee_email $question $qtype $guess";
  exit;
}

// get user id of guesser
$uid1 = getUserID($user_email);

// get user id of attendee being guessed about
$uid2 = getUserID($attendee_email);

// get question id
$qid = getQuestionID($question);

// check hash value to make sure this is a valid request
//$secret_word = '%-3dKC45^DbHDw490+aeMcxLP#!153mNcB';
//$my_hash = md5("$email:$secret_word");
//if ($my_hash == $their_hash) {

//}

// get answer to question submitted by attendee
$answer_query = "SELECT $question FROM responses WHERE uid='$uid2'";
$answer_result = $dbh->query($answer_query);
while ($answer_row = $answer_result->fetch_object()) {
  $answer = $answer_row->$question;
}

// check if guess is correct
if (mysqli_real_escape_string($dbh, $answer) === $guess) {
  $correct = 1;
}

// record guess data to DB
$guess_query = "INSERT INTO guesses (uid1, uid2, qid, guess, correct, timestamp) VALUES('$uid1', '$uid2', '$qid', '$guess', '$correct', '$date_time')";
$query_result = $dbh->query($guess_query) or die($dbh->error);

// return correct value
echo $correct;
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

function getQuestionID($label) {
  global $dbh;
  $question_query = "SELECT id FROM questions WHERE label='$label'";
  $question_result = $dbh->query($question_query);
  while ($question = $question_result->fetch_object()) {
    $qid = $question->id;
  }
  return $qid;
}

?>
