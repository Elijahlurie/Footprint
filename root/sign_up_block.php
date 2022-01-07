<?php

//if the time since the session variables with the users previous inputs is over a minute, unset the variables because they arent needed.
  if(time() - $_SESSION['input_timestamp'] > 60){
    unset($_SESSION['input_name']);
    unset($_SESSION['input_zipcode']);
    unset($_SESSION['input_phone']);
    unset($_SESSION['input_timestamp']);
  }

echo '
  <div id="user_join_form">
    <div id="sign_up_title_container">
      <h3 id="sign_up_form_title">Sign Up</h3>
    </div>
    <div id="sign_up_form_content">
      <form method="POST" action = "'.addUsers($conn).'">
        <div id="name_phone_inputs">
          <div class="input_container">
            <h4>First name</h4>
            <input  id="name_input" class="form_input" type="text" name="name" value="'.$_SESSION['input_name'].'" placeholder="First Name">
          </div>
          <div class="input_container">
            <h4>Phone number</h4>
            <div id="phone_input_container">
              <input  id="phone_input" class="form_input" type="text" name="phone" value="'.$_SESSION['input_phone'].'" placeholder="(xxx) xxx-xxxx">
              <p id="country_code">+1</p>
            </div>
          </div>
        </div>
        <div class="input_container">
          <h4>Postal Code</h4>
          <input  id="zipcode_input" class="form_input" type="text" name="zipcode" value="'.$_SESSION['input_zipcode'].'" placeholder="Postal Code">
        </div>
        <p id="why_zipcode_link">Why do we ask for this?</p>
        <div class="input_container">
          <h4>Password</h4>
          <input id="password_input" class="form_input" type="text" name="password" placeholder="Min. 8 Characters">
        </div>
        <p id="signup_error">'.$_SESSION['signup_error'].'</p>
        <button id="submit_user" type="submit" name="user_submit">Ready!</button>
      </form>
    </div>
  </div>
  ';


?>
