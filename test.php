<?
  // Remember to copy files from the SDK's src/ directory to a
  // directory in your application on the server, such as php-sdk/
  include_once('facebook.php');

  $config = array(
    'appId' => '274836979297194',
    'secret' => '07bd57effc23dc91316d07fac68bba53',
  );

  // Initialize a Facebook instance from the PHP SDK
  $facebook = new Facebook($config);
  $user_id = $facebook->getUser();

  // Declare the variables we'll use to demonstrate
  // the new event-management APIs

  // We'll create an event in this example.
  // We'll need create_event permission for this.
  $event_id = 359893154086611;
  $event_name = "New Event API Test Event";
  $event_start = time();     // We'll just start the event now.
  $event_privacy = "SECRET"; // We'll make it secret so we don't annoy folks.

  // We'll use three users to demostrate the new APIs
  $user_id1 = "100000061103790";
  $user_id2 = "100000178962457";
  $user_id3 = "100000782350531";

  // Convenience method to print simple pre-formatted text.
  function printMsg($msg) {
     echo "<pre>$msg</pre>";
  }
?>
<html>
  <head></head>
  <body>

  <?
    if($user_id) {

      // We have a user ID, so probably a logged in user.
      // If not, we'll get an exception, which we handle below.
      try {

        // Get the user profiles so we can print friendly messages
        $me = $facebook->api('/me', 'GET');
        //var_dump($me);
        $user_1 = $facebook->api($user_id1, 'GET');
        //  var_dump($user_1);
        $user_2 = $facebook->api($user_id2, 'GET');
         // var_dump($user_2);
        $user_3 = $facebook->api($user_id3, 'GET');
       //   var_dump($user_3);

        printMsg('User 1: ' . $user_1['name']);
        printMsg('User 2: ' . $user_2['name']);
        printMsg('User 3: ' . $user_3['name']);

        // Create an event
        $ret_obj = $facebook->api('/me/events', 'POST', array(
                                    'name' => $event_name,
                                    'start_time' => $event_start,
                                    'privacy' => $event_privacy
                                 ));

        if(isset($ret_obj['id'])) {
          // Success
          $event_id = $ret_obj['id'];
          printMsg('Event ID: ' . $event_id);
          var_dump($event_id);
        } else {
          printMsg("Couldn't create event.");
        }
        

        // Invite user 1 to the event
        printMsg('Inviting ' . $user_1['name']);
        $ret_val = $facebook->api($event_id . "/invited/" . $user_id1,
                     'POST');

        if($ret_val) {
          // Success
          printMsg($user_1['name'] . ' successfully invited.');
        } else {
          printMsg("Couldn't invite " . $user_1['name']);
        }
        
        // Check if user 1 is invited to the event
        $ret_val = $facebook->api($event_id . '/invited/' . $user_id1, 'GET');
        
        if($ret_val) {
          printMsg($user_1['name'] . ' is invited (checked).');
        } else {
          printMsg($user_1['name'] . ' is not invited');
        }
        
        // User 1 should be no reply, so we check now.
        // Check in the same way for /attending, /maybe, /declined
        $ret_val = $facebook->api($event_id . '/noreply/' . $user_id1, 'GET');
        
        if($ret_val) {
          printMsg($user_1['name'] . ' has not replied.');
        } else {
          printMsg('Error: ' . $user_1['name'] . ' has replied');
        }

        // Check if user 2 is invited to the event (shouldn't be, yet)
        $ret_obj = $facebook->api($event_id . '/invited/' . $user_id2, 'GET');
          
        // If the user is not invited, we'll get an empty data[] array 
        if(count($ret_obj['data']) > 0) {
          printMsg('Error: ' . $user_2['name'] . ' invited.');
        } else {
          printMsg($user_2['name'] . ' is not invited');
        }   

        // Invite users 2 & 3
        printMsg('Inviting ' . $user_2['name'] . ' and ' . $user_3['name']);
        $ret_val = $facebook->api(
                     $event_id . '/invited?user=' . 
                       $user_id1 . ',' . $user_id2,
                     'POST');

        if($ret_val) {
          printMsg($user_2['name'] . ' and ' . $user_3['name'] . ' invited');
        } else {
          printMsg("Couldn't invite " . $user_2['name'] .
                     ' and ' . $user_3['name']);
        }   

        // Now User 2 should be invited.
        $ret_obj = $facebook->api($event_id . '/invited/' . $user_id2, 'GET');

        // If the user is not invited, we'll get an empty data[] array 
        if(count($ret_obj['data']) > 0) {
          printMsg($user_2['name'] . ' invited (checked).');
        } else {
          printMsg('Error: ' . $user_2['name'] . ' is not invited');
        }   

        // Un-invite user 2
        printMsg('Un-inviting ' . $user_2['name']);
        $ret_val = $facebook->api($event_id . '/invited/' . $user_id2,
                     'DELETE');

        if($ret_val) {
          printMsg($user_2['name'] . ' un-invited.');
        } else {
          printMsg("Couldn't un-invite " . $user_2['name'] );
        }   

        // Creator of the event ("me") should be attending by default
        printMsg('Checking if creator of event (' . 
                  $me['name'] . ') is attending');
        $ret_obj = $facebook->api($event_id . '/attending/' . $me['id'],
                                       'GET');

       // If attending, the data[] array will have our ID in it.
       if(isset($ret_obj['data'][0])) {
         printMsg($ret_obj['data'][0]['name'] . ' is attending');
       } else {
         printMsg('Error: Creator of event is not attending ?!');
       }   

        // Delete the event
        printMsg('Deleting the event.');
        $ret_val = $facebook->api($event_id, 'DELETE');

        if($ret_val) {
          printMsg('Event deleted');
        } else {
          printMsg("Error: Couldn't delete event");
        }   

      } catch(FacebookApiException $e) {
        // If the user is logged out, you can have a 
        // user ID even though the access token is invalid.
        // In this case, we'll get an exception, so we'll
        // just ask the user to login again here.
        $login_url = $facebook->getLoginUrl( array(
                       'scope' => 'create_event, rsvp_event'
                       )); 
        echo 'Please <a href="' . $login_url . '">login.</a>';
        error_log($e->getType());
        error_log($e->getMessage());
      }   
    } else {

      // No user, so print a link for the user to login
      $login_url = $facebook->getLoginUrl( array(
                      'scope' => 'create_event,rsvp_event'
                      )); 
      echo 'Please <a href="' . $login_url . '">login.</a>';
    }   
  ?>  
  </body>
</html>