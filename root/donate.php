<!DOCTYPE html>
<?php
$time = date_default_timezone_set('America/Los_Angeles');
include "connection.php";
include "user_join.php";

header('Location:index.php');
 ?>
<html>
<head>
  <meta charset="utf-8">
  <title>Donate</title>
  <meta name="viewport" content="width=device-width,initial-scale=1"/>
  <link rel="stylesheet" href="styles/styles.css">

</head>

<body>
  <?php
    include "nav_tag.php";
  ?>
  <div id="donation_page_wrapper">
    <div id="donation_header_cont">
      <div id="donation_header_text">
        <h1>Big text goes here here here</h1>
        <hr>
        <h2>Put in a lil subtext here maybe</h2>
      </div>
      <div id="donation_header_btn_cont">
        <form action="https://www.paypal.com/donate" method="post" target="_top">
          <input type="hidden" name="business" value="8QR2D2SNL6KHS" />
          <input type="hidden" name="no_recurring" value="0" />
          <input type="hidden" name="item_name" value="Your donation allows us to expand our impact and help people like you help the Earth. Thank you." />
          <input type="hidden" name="currency_code" value="USD" />
          <input type="image" src="https://www.paypalobjects.com/en_US/i/btn/btn_donate_LG.gif" border="0" name="submit" title="PayPal - The safer, easier way to pay online!" alt="Donate with PayPal button" />
          <img alt="" border="0" src="https://www.paypal.com/en_US/i/scr/pixel.gif" width="1" height="1" />
        </form>
      </div>
    </div>


  </div>
  <?php
    include "footer.php";
   ?>
   <script src="scripts/scripts.js"></script>
</body>
</html>
