<?php
include "connection.php";


//puts all the possible greetings from the database in an array and returns the array
function getGreetings($connection){
  $greetings_sql = "SELECT * FROM messages;";
  $greetings_result = mysqli_query($connection, $greetings_sql);
  $array_greetings = mysqli_fetch_all($greetings_result, MYSQLI_ASSOC);

  $greetings_array = [];
  foreach($array_greetings as $greeting){
    $greetings_array[] = $greeting["greeting"];
  }
  return $greetings_array;
};
$greetings_array = getGreetings($conn);

//puts all the possible actions from the database in an array and returns the array
function getActions($connection){
  $actions_sql = "SELECT * FROM actions;";
  $actions_result = mysqli_query($connection, $actions_sql);
  $array_actions = mysqli_fetch_all($actions_result, MYSQLI_ASSOC);
  $new_array = [];
  foreach($array_actions as $action){
    $new_array[] = $action["action"];
  }
  return $new_array;
};
$actions_array = getActions($conn);


/*----all the stuff for twillio sending texts:-------*/
  /*  require __DIR__ . '/vendor/autoload.php';
    use Twilio\Rest\Client;

    // Your Account SID and Auth Token from twilio.com/console
    $account_sid = 'AC15958a42c66183337af70a7c09160f62';
    $auth_token = 'bbf9c688813d6dcc5981e34649357741';
    // In production, these should be environment variables. E.g.:
    // $auth_token = $_ENV["TWILIO_AUTH_TOKEN"]


    // A Twilio number you own with SMS capabilities
    $twilio_number = "+15082528841";

    $client = new Client($account_sid, $auth_token);
*/

//get array of users from database
  $users_sql = "SELECT * FROM users;";
  $users_result = mysqli_query($conn, $users_sql);
  $array_users = mysqli_fetch_all($users_result, MYSQLI_ASSOC);

    foreach($array_users as $user):
        $text_number = $user['phone'];

        $random_greeting_number = mt_rand(0,count($greetings_array)-1);

        //add/subtract seconds from time(), seconds since midnight jan 1 1970 GMT, based on users timezone
        //$user['timezone']/100 because I wanted to avoid decimal in the database
        $user_seconds = time() + (($user['timezone']/100)*3600);
        //get the seconds left over when converting user seconds to days (seconds passed so far today)
        $user_seconds_today = $user_seconds % 86400;
        //convert $user_seconds_today to hours (not sure why i need to subtract 1 for it to be right)
        $user_hours_today = ($user_seconds_today / 3600);

    /*
      //if its past 11am and earlier than 8pm text them
        if($user_hours_today >= 11 && $hours_today < 20 && !$user['texted']):
          $client->messages->create(
              // Where to send a text message (your cell phone?)
              $text_number,
              array(
                  'from' => $twilio_number,
                  'body' => $greetings_array[$random_greeting_number].' '.$user['name'].'! Your eco action today is: '.$actions_array[$user["curr_action"]]
              )
          );
          //update that the user has been texted
          $sql = "UPDATE users SET texted = ".time()." WHERE id=".$user['id'].";";
          $result = mysqli_query($conn, $sql);
        endif;

        */
        //get current days since jan 1 1970 in the users time zone
  	    $user_days = ($user_seconds/86400) - ($user_seconds%86400)/86400;
        //if it's an appropriate time to text and the user has not completed an action in 15+ days, send the user a check-in text
        if($user_hours_today >= 12 && $hours_today < 20 && ($user_days - $user['last_completed_action']) >= 15):
            //send a check-in text
        endif;
    endforeach;

//include in this file getCoordinates and getTimezone functions from user_join.php to use them in this file without having to include whole user_join file

//given a zipcode, use Google geocode API to return the latitude and longitude
  function getCoordinates2($zip){
    $url = "https://maps.googleapis.com/maps/api/geocode/json?address=".urlencode($zip)."&key=AIzaSyBvgdF6Zqi8ax5CXNx4--PI00XqISwvFek";
    $result_string = file_get_contents($url);
    $result = json_decode($result_string, true);
  	$result1[]=$result['results'][0];
  	$result2[]=$result1[0]['geometry'];
  	$result3[]=$result2[0]['location'];
  	return $result3[0];
  };

  //given coordinates and a number of seconds since Jan 1, 1970, return the timzone offset from UTC
  function getTimezone2($timestamp, $coordinates){
  	$timezone_url = "https://maps.googleapis.com/maps/api/timezone/json?location=".$coordinates['lat'].",".$coordinates['lng']."&timestamp=".$timestamp."&key=AIzaSyBvgdF6Zqi8ax5CXNx4--PI00XqISwvFek";
  	$timezone_result_string = file_get_contents($timezone_url);
  	$timezone_result = json_decode($timezone_result_string, true);
  	//return the offset from UTC time, taking daylight savins into account by adding the raw offset from UTC (in seconds) to the daylight savings offset and converting to hours
  	$offset = ($timezone_result['rawOffset'] + $timezone_result['dstOffset']) / 3600;
  	return $offset;
  };


