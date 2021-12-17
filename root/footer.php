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
            <li><a href="'.$path.'admin.php">Admin</a></li>
          </ul>
      ';
    ?>
    </div>
  </div>
</div>
