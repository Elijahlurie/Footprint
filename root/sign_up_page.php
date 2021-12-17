<!DOCTYPE html>
<?php

$time = date_default_timezone_set('America/Los_Angeles');
include "connection.php";
include "user_join.php";
?>
<html>
<head>
  <meta charset="utf-8">
  <title>Footprint</title>
  <meta name="viewport" content="width=device-width,initial-scale=1"/>
  <link rel="stylesheet" href="styles/styles.css">
</head>

<body>
  <?php
  include "nav_tag.php";


  //if user logged in, send them to home page
    if(isset($_SESSION['id'])){
      header("Location:index.php");
    }

   ?>
   <div id="sign_up_page">
      <div id='sign_up_page_header'>
        <h2>Sign Up</h2>
        <br>
        <h3>Welcome to Footprint! You're here because you want to make a difference.</h3>
        <p>Curious? <span><a href="about_us.php">Learn More</a></span>.</p>
      </div>
      <div id="user_join_form_margins">
        <?php
          include "sign_up_block.php";
        ?>
    </div>
    <div id="zipcode_explanation">
      <h3>Why do we ask for your postal code?</h3><br>
      <p>We use your postal code to calculate the right time for your local timezone to text you each day.</p>
    </div>
    <div id="have_account_link_container">
      <p><a href="login_page.php">Already have an account?</a></p>
    </div>

  </div>
  <?php
    include "footer.php";
   ?>
   <script src="scripts/scripts.js"></script>
   <script>
      //code for opening the explanation for why we ask for users' postal codes
      var zipcode_explanation = document.getElementById('zipcode_explanation');
      openDivs(why_zipcode_link, zipcode_explanation, 'block', true, user_join_form);

   </script>

</body>

</html>