//check if day has changed and assign new random actions if it has
//also check to see if daylight savings has changed timezone offset for user ad adjust database values accordingly
function checkDay($connection, $actions_array){
//get array of users from database
  $users_sql = "SELECT * FROM users;";
  $users_result = mysqli_query($connection, $users_sql);
  $array_users = mysqli_fetch_all($users_result, MYSQLI_ASSOC);
//get current time to not have to repeat calculation in foreach loop
  $timestamp = time();
//if today is over value for the day in yesterday, update yesterday
//and give each user a new random # for their action
  foreach($array_users as $user):
    //get current seconds since jan 1 1970 in the users time zone and convert to nearest whole days.
    $user_curr_time = time() + 3600 * ($user['timezone']/100);
    $user_days = ($user_curr_time/86400) - ($user_curr_time%86400)/86400;

    //if the number of days since 1970 for the users timezone is greater than the value for yesterday for that user, update their actions and set days as the new value for yesterday
    if($user_days > $user['yesterday']):
      $user_id = $user['id'];

      $day_sql = "UPDATE users SET yesterday = ".$user_days." WHERE id = $user_id;";
      $day_result = mysqli_query($connection, $day_sql);


      //use the past_actions table to find an action that the user has not had recently and then update the past_actions table
      //get all the values in the past_actions table

      $get_past_actions_sql = "SELECT * FROM past_actions WHERE user_id = $user_id;";
      $get_past_actions_result = mysqli_query($connection, $get_past_actions_sql);
      $array_past_actions_row = mysqli_fetch_all($get_past_actions_result, MYSQLI_ASSOC)[0];

      //now, 1. iterate through the list of past actions to find limits for random number generation for new action and 2. set day0 in sql statement below to new action and each day after that to whatever the value before it was (set day10 to whatever day9 was before ) using the array i got above

      //create array to store only the number values for the user's past actions
      $array_past_actions = [];
      //iterate through the columns day0, day1, day2, etc until day19 and store the current values for those columns for specified user in $array_past_actions
      for($i = 0; $i < 20; $i++){
        $column_name = 'day'. $i;
        $array_past_actions[] = $array_past_actions_row[$column_name];
      }

      //generate random numbers within length of $array_actions until the randum number isn't any of the ones they have had recetly (numbers listed in the $array_past_actions array)
        do{
          $rand = mt_rand(0,(count($actions_array)-1));
        }while(in_array($rand, $array_past_actions));


      //update the past_actions table to have the users new action listed in day0 and every othe past action moved over one column

      $new_past_actions_sql = "UPDATE past_actions SET day0 = $rand, day1 = $array_past_actions[0], day2 = $array_past_actions[1], day3 = $array_past_actions[2], day4 = $array_past_actions[3], day5 = $array_past_actions[4], day6 = $array_past_actions[5], day7 = $array_past_actions[6], day8 = $array_past_actions[7], day9 = $array_past_actions[8], day10 = $array_past_actions[9], day11 = $array_past_actions[10], day12 = $array_past_actions[11], day13 = $array_past_actions[12], day14 = $array_past_actions[13], day15 = $array_past_actions[14], day16 = $array_past_actions[15], day17 = $array_past_actions[16], day18 = $array_past_actions[17], day19 = $array_past_actions[18] WHERE user_id = $user_id;";
      $new_past_actions_result = mysqli_query($connection, $new_past_actions_sql);


      //update the user's new action in the user table as well
        $user_action_sql = "UPDATE users SET curr_action = ".$rand." WHERE id=".$user['id'].";";
        $user_action_result = mysqli_query($connection, $user_action_sql);
        $user_texted_sql = "UPDATE users SET texted = 0 WHERE id=".$user['id'].";";
        $user_texted_result = mysqli_query($connection, $user_texted_sql);

        //update if user completed the action that day

        $completed_yesterday = $user['completed_action'];
        //set new completed yesterday for user accordingly
        $completed_yesterday_sql = "UPDATE users SET completed_yesterday = ".$completed_yesterday." WHERE id=".$user['id'].";";
        $completed_yesterday_result = mysqli_query($connection, $completed_yesterday_sql);

        //set completed_action to false
        $completed_action_sql = "UPDATE users SET completed_action = FALSE WHERE id=".$user['id'].";";
        $completed_action_result = mysqli_query($connection, $completed_action_sql);

        //Check if timezone for user has changed due to daylight savings and update timezone if it has
        //call getCoordinates() function to get latitude and longitude from zipcode
        $location = getCoordinates2($user['zipcode']);
        //call getTimezone() function to get time zone from the coordinates we just got
        $timezone = getTimezone2($timestamp,$location);
        //multiply by 100 for storage convenience if there is a timezone offset that isn't a whole number
        $timezone_stored = $timezone * 100;
        //if current timezone calculated is not equal to timezone stored for user, update timezone for user
        if($timezone_stored != $user['timezone']){
          $edit_timezone_sql = "UPDATE users SET timezone = ".$timezone_stored." WHERE id = ".$user['id'].";";
          $edit_timezone_result = mysqli_query($connection, $edit_timezone_sql);
        }
      endif;
    endforeach;
};
checkDay($conn, $actions_array);
?>
