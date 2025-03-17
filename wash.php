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
        $format_field = '';
        $found_album = false;
        $found_single = false;
        echo '<tr><td>';

        // Artist
        echo condense_artists(
            $release['basic_information']['artists']
        ) . '</td><td>';

        // Title
        echo format_title($release);
        echo '</td><td>';

        // Listing the format is important because some formats should NOT
        // be washed, such as tapes and most flexidiscs.
        foreach ($release['basic_information']['formats'] as $format) {
            $format_field .= $format['name'] . '<br />';
        }
        echo $format_field . '</td><td>';
        echo '<input type="hidden" id="format_' .
            $release['instance_id'] .
            '" value="' .
            $format_field .
            '" />';

        // Click this button if this item should not be washed.
        echo '<button class="btn btn-danger" name="na_' .
            $release['instance_id'] .
            '" value="na_' .
                $release['instance_id'] .
            '_' .
            $release['id'] .
            '" onClick="checkConflict(\'na_' .
            $release['instance_id'] .
            '_' .
            $release['id'] .
            '\');" type="button">NO</button></td><td>';

        // Click this button if this item needs a date other than today.
        echo '<button class="btn btn-warning" ' .
            'name="date_' .
            $release['instance_id'] .
            '" value="ed_' .
            $release['instance_id'] .
            '" onClick="checkConflict(\'ed_' .
            $release['instance_id'] .
            '_' .
            $release['id'] .
            '\');" type="button">When?</button></td><td>';

        // Click this button to mark the item as washed on today's date.
        echo '<button class="btn btn-primary" name="today_' .
            $release['instance_id'] .
            '" value="today_' .
            $release['instance_id'] .
            '" onClick="checkConflict(\'today_' .
            $release['instance_id']  .
            '_' .
            $release['id'] .
            '\');" type="button">today</button>';
        echo '</td></tr>';
    }
    echo '</tbody></table></form>';
    require_once APP_ROOT . 'partials/modals.html';
?>
</body>
<script src="js/wash.js"></script>
</html>