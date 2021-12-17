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
   ?>
  <div id="pageWrapper">
    <div id="main_block">
      <h1 id="join_title">One easy action, every day.<br>
        It adds up.
      </h1><br>
      <?php
      //if no one is logged in, include the code for th sign up form
      if(!isset($_SESSION['id'])){
        echo '
        <div id="user_join_form_container">
          <h3 id="open_sign_up">Sign Up</h3>';

          include "sign_up_block.php";
        echo '</div>';
        //include a signup link that just links to sign up page for if user is on mobile device
        echo '<h3 id="mobile_open_sign_up"><a href="sign_up_page.php">Sign Up<a></h3>';
      }
      ?>
      <div id="home_zipcode_explanation">
        <h3>Why do we ask for your postal code?</h3><br>
        <p>We use your postal code to calculate the right time for your local timezone to text you each day.</p>
      </div>
    </div>
    <div id="explanation">
      <div id="walking_cartoon_container">
        <img id="walking_cartoon" src="https://blush.design/api/download?shareUri=hZm2R9IK8&w=800&h=800&fm=png" alt="Cartoon of man on his phone walking happily">
      </div>
      <div id="explanation_text">
        <h2 id="exp_1">We text you one environmental action every day.</h2><br><br><br>
        <p id="exp_2">Together, our actions make a huge difference!</p>
      </div>
    </div>

      <?php
        //different writings for if 10+ actionshave been done or not
        if(addAllActions($conn) > 9){
          echo '
            <div id="total_statistic">
              <p id="statistic">The entire Footprint community has done '.addAllActions($conn).' actions for the environment.</p>
            </div>
          ';
        } else{
          echo '
            <div id="total_statistic">
              <p id="statistic">Let\'s start making a difference!</p>
            </div>
          ';
        }

       ?>
  </div>
  <?php
    include "footer.php";
   ?>
<script src="scripts/scripts.js"></script>
<script>

     var open_sign_up = document.getElementById('open_sign_up');
     var user_join_form = document.getElementById('user_join_form');
     var name_input = document.getElementById('name_input');
     var phone_input = document.getElementById('phone_input');
     var zipcode_input = document.getElementById('zipcode_input');

  //if user has tried entering stuff already, automatically show sign up form and error message when page reloads
  if(name_input.value != "" || phone_input.value != "" || zipcode_input.value != ""){
    user_join_form.style.opacity = 1;
    open_sign_up.style.opacity = 0;
  }else{
    user_join_form.style.opacity = 0;
    user_join_form.style.display = "none";
    open_sign_up.style.opacity = 1;
  }
  //as an extra check, if user join form is open for any reason make sure open sign up link is invisible.
  if(window.getComputedStyle(user_join_form).getPropertyValue("opacity") == 1){
    open_sign_up.style.opaity = 0;
  }

  var  user_join_form_opacity = 0;
  var open_sign_up_opacity = 0;
  //move div left for animation
  var makeOpaque = function(){
    user_join_form.style.display = "block";
    //if the opacity of the div is still below 1 increase opacity by 0.1 and keep calling this function for the animation
    if(user_join_form_opacity < 1){
      //increase opacity of sign up div and decrease opacity of link to open the div
      user_join_form_opacity += 0.05;
      open_sign_up_opacity -= 0.05;
      user_join_form.style.opacity = user_join_form_opacity;
      open_sign_up.style.opacity = open_sign_up_opacity;

      window.requestAnimationFrame(makeOpaque);
    }
  };
  //start the animation
  open_sign_up.addEventListener('click', makeOpaque);

//code for opening the explanation for why we ask for users' postal codes
  var home_zipcode_explanation = document.getElementById('home_zipcode_explanation');
  openDivs(why_zipcode_link, home_zipcode_explanation, 'block', true, user_join_form);
</script>
</body>
</html>
