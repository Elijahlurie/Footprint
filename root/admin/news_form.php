<!DOCTYPE html>
<?php
$time = date_default_timezone_set('America/Los_Angeles');
include "../connection.php";
include "../user_join.php";

//define $path variable so links inside nav tag and footer still point to the right page even though this file is in a folder
$path = "../";

//check if $_SESSION['created_preview'] variable was set by the createBlogPreview function; if it wasn't, redirect user from this page to admin page
if(!$_SESSION['created_preview']){
  header('Location:admin.php');
}
 ?>
<html>
<head>
  <meta charset="utf-8">
  <title>Create News Post</title>
  <meta name="viewport" content="width=device-width,initial-scale=1"/>
  <link rel="stylesheet" href="../styles/styles.css">
  <link rel="stylesheet" href="../styles/blog_post_styles.css">
  <!--script for rich text editor-->
  <script src="https://cdn.tiny.cloud/1/pqwdz7bddbd3ranupfkf3fghsfyr18540uxmv2kdc7w3jhub/tinymce/5/tinymce.min.js" referrerpolicy="origin"></script>
</head>

<body>
  <div id="pageWrapper">
    <?php
      include "../nav_tag.php";
     ?>
  <h1>news form page</h1>
  </div>
  <?php
    include "../footer.php";
   ?>
   <script src="../scripts/scripts.js"></script>
   <script>
   //code for initializing the rich text editor plugin
    tinymce.init({
      selector: 'textarea',
      plugins: 'a11ychecker advcode casechange export formatpainter linkchecker autolink lists checklist media mediaembed pageembed permanentpen powerpaste table advtable tinycomments tinymcespellchecker',
      toolbar: 'undo redo | bold italic underline | alignleft aligncenter alignright | fontsizeselect | indent outdent | bullist numlist',
      toolbar_mode: 'floating',
      tinycomments_mode: 'embedded',
      tinycomments_author: 'Author name',
    });
</script>
</body>
</html>
