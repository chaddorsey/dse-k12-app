<?php

header("Access-Control-Allow-Origin: *");

require_once('_incl/db-connect.php');

$column_names = [];
$column_query = "SHOW COLUMNS FROM responses";
$column_result = $dbh->query($column_query);
while ($row = $column_result->fetch_object()) {
  if (!($row->Field == 'id') && !($row->Field == 'name')) {
    $column_names[] = $row->Field;
  }
}

$output = '[';
foreach ($column_names as $column_name) {
  if ($column_name == 'email') { // FIX THIS - change column name in DB
    $output .= "\"email_address\",";
  } else {
    $output .= "\"$column_name\",";
  }
}
$output = preg_replace("/,$/", ']', $output);

$response_query = "SELECT * FROM responses";
$response_result = $dbh->query($response_query);

while ($response = $response_result->fetch_object()) {
  $output .= ", [";
  foreach ($column_names as $column_name) {
    $output .= '"' . $response->$column_name . '",';
  }
  $output = preg_replace("/,$/", ']', $output);
}

echo "[$output]";

?>
