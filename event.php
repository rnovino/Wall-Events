<?
  // Remember to copy files from the SDK's src/ directory to a
  // directory in your application on the server, such as php-sdk/
include_once 'facebook.php';
require 'config.php';

//start the session if necessary
if( session_id() ) {

} else {
  session_start();
}

//instantiate the Facebook library with the APP ID and APP SECRET
$facebook = new Facebook(array(
  'appId' => $conf['APP_ID'],
  'secret' => $conf['APP_SECRET'],
  'cookie' => true
));

$event_att = $facebook->api(
  '/me/event',
  'GET',
  array(
    'access_token' => $_SESSION['active']['access_token']
  )
);



?>