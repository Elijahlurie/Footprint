<!DOCTYPE html>
<?php
$time = date_default_timezone_set('America/Los_Angeles');
include "connection.php";
include "user_join.php";

 ?>
<html>
<head>
  <meta charset="utf-8">
  <title>About Us</title>
  <meta name="viewport" content="width=device-width,initial-scale=1"/>
  <link rel="stylesheet" href="styles/styles.css">
</head>

<body>
  <?php
    include "nav_tag.php";
   ?>
<div id="pageWrapper">
    <div id="about_us_header_container">
      <div id="about_us_header_text">
        <h1 id="about_us_header" class = "header">Take the step towards a greener planet.</h1>
        <p>Join us in saving the environment, one simple action at a time.</p>
      </div>
    </div>
    <div class="about_page_container_1">
      <div class="about_page_container">
        <div class="about_page_title_container">
          <h1>Our Goals</h1>
        </div>
        <div class="about_page_text_container">
          <ul>
            <li>Give every person who cares about the environment the chance to make a real difference.</li><br>
            <li>Teach people how to lessen their environmental footprint with easy actions backed by science.</li><br>
            <li>Develop a large community so together we can make a positive impact!</li>
          </ul>
        </div>
      </div>
    </div>
    <div class="about_page_container_2">
      <div class="about_page_container">
        <div class="about_page_title_container">
          <h1>How does it work?</h1>
        </div>
        <div class="about_page_text_container">
          <ol>
            <li>You sign up <a href="sign_up_page.php">here</a>.</li><br>
            <li>Each day at 11am, we text you an environmental action to complete.</li><br>
            <li>After completing your action, sign in to your dashboard to log your success!</li>
          </ol>
        </div>
      </div>
    </div>
    <div id="about_para_container">
      <h2>Every Action is Free and Accessible.</h2>
      <br><br>
      <p>We strive for Footprint to be accessible to all, so no action will ever ask you to buy anything or spend any money.</p>
      <br><br><br>
      <h2>Track the community's progress.</h2>
      <br><br>
      <p>Check back on your dashboard or the home page to see how many total actions we have completed. Alone these actions might seem small, but together we have huge potential!</p>
      <br><br><br>
      <h2>Let's Grow.</h2>
      <br><br>
      <p>Invite friends to maximize our collective impact!</p>
    </div>
    <div id="quote_container_cont">
    <div id="quote_container">
      <p><span class="italic">“You cannot get through a single day without having an impact on the world around you. What you do makes a difference and you have to decide what kind of a difference you want to make.”</span>
      <br>—Jane Goodall</p>
    </div>
    </div>
</div>

  <?php
    include "footer.php";
   ?>
   <script src="scripts/scripts.js"></script>

</body>

</html>
