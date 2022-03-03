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
  /*----Setup for twillio sending texts:-------*/
  require __DIR__ . '/vendor/autoload.php';
  use Twilio\Rest\Client;

  // Your Account SID and Auth Token from twilio.com/console
  $account_sid = 'AC2150fab45e4eb3042573fdf41874bddc';
  $auth_token = 'c910fe65623cd040666afc198e3e6159';
  // In production, these should be environment variables. E.g.:
  // $auth_token = $_ENV["TWILIO_AUTH_TOKEN"]

  // A Twilio number you own with SMS capabilities
  $twilio_number = "+19107734145";

  $client = new Client($account_sid, $auth_token);
  /*end of twilio setup*/

  //if user is already logged in, send them to user page
    if(isset($_SESSION['id'])){
      header("Location:user_page.php");
    }

     include "nav_tag.php";
    ?>

  <div id="pageWrapper">
    <div id="login_page_container">
      <h2 id="login_title">Footprint Login</h2>
      <div id="user_login_form">
      <?php
      //if no code has been sent, display form to enter phone number
      if(!$_SESSION['recovery_code_sent']){
        echo '
          <h3>Forgot your password? Request a one-time code.</h3>
          <form method="POST" action="'.sendRecoveryCode($conn, $client, $twilio_number).'">
            <div class="login_input_container">
              <h4>Phone Number</h4>
              <input  id="login_phone_input" class="form_input" type="text" name="recover_login_phone" placeholder="(xxx) xxx-xxxx">
              <p id="country_code">+1</p>
            </div>
            <button id="login_submit" type="submit" name="recover_login_submit_phone">Send Code</button>
          </form>
          ';
      } else if($_SESSION['recovery_code_sent'] ==1){
        //if a code has been sent, display form to enter the code
        echo '
          <h3>Enter the code texted to you below.</h3>
          <form method="POST" action="'.enterRecoveryCode($conn).'">
            <div class="login_input_container">
              <h4>6 digit code texted to you</h4>
              <input class="form_input" type="text" name="recover_login_code" placeholder="6 digit code">
            </div>
            <button id="login_submit" type="submit" name="recover_login_submit_code">Enter</button>
          </form>
        ';
      }

      echo '<p id="recover_login_error">'.$_SESSION['recovery_code_error'].'</p>';
      //reset login error message to be blank so it doesn't remain when user reloads the page again
      $_SESSION['recovery_code_error'] = "";
      ?>
      </div>
      <p id="no_account_link"><a href="sign_up_page.php">Don't have an account?</a></p>
    </div>
  </div>
  <?php
    include "footer.php";
   ?>
   <script src="scripts/scripts.js"></script>
</body>
</html>
