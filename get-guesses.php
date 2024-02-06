<?php

header('Access-Control-Allow-Origin: *');

require_once('_incl/db-connect.php');

$email = strip_tags($_REQUEST['e']);
$output = '';
$guess_query = "SELECT g.*, q.label FROM guesses AS g JOIN users AS u ON g.uid1=u.id JOIN questions AS q ON q.id=g.qid WHERE u.email='$email'";
$guess_result = $dbh->query($guess_query);

while ($guess = $guess_result->fetch_object()) {
  $qlabel = $guess->label;
  $correct = $guess->correct;
  $uid = $guess->uid2;
  $output .= '{"' . $qlabel . '":"' . $correct . '","uid":"' . $uid . '"},';
}

$output = preg_replace("/,$/", '', $output);
echo '[' . $output . ']';

?>
