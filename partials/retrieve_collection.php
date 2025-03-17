<?php
$releases = [];

// Make a call to the discogs.com API, specifically to retrieve my collection.
$ch = curl_init();
curl_setopt($ch, CURLOPT_RETURNTRANSFER,true);
curl_setopt($ch,
    CURLOPT_HTTPHEADER,
    [
        'Content-Type: application/json',
        'User-Agent: ' . $app_name,
        'Authorization: Discogs token=' . $token,
    ]
);

    $url = 'https://api.discogs.com/users/' .
        $user .
        '/collection/folders/0/releases?sort=artist&per_page=' .
        $items_per_page;

    curl_setopt($ch, CURLOPT_URL, $url);
?>