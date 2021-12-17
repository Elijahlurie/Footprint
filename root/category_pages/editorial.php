<!DOCTYPE html>
<?php
$time = date_default_timezone_set('America/Los_Angeles');
include "../connection.php";
include "../user_join.php";

//define $path variable so links inside nav tag and footer still point to the right page even though this file is in a folder
$path = "../";
 ?>
<html>
<head>
  <meta charset="utf-8">
  <title>Editorial</title>
  <meta name="viewport" content="width=device-width,initial-scale=1"/>
  <link rel="stylesheet" href="../styles/styles.css">

</head>

<body>
  <?php
    include "../nav_tag.php";
  ?>
  <div id="pageWrapper">
    <h1>Editorial Posts</h1>
  </div>
  <?php
    include "../footer.php";
   ?>
   <script src="../scripts/scripts.js"></script>
</body>
</html>
