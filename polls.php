<?php

$data = $_POST;
print_r($data);

$poll_id = $data["id"];
$answers = $data["answers"];
$voted = $data["voted"];

print_r($poll_id);
print_r($answers);
print_r($voted);

$file_path = 'polls.json';

$file_data = json_decode(file_get_contents($file_path), true);

$poll = $file_data[$poll_id];

$poll["answers"] = $answers;
$poll["voted"] = $voted;

file_put_contents($file_path, json_encode($file_data));

// for some reason, the json file is not being updated. I have tried many things, I have tried using ajax, fetch etc.
// but I can't seem to get it working. That is the reason I have submitted late. :/

?>