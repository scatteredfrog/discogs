<?php
define( 'APP_ROOT', dirname( __FILE__ ) . '/' );

$user = 'YourUserName';
$token = 'YourCurrentAppToken';
$field_id = 4; // or whatever the ID of your equivalent "Date washed" field is

// The API call will not return all the items at once but in paginated pieces.
// Here we set the call to pull the maximum number of pages allowed, to cut
// down on the number of cURL calls.
$items_per_page = 100;

$app_name = 'your_app_name';