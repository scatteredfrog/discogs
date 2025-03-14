<?php
// Discogs sends artists as an array. Sometimes there might be multiple
// artists, so we're going to take the array of artists and condense it to a
// string.
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