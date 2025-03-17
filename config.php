<?php
define( 'APP_ROOT', dirname( __FILE__ ) . '/' );

$user = 'YourUserName';
$token = 'YourCurrentAppToken';
$field_id = 4; // or whatever the ID of your equivalent "Date washed" field is

// The API call will not return all the items at once but in paginated pieces.
// Here we set the call to pull the maximum number of pages allowed, to cut
// down on the number of curl calls.
$items_per_page = 100;

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

$app_name = 'your_app_name';
