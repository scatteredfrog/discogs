<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width:device-width, initial-scale=1">
        <title>Dates that need reformatting</title>
        <link href="css/main.css" rel="stylesheet">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

    </head>
    <body>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js" integrity="sha384-0pUGZvbkm6XF6gxjEnlmuGrJXVbNuzT9qBBavbLwCsOGabYfZo0T0to5eqruptLy" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>

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
                // that are populated.
                // We just want dates that are formated with the month first
                // so we can re-format them with the year first. The easiest
                // way I could think of to find out is to explode the date and
                // check if the first part is numeric, and if so, if it's
                // under 1000 -- after all, the year must be four digits!
                if (!empty($release['notes']) &&
                    array_search(4, array_column($release['notes'], 'field_id'))) {
                    foreach($release['notes'] as $note) {
                        if ($note['field_id'] == '4') {
                            $parts = explode('/', $note['value']);
                            if (is_numeric($parts[0]) && ($parts[0] < 1000)) {
                                $releases[] = $release;
                            }
                        }
                    }
                } else {
                    continue;
                }
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
        <table class="table table-striped">
    <thead class="table-dark stickyHeader">
        <tr>
            <th scope="col">Artist</th>
            <th scope="col">Title</th>
            <th scope="col">Date Washed</th>
            <th scope="col"><?php echo $home; ?></th>
        </tr>
    </thead>
    <tbody>
<?php
    foreach ($releases as $release) {
        echo '<tr><td>';
        // Artist
        echo condense_artists(
            $release['basic_information']['artists']
        ) . '</td><td>';

        // Title
        echo format_title($release);
        echo '</td><td>';

        // Date
        foreach ($release['notes'] as $note) {
            if ($note['field_id'] == $field_id) {
                $current_date = $note['value'];
                echo $current_date;
                break;
            }
        }

        // Reformat button. We'll stick the release ID, instance_id, and date
        // into the ID of the button so we can handle it easily in the jQuery.
        echo '</td><td>';
        echo '<button class="reformat btn btn-info" ' .
            'id="release_' .
            $release['id'] .
            '_' .
            $release['instance_id'] .
            '_' .
            $current_date .
            '">Reformat</button></td></tr>';
    }
?>
            </tbody>
        </table>
        <?php require_once APP_ROOT . 'partials/modals.html'; ?>
        <script>
            // Not a lot of JS, so we'll just keep it in this file.

            // User clicks the "Reformat" button.
            $('.reformat').on('click', function()
            {
                let pieces = $(this).attr('id').split('_');

                let post_data = {
                    'release_id' : pieces[1],
                    'instance_id': pieces[2],
                    'wash_date'  : pieces[3],
                };

                // Send the data to the backend.
                $.post('post/fixit.php', post_data, function(response) {
                    // The backend returns the reformatted date if all goes
                    // well. Match the return data against a regex for
                    // YYYY-MM-DD format.
                    if (/\d{4}\-\d{2}\-\d{2}/.test(response)) {
                        // Reload if all goes well.
                        location.reload();
                    } else {
                        $('#modal_error_text').html(response);
                        $('#error_modal').modal('show');
                    }
                });
            });
        </script>
    </body>
</html>