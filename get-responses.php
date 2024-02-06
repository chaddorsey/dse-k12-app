<?php

error_reporting(0);

header('Access-Control-Allow-Origin: *');

require_once('_incl/db-connect.php');

$column_names = [];
$column_query = "SHOW COLUMNS FROM responses";
$column_result = $dbh->query($column_query);
while ($row = $column_result->fetch_object()) {
  if (!($row->Field == 'id') && !($row->Field == 'name')) {
    $column_names[] = $row->Field;
  }
}

$email = strip_tags($_REQUEST['e']);
$guess = strip_tags($_REQUEST['g']);
$output = '';
$response_query = "SELECT r.* FROM responses AS r JOIN users AS u ON r.uid=u.id WHERE u.email='$email'";
$response_result = $dbh->query($response_query);

while ($response = $response_result->fetch_object()) {
  foreach ($column_names as $column_name) {
    $qtype_query = "SELECT type FROM questions WHERE label='$column_name'";
    $qtype_result = $dbh->query($qtype_query) or die($dbh->error);
    $qtype_value = $qtype_result->fetch_object();
    $qtype = $qtype_value->type;
    if ($qtype === 'OT' || $qtype == 'ON') {
      $response_count = 0;
      $response_count_query = "SELECT DISTINCT $column_name FROM responses WHERE $column_name<>''";
      $response_count_result = $dbh->query($response_count_query) or die ($dbh->error);
      while ($row = $response_count_result->fetch_object()) {
        $response_count++;
      }
      if ($response_count > 2 || $g == '') {
        $output .= '"' . $column_name . '":"' . $response->$column_name . '",';
      }
    } else {
      $output .= '"' . $column_name . '":"' . $response->$column_name . '",';
    }
  }
}

if ($output == '') {
  $column_query = "SHOW COLUMNS FROM responses";
  $column_result = $dbh->query($column_query);
  while ($row = $column_result->fetch_object()) {
    if (!($row->Field == 'id') && !($row->Field == 'uid')) {
      $column_names[] = $row->Field;
    }
  }
  foreach ($column_names as $column_name) {
    $output .= "\"$column_name\":\"\",";
  }
}

$output = preg_replace("/,$/", '', $output);
echo '{' . $output . '}';

?>
