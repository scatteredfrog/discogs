<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width:device-width, initial-scale=1">
        <title>Transfer ownershiup</title>
        <link href="css/main.css" rel="stylesheet">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
        <link href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/themes/ui-lightness/jquery-ui.css" rel='stylesheet'>

    </head>
    <body>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js" integrity="sha384-0pUGZvbkm6XF6gxjEnlmuGrJXVbNuzT9qBBavbLwCsOGabYfZo0T0to5eqruptLy" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>

<?php
require_once 'config.php';
require_once 'helpers.php';
require_once APP_ROOT . 'partials/retrieve_collection.php';

session_start();

if (!isset($_SESSION['count'])) {
    $_SESSION['releases'] = [];
    do {
        $result = json_decode(curl_exec($ch), true);
        if (isset($result['releases'])) {
            foreach($result['releases'] as $release) {
                $_SESSION['releases'][] = $release;
            }
        }
        // Here's where we check to see if there's another page of data. If
        // there is, it's in the array as [pagination][urls][next]. If found,
        // we need to make a curl call to that URL to get the next batch.
        if (isset($result['pagination']['urls']['next'])) {
            curl_setopt(
                $ch,
                CURLOPT_URL,
                $result['pagination']['urls']['next'],
            );
        }
    } while (isset($result['pagination']['urls']['next']));

    $_SESSION['count'] = count($_SESSION['releases']);
}
?>

<div class="container-fluid">
    <div class="row bg-info fw-bold p-3">
        Transfer Releases to a Different Owner
    </div>
</div>
<div class="container p-3">
    <form name="artist_title_search" id="artist_title_search">
        <div class="container">
            <div class="row height42">
                <div class="col-4">
                    <input class="form-control" type="text" placeholder="Artist name" id="artist" />
                </div>
            </div>
            <div class="row height42">
                <div class="col-4">
                    <input class="form-control" type="text" placeholder="Title" id="title" />
                </div>
            </div>
            <div class="row height42">
                <div class="col-4">
                    <button type="button" class="mx-auto btn-sm btn-primary btn float-end" name="submit" id="submit">Search</button>
                </div>
            </div>
        </div>
    </form>
</div>