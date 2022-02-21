<!DOCTYPE html>
<?php
$time = date_default_timezone_set('America/Los_Angeles');
include "../connection.php";
include "../user_join.php";
include "../blog_page_functions.php";
//define $path variable so links inside nav tag and footer still point to the right page even though this file is in a folder
$path = "../";
 ?>
<html>
<head>
  <meta charset="utf-8">
  <title>Action Deep Dives</title>
  <meta name="viewport" content="width=device-width,initial-scale=1"/>
  <link rel="stylesheet" href="../styles/styles.css">

</head>

<body>
  <?php
    include "../nav_tag.php";
  ?>
  <div id="cat_page_category">Action Deep Dives</div>
  <div id="cat_page_wrapper">
    <h1 id="cat_page_header">Action Deep Dives</h1>
    <div id="all_previews_cont">
    </div>
    <div id="ajax_loader">
      <h4>Loading Blog Posts . . .</h4>
    </div>
  </div>

   <script src="../scripts/scripts.js"></script>
   <script src="../scripts/background_image.js"></script>
   <!--jquery cdn-->
   <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
   <script src="../scripts/infinite_scroll.js"></script>
</body>
</html>
