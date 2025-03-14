<?php
require_once '../config.php';
$ch = curl_init();
curl_setopt($ch, CURLOPT_RETURNTRANSFER,true);
curl_setopt(
    $ch,
    CURLOPT_USERAGENT,
    $app_name,
);

// Was a POST request made?
if (!empty($_POST)) {
    if (isset($_POST['action_instance'])) {
        // Required fields:
        // - username
        // - value [that is, what will be put in the washy field]
        // - folder_id
        // - release_id
        // - instance_id
        // - field_id
        if (str_contains($_POST['action_instance'], '_')) {
            list($action, $instance_id, $release_id) = explode('_', $_POST['action_instance']);
        } else {
            die('Action missing');
        }
        $folder_id = 0; // ENTIRE collection

        // Switch to determine the action.
        switch($action) {
            // Should not be washed - mark as "n/a".
            case 'na':
                $value = 'n/a';
                break;
            // Just washed: put today's date.
            case 'today':
                date_default_timezone_set('America/Chicago');
                $value = date('Y-m-d');
                break;
            // Washed on another day - enter the date.
            case 'ed':
                $date = date_create($_POST['new_date']);
                $value = date_format($date, 'Y-m-d');
                break;
            default:
                die('Unknown action.');
            }

        $url = 'https://api.discogs.com/users/' .
            $user .
            '/collection/folders/0/releases/' .
            $release_id .
            '/instances/' .
            $instance_id .
            '/fields/' .
            $field_id;

        $post_data = array('value' => $value);
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
            echo 1;
        } else {
            die('Could not write to the database.');
        }
    } else {
        die('Missing action and instance.');
    }
}
