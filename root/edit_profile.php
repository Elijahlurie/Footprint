<!DOCTYPE html>
<?php

$time = date_default_timezone_set('America/Los_Angeles');
include "connection.php";
include "user_join.php";
?>
<html>
<head>
  <meta charset="utf-8">
  <title>Profile</title>
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
  <div id="edit_profile_wrapper">
    <h1 id="edit_profile_title">Edit Profile</h1>
    <?php
    //format the phone number with parntheses anad spaces to look nicer
      $formatted_phone = substr_replace($specific_user['phone'], ' (', 2, 0 );
      $formatted_phone = substr_replace($formatted_phone, ') ', 7, 0 );
      $formatted_phone = substr_replace($formatted_phone, '-', 12, 0 );

      echo '
        <form id="edit_profile_form" method="POST" action = "'.editProfile($conn).'">
          <ul>
            <li>
              <h3>First Name</h3>
              <input class="form_input" name="edit_name" type="text" value="'.$specific_user['name'].'">
            </li>
            <li>
              <h3>Phone Number</h3>
              <input class="form_input" id="edit_phone" name="edit_phone" type="text" value="'.$formatted_phone.'">
            </li>
            <li>
              <h3>Postal Code</h3>
              <input class="form_input" name="edit_zipcode" type="text" value="'.$specific_user['zipcode'].'">
            </li>
            <li id="edit_profile_error">
              '.$_SESSION['edit_profile_error'].'
            </li>
            <li id="edit_submit_container">
            <button id="edit_profile_submit" name="edit_profile_submit" type="submit">Save</button>
            </li>
          </ul>
        </form>
      ';

      //reset edit profile error message to be blank so it doesn't remain when user reloads the page again
      $_SESSION['edit_profile_error'] = "";
     ?>
     <button class = "delete_link" id="edit_profile_delete_link">Delete Account</button>
     <img id="sitting_doodle" src="https://blush.design/api/download?shareUri=V8e10g5KT&w=800&h=800&fm=png" alt="drawing of man siting in chair reading">
</div>
  <?php
    include "footer.php";
   ?>
   <script src="scripts/scripts.js"></script>
 </body>
 </html>
