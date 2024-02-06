<?php

error_reporting(0);

header('Access-Control-Allow-Origin: *');

require_once('_incl/db-connect.php');

$qlabel = strip_tags($_REQUEST['q']);
$target_attendee = strip_tags($_REQUEST['ta']);
$output = '';
$questions = [];

if (!($qlabel == '')) {
  $question_query = "SELECT id, text, label, type, options FROM questions WHERE label='$qlabel'";
} else {
  $question_query = "SELECT id, text, label, type, options FROM questions";
}
$question_result = $dbh->query($question_query);

while ($question = $question_result->fetch_object()) {
  $qindex = $question->id;
  $qtext = $question->text;
  $qlabel = $question->label;
  $qtype = $question->type;
  $available_question = false;
  $qoptions_array = [];
  if (($qtype == 'OT' || $qtype == 'ON') && !($target_attendee == '')) {
    $response_count = 0;
    $response_count_query = "SELECT $qlabel FROM responses WHERE $qlabel<>''";
    $response_count_result = $dbh->query($response_count_query) or die ($dbh->error);
    while ($row = $response_count_result->fetch_object()) {
      $response_count++;
    }
    if ($response_count > 2) {
      $available_question = true;
      $random_num = rand(0, $response_count - 4);
      $correct_answer_query = "SELECT $qlabel FROM responses WHERE uid='$target_attendee'";
      $correct_answer_result = $dbh->query($correct_answer_query);
      while($row = $correct_answer_result->fetch_object()) {
        $correct_answer = $row->$qlabel;
      }
      $qoptions_array[] = $correct_answer;
      $distractor_query = "SELECT DISTINCT $qlabel FROM responses WHERE UID<>'$target_attendee' AND $qlabel<>'' LIMIT $random_num, 3";
      $distractor_result = $dbh->query($distractor_query);
      while ($distractor_row = $distractor_result->fetch_object()) {
        $qoptions_array[] = $distractor_row->$qlabel;
      }
      sort($qoptions_array);
    }
  } else {
    $available_question = true;
    $qoptions = preg_replace("/\'/", '', preg_replace("/\[(.*)\]/", "$1", $question->options));
    $qoptions_array = explode(',', $qoptions);
  }
  if ($available_question) {
    $question = new Question;
    $question->buildQuestion($qindex, $qtext, $qtype, $qlabel, $qoptions_array);
    $questions[] = $question;
  }
}

if (count($questions) == 1) {
  $output = json_encode($questions[0]);
} else {
  foreach ($questions as $question) {
    $output .= json_encode($question) . ',';
  }
  $output = preg_replace("/,$/", '', $output);
  $output = '[' . $output . ']';
}

echo $output;

class Question {
  function buildQuestion($qindex, $qtext, $qtype, $qlabel, $qoptions) {
    $this->index = $qindex;
    $this->text = $qtext;
    $this->type = $qtype;
    $this->label = $qlabel;
    $options_index = 1;
    foreach ($qoptions as $option) {
      $response_name = 'response' . $options_index;
      $this->$response_name = $option;
      $options_index++;
    }
  }
}

?>
