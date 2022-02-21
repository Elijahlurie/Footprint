<script src="https://unpkg.com/akar-icons-fonts"></script>
<nav id="nav_tag">
  <?php
    //$path variable adds "../" before link paths for pages inside folders, or "" for pages not in any folder (variable defined on top of each page)
    //if $path has not been set assume it should equal ""
    if(!$path){
      $path = "";
    }
    echo '
      <h2 id="footprint_title"><a href="'.$path.'index.php">Footprint</a></h2>
      <a id="logo_nav_link" href="'.$path.'index.php">
        <img id="nav_logo" src="'.$path.'images/footprint_logo.png" alt="Footprint logo">
      </a>
      <div id="main_links">
        <h4><a href="'.$path.'index.php">Home</a></h4>
        <h4><a href="'.$path.'about_us.php">About</a></h4>
        <h4><a href="'.$path.'blog.php">Blog</a></h4>
      </div>
      <div id="nav_tag_menu_dropdown">
        <h4 id="nav_tag_menu_dropdown_h4">Menu &#9660</h4>
        <div id="dropdown_main_links">
          <a href="'.$path.'index.php"><h4>Home</h4></a>
          <a href="'.$path.'about_us.php"><h4>About</h4></a>
          <a href="'.$path.'blog.php"><h4>Blog</h4></a>
        </div>
      </div>
    ';

      $specific_user = getTheUser($conn);
      // if user is not logged in, have icon in corner with link to login page, otherwise have link to menu with all of the user links
        if(!isset($_SESSION['id'])){
          echo '<h3 id="login_link"><a href="'.$path.'login_page.php"><i class="ai-person"></i></a></h3>';
        } else if (isset($_SESSION['id'])){
          echo '
          <h3 id="hello_user">Hello, '.$specific_user['name'].'</h3>
          <div id="user_links">
            <div id="user_links_content">
              <ul>
              <li>
                <a href="'.$path.'user_page.php">Dashboard</a>
              </li>
              <li>
                <a href="'.$path.'edit_profile.php">Edit Profile</a>
              </li>
              <li>
                <form method="POST" action="'.logOut($path).'">
                  <button type="submit" name="logout">Log Out</button>
                </form>
              </li>
              </ul>
            </div>
          </div>


          <div id="delete_div_container">
            <div id="delete_div_content">
              <h3>Are you sure you want to delete your account?</h3>
              <form method="POST" action="'.deleteAccount($conn).'">
                <div id="cancel_delete" class="delete_confirmation">Cancel</div>
                <button class="delete_confirmation" type="submit" name="delete_account">Yes, Delete Account</button>
              </form>
            </div>
          </div>
          ';
        }
      ?>

</nav>
