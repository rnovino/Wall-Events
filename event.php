<?
  // Remember to copy files from the SDK's src/ directory to a
  // directory in your application on the server, such as php-sdk/
include_once 'facebook.php';
require 'config.php';

////start the session if necessary
//if( session_id() ) {
//
//} else {
//  session_start();
//}

//instantiate the Facebook library with the APP ID and APP SECRET
$facebook = new Facebook(array(
  'appId' => $conf['APP_ID'],
  'secret' => $conf['APP_SECRET'],
  'cookie' => true
));

$user = $facebook->getUser();

if ($user) {
  try {
    // Proceed knowing you have a logged in user who's authenticated.
    $user_profile = $facebook->api('/me/events');
  } catch (FacebookApiException $e) {
    error_log($e);
    $user = null;
  }
}

// Login or logout url will be needed depending on current user state.
if ($user) {
  $logoutUrl = $facebook->getLogoutUrl();
} else {
  $loginUrl = $facebook->getLoginUrl();
}


?>
<h1>Yummie Tester</h1>
Hello <fb:name uid='<?php echo $user; ?>' useyou='false' possessive='true' />! <br>
Your id : <?php echo $user; ?>.<br>

<h2>Event<h2>

<?php
$events = $facebook->api_client->events_getMembers(486464158048547);

echo "<h3>Attending</h3>";

echo "<ol>";

if($events['attending'][0]){
  foreach($events['attending'] as $member){
  echo "<li><fb:name uid=\"$member\" useyou=\"false\"></li>";  
  } 
}else{
  echo "None";  
}

echo "</ol>";


echo "<h3>Unsure</h3>";

echo "<ol>";

if($events['unsure'][0]){
  foreach($events['unsure'] as $member){
  echo "<li><fb:name uid=\"$member\" useyou=\"false\"></li>";  
  } 
}else{
  echo "None";  
}

echo "</ol>";

echo "<h3>Declined</h3>";

echo "<ol>";

if($events['declined'][0]){
  foreach($events['declined'] as $member){
  echo "<li><fb:name uid=\"$member\" useyou=\"false\"></li>";  
  } 
}else{
  echo "None";  
}

echo "</ol>";

echo "<h3>Not Replied</h3>";

echo "<ol>";

if($events['not_replied'][0]){
  foreach($events['not_replied'] as $member){
  echo "<li><fb:name uid=\"$member\" useyou=\"false\"></li>";  
  } 
}else{
  echo "None";  
}

echo "</ol>"; 

?>