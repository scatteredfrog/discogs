<?php
require_once '../config.php';

// Was a POST request made?
if (!empty($_POST)) {
    if (isset($_POST['wash_date']) && isset($_POST['release_id']) && isset($_POST['instance_id'])) {
        $reformatted_date = date('Y-m-d', strtotime($_POST['wash_date']));
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,true);
        curl_setopt(
            $ch,
            CURLOPT_USERAGENT,
            "www.fab4it.com",
        );
        $url = 'https://api.discogs.com/users/' .
            $user .
            '/collection/folders/0/releases/' .
            $_POST['release_id'] .
            '/instances/' .
            $_POST['instance_id'] .
            '/fields/' .
            $field_id;

        $post_data = array('value' => $reformatted_date);
        curl_setopt($ch,
            CURLOPT_HTTPHEADER,
            [
            'Content-Type: application/json',
            'Authorization: Discogs token=' . $token
            ]
        );
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($post_data));
        curl_setopt($ch, CURLOPT_URL, $url);
        $response = json_decode(curl_exec($ch), 1);
        $curl_response = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        if ($curl_response == 204) {
            curl_close($ch);
            echo $reformatted_date;
        } else {
            die('Could not write to the database.');
        }

    } else {
        die('Missing date or release ID!');
    }
} else {
    die('Post data missing!');
}