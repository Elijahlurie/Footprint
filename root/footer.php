<?php
//for category pages, if a post variable has been set with a path variable, set $path to it so that footer links work
if($_POST['cat_page_path']){
  $path = $_POST['cat_page_path'];
}
 ?>
<div id="footer">
  <div id="footer_logo_container">
    <?php
    //$path variable adds "../" before link paths for pages inside folders, or "" for pages not in any folder (variable defined on top of each page or in nav tag)

      echo '
        <img id="footer_logo" src="'.$path.'images/footprint_logo.png" alt="Footprint logo">
      </div>
      <div id="footer_content">
        <div id="footer_title_container">
          <h1>Footprint</h1>
        </div>
        <hr>
        <div id="footer_content_text">
          <ul>
            <li><a href="'.$path.'sign_up_page.php">Sign Up</a></li>
            <li><a href="'.$path.'about_us.php">About Footprint</a></li>
            <li><a href="mailto: elijahlurie@berkeley.edu?subject=Footprint Question/Comment">Contact us</a></li>
            <li><a href="'.$path.'admin/admin.php">Admin</a></li>
          </ul>
      ';
    ?>
    </div>
  </div>
</div>
