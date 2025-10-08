<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width:device-width, initial-scale=1">
        <title>Stuff that wasn't yet washed</title>
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

    // We're going to keep calling the API until there are no more pages left.
    do {
        $result = json_decode(curl_exec($ch), true);

        // While $result['pagination']['urls']['next'] exists,
        // keep loading the URL and adding to the releases.
        if (isset($result['releases'])) {
            foreach ($result['releases'] as $release) {
                // The custom field that contains the date an item is washed
                // has a field_id of 4. The only way that field will exist in
                // the data is if it's populated. Ergo, we just want the ones
                // that are NOT populated. Originally I had a foreach() loop
                // here, but that slowed things down, so I tried this trick.
                // At first I didn't have the "!== false" part, not realizing
                // that sometimes the "field_id" of 4 might actually be in
                // element zero of the array, and hence would be interpreted as
                // false, inaccurately. So I had to add the check for value AND
                // type to make sure that items with the field_id of 4 in
                // element zero are not included.
                if (!empty($release['notes']) &&
                    (array_search($field_id,
                        array_column($release['notes'], 'field_id'))) !== false) {
                    continue;
                }
                $releases[] = $release;
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

    curl_close($ch);

?>

<table class="table table-striped"><form id="unwashed_records" method="post" action="washy.php">
    <thead class="table-dark stickyHeader">
        <tr>
            <th scope="col">Artist</th>
            <th scope="col">Title</th>
            <th scope="col">Format</th>
            <th scope="col"></th>
            <th scope="col"></th>
            <th scope="col"><?php echo $home; ?></th>
        </tr>
    </thead>
    <tbody>
<?php
    foreach ($releases as $release) {
        // cache nested values once
        $basic = $release['basic_information'];
        $instanceId = $release['instance_id'];
        $releaseId = $release['id'];

        // cache output from helper functions
        $artistHtml = condense_artists($basic['artists']);
        $titleHtml  = format_title($release);

        // build formats fast using array_map + implode
        $formatNames = array_map(function($f)
            { return $f['name']; },
            $basic['formats']);

        $formatField = implode('<br />', $formatNames);
        if ($formatField !== '') {
            $formatField .= '<br />';
        }

        // Build the row string.
        $row  = '<tr><td>';
        $row .= $artistHtml . '</td><td>';
        $row .= $titleHtml . '</td><td>';
        $row .= $formatField . '</td><td>';
        $row .= '<input type="hidden" id="format_' . $instanceId . '" value="' . htmlspecialchars($formatField, ENT_QUOTES) . '" />';
        $row .= '</td><td>';
        $row .= '<button class="btn btn-danger" name="na_' . $instanceId .
                '" value="na_' . $instanceId . '_' . $releaseId .
                '" onClick="checkConflict(\'na_' . $instanceId . '_' . $releaseId .
                '\');" type="button">NO</button></td><td>';
        $row .= '<button class="btn btn-warning" name="date_' . $instanceId .
                '" value="ed_' . $instanceId .
                '" onClick="checkConflict(\'ed_' . $instanceId . '_' . $releaseId .
                '\');" type="button">When?</button></td><td>';
        $row .= '<button class="btn btn-primary" name="today_' . $instanceId .
                '" value="today_' . $instanceId .
                '" onClick="checkConflict(\'today_' . $instanceId . '_' . $releaseId .
                '\');" type="button">today</button>';
        $row .= '</td></tr>';

        echo $row;
    }
    echo '</tbody></table></form>';
    require_once APP_ROOT . 'partials/modals.html';
?>
</body>
<script src="js/wash.js"></script>
</html>