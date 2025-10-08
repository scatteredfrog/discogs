<?php
session_start();
require_once '../helpers.php';

$artist = $_POST['artist'];

$return_array = [];
$found_artist = false;

foreach($_SESSION['releases'] as $rK => $rV) {
    $found_artist = false;
    foreach ($rV['basic_information']['artists'] as $bi_artist) {
        if (strpos(strtolower($bi_artist['name']), strtolower($artist))) {
            $found_artist = true;
//            $return_array[] = $_SESSION['releases'][$rK];
            break;
        }
    }
    if ($found_artist) {
        $return_array[] = [
            'session_key' => $rK,
            'artists'     => condense_artists($rV['basic_information']['artists']),
            'title'       => format_title($rV),
            'formats'     => condense_formats($rV),
            'thumb'       => $rV['basic_information']['thumb'],
        ];
    }
}
error_log("Return array: " . json_encode($return_array));

echo json_encode($return_array);
