<!DOCTYPE html>
<?php
$time = date_default_timezone_set('America/Los_Angeles');
include "connection.php";
include "user_join.php";
 ?>
<html>
<head>
  <meta charset="utf-8">
  <?php
  $specific_user = getTheUser($conn);
    echo '<title>'.$specific_user['name'].'\'s Dashboard</title>';
  ?>
  <meta name="viewport" content="width=device-width,initial-scale=1"/>
  <link rel="stylesheet" href="styles/styles.css">
</head>
<body>
  <?php
  include "nav_tag.php";

//if user not logged in, send them to login page
  if(!isset($_SESSION['id'])){
    header("Location:login_page.php");
  }
   ?>

    <div id="whole_usersPage">
    <?php
      echo "<h2 id='dashboard_header'>".$specific_user['name']."'s Dashboard</h2><br>";
    //messages depending on if they did their action yesterday and if they've done their action today
      if($specific_user['completed_yesterday'] && !$specific_user['completed_action']){
        echo '<h4 id="user_page_greeting">Great job completing your action yesterday! You  have a new one today!</h4>';
      } else if(!$specific_user['completed_yesterday'] && !$specific_user['completed_action']){
        echo '<h4 id="user_page_greeting">You have a new action today!</h4>';
      } else if($specific_user['completed_action']){
        echo '<h4 id="user_page_greeting">Great job today!</h4>';
      } else{
        echo '<h4 id="user_page_greeting">You have a new action today!</h4>';
      }

      //have specified wording for action announcement depending on if action has been completed
      $word;
      if(!$specific_user['completed_action']){
        $word = "is";
      }else{
        $word = "was";
      }
     ?>
      <div id="users_action_container">
        <?php echo '<p id="user_action_preface">Your action today '.$word.':</p>'; ?>

        <hr>
        <?php
          echo '<p id="users_action">'.$actions_array[$specific_user['curr_action']].'</p>';
         ?>
       </div>
       <?php
        if(!$specific_user['completed_action']){
          echo '
          <form id="completed_action_form" method="POST" action="'.completedAction($conn).'">
            <button id="completed_action_button" type="submit" name="completed_action">Completed It</button>
          </form>
          ';
        }
        ?>
        <h1 id="user_header_look">Look at all <br> we've accomplished. . .</h1>
        <?php
        $get_sql = "SELECT * FROM users WHERE id=".$_SESSION['id'].";";
        $get_result = mysqli_query($conn, $get_sql);
        $array_user = mysqli_fetch_all($get_result, MYSQLI_ASSOC);
        //different writings for if only 1 or 0 actions have been done
        if($array_user[0]["actions_completed"] > 1){
          echo '<div class="totaled_actions_container">
                  <p>You have done '.$array_user[0]["actions_completed"].' actions for the environment.</p>
                </div>';
        } else if($array_user[0]["actions_completed"] == 1){
          echo '<div class="totaled_actions_container">
                  <p>You have done '.$array_user[0]["actions_completed"].' action for the environment.</p>
                </div>';
        } else{
          echo '<div class="totaled_actions_container">
                  <p>If you\'ve done your action for today, press \'Completed It\' and come back tomorrow when you complete your next action!</p>
                </div>';
        }
        //different writings for if only 1 or 0 actions have been done
        if(addAllActions($conn) > 1){
          echo '<div class="totaled_actions_container">
                  <p>The entire Footprint community has done '.addAllActions($conn).' actions for the environment.</p>
                </div>';
        } else if (addAllActions($conn) == 1){
          echo '<div class="totaled_actions_container">
                  <p>The entire Footprint community has done '.addAllActions($conn).' action for the environment.</p>
                </div>';
        } else{
          echo '<div class="totaled_actions_container">
                  <p>Be the first in the Footprint community to complete an action!</p>
                </div>';
        }
        ?>
        <div class="totaled_actions_container">
          <p>We will stop climate change, together!</p>
        </div>

      <div id="additional_user_links_container">
          <div id="additional_user_links_header">
            <h3>Helpful links</h3>
          </div>
          <div id="additional_user_links">
            <?php
            echo '
              <p><a href="index.php">Home</a></p>
              <p><a href="edit_profile.php">Edit Profile</a></p>
              <form method="POST" action="'.logOut().'">
                <p><button id="additional_log_out_button" type="submit" name="logout">Log Out</button></p>
              </form>
              <p class="delete_link">Delete Account<p>
            ';
           ?>
          </div>
      </div>
    </div>
    </div>
  <?php
    include "footer.php";
   ?>
   <script src="scripts/scripts.js"></script>

</body>

</html>
