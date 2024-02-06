<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

// Add allow origin header to enable AJAX call from Google script. make sure this domain will always be the same. use wildcard?
header('Access-Control-Allow-Origin: *', false);
header('Content-Type: application/json');
echo json_encode(['status' => 'success']);
//exit();

// connect to the database
require_once('_incl/db-connect.php');

// get submitted data -- need to make this dynamically determine which question is being asked
// get email, question label, response value
$their_hash = strip_tags($_REQUEST['h'] ?? '');
$email = strip_tags($_REQUEST['e']);
$qlabel = strip_tags($_REQUEST['ql']);
$qtype = strip_tags($_REQUEST['qt']);
$resp_val = strip_tags(mysqli_real_escape_string($dbh, $_REQUEST['rv']));

error_log("here");
error_log("Email: $email, QLabel: $qlabel, QType: $qtype, RespVal: $resp_val");

if ($email === '' || $qlabel === '' || $qtype === '' || $resp_val === '') {
  echo "error: $email $qlabel $qtype $resp_val";
  exit;
}

$user_id = getUserID($email);

// check hash value to make sure this is a valid request
//$secret_word = '%-3dKC45^DbHDw490+aeMcxLP#!153mNcB';
//$my_hash = md5("$email:$secret_word");
//if ($my_hash == $their_hash) {

//}

// check if question already has a column in responses table, if it doesn't add one
// number questions: ALTER TABLE responses ADD [column name] int(100) NULL;
// multiple choice or text question: ALTER TABLE responses ADD [column name] varchar(255) NOT NULL;
$col_check_query = "SHOW COLUMNS FROM responses LIKE '$qlabel'";
$col_check_result = $dbh->query($col_check_query) or die($dbh->error);
$col_exists = $col_check_result->num_rows ? TRUE : FALSE;
if (!$col_exists) {
   if ($qtype == 'N') {
     $add_col_query = "ALTER TABLE responses ADD $qlabel int(100) NULL";
   } else {
     $add_col_query = "ALTER TABLE responses ADD $qlabel varchar(255) NOT NULL";
   }
   $add_col_result = $dbh->query($add_col_query);
}

// check if user has already submitted responses
$exists_query = "SELECT id FROM responses WHERE uid='$user_id'";
$exists_result = $dbh->query($exists_query);

// add data to database
if ($exists_result->num_rows > 0) {
  $submit_query = "UPDATE responses SET $qlabel='$resp_val' WHERE uid='$user_id'";
} else {
  $submit_query = "INSERT INTO responses (uid, $qlabel) VALUES('$user_id', '$resp_val')";
}

$submit_result = $dbh->query($submit_query) or die($dbh->error);

// let us know everything turned out OK
//echo 'success';

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
