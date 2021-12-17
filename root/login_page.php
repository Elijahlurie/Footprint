<!DOCTYPE html>
<?php
$time = date_default_timezone_set('America/Los_Angeles');
include "connection.php";
include "user_join.php";
 ?>
<html>
<head>
  <meta charset="utf-8">
  <title>Login</title>
  <meta name="viewport" content="width=device-width,initial-scale=1"/>
  <link rel="stylesheet" href="styles/styles.css">
</head>

<body>
  <?php


  //if user is already logged in, send them to user page
    if(isset($_SESSION['id'])){
      header("Location:user_page.php");
    }

     include "nav_tag.php";
    ?>

  <div id="pageWrapper">
    <div id="login_page_container">
      <h2 id="login_title">Footprint Login</h2>
      <?php
      echo '
      <div id="user_login_form">
        <form method="POST" action="'.loginUsers($conn).'">
          <div class="login_input_container">
            <h4>Your First Name</h4>
            <input class="form_input" type="text" name="login_name" placeholder="First Name">
          </div>
          <div class="login_input_container">
            <h4>Your Phone Number</h4>
            <input  id="login_phone_input" class="form_input" type="text" name="login_phone" placeholder="(xxx) xxx-xxxx">
            <p id="country_code">+1</p>
          </div>
          <button id="login_submit" type="submit" name="login_submit">Enter</button>
        </form>
      </div>
      <p id="login_error">'.$_SESSION['login_error'].'</p>
      ';

      //reset login error message to be blank so it doesn't remain when user reloads the page again
      $_SESSION['login_error'] = "";
      ?>
      <p id="no_account_link"><a href="sign_up_page.php">Don't have an account?</a></p>
    </div>
  </div>
  <?php
    include "footer.php";
   ?>
   <script src="scripts/scripts.js"></script>
</body>
</html>
