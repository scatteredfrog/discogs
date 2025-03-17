<?php
// =======================================================
// VARIABLES
// =======================================================
$home = '<a href="/">' .
    '<button type="button" class="btn btn-success">Home</button>';

// This is the error symbol that will appear in the header of the modal that
// pops up if there's a problem when submitting. Since I'm using it twice, I
// put some DRY in by assigning it to a variable.
$error_symbol = '<svg xmlns="http://www.w3.org/2000/svg" width="16" ' .
    'height="16" fill="currentColor" class="bi bi-exclamation-diamond-fill" ' .
    'viewBox="0 0 16 16"><path d="M9.05.435c-.58-.58-1.52-.58-2.1 0L.436 ' .
    '6.95c-.58.58-.58 1.519 0 2.098l6.516 6.516c.58.58 1.519.58 2.098 ' .
    '0l6.516-6.516c.58-.58.58-1.519 0-2.098zM8 4c.535 0 .954.462.9.995l-.35 ' .
    '3.507a.552.552 0 0 1-1.1 0L7.1 4.995A.905.905 0 0 1 8 4m.002 6a1 1 0 1 ' .
    '1 0 2 1 1 0 0 1 0-2"/></svg>';

// =======================================================
// FUNCTIONS
// =======================================================

/**
 * Discogs sends artists as an array. Sometimes there are multiple artists,
 * so we're going to take the array of artists and condense it to a string.
 *
 * @param array $artists The array containing the artist name(s).
 *
 * @return stirng|null $artist_string The condensed artist string.
 */
function condense_artists(array $artists): ?string
{
    $artist_string = '';

    foreach ($artists as $artist) {
        $artist_string .= $artist['name'] . '<br />';
    }
    return $artist_string;
}

/**
 * Here we're putting quotes around the title if it's a single, as per standard
 * punctuation rules. If it's an album or EP, we'll use italics. The only
 * problem is that due to the way titles are entered, it's not always 100%
 * accurate, so we'll just not touch any title that isn't totally clear as to
 * whether it's a single or an album/EP. Singles are SUPPOSED to be marked as
 * "Single" on discogs.com, but alas, they aren't always.
 *
 * Why is this important? Case in point: "Lay a Little Lovin' On Me" by Robin
 * McNamara. I have both the single, and the album of the same name. I need to
 * be able to differentiate which is which.
 *
 * @param array $release All the details about a title.
 *
 * @return string|null $formatted_title The formatted title text.
 */
function format_title(array $release): ?string
{
    $found_album = $found_single = false;
    $formatted_title = '';
    foreach($release['basic_information']['formats'] as $format) {
        if (isset($format['descriptions'])) {
            if (array_intersect(['LP', 'Album', 'EP'], $format['descriptions'])) {
                $found_album = true;
                $formatted_title = '<span class="fst-italic">';
                break;
            } else if (array_intersect(['Single'], $format['descriptions'])) {
                $found_single = true;
                $formatted_title = '"';
                break;
            }
        }
    }
    $formatted_title .= $release['basic_information']['title'];
    $formatted_title .= $found_single ? '"' : ($found_album ? '</span>' : '');

    return $formatted_title;
}