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
  <title>Admin</title>
  <meta name="viewport" content="width=device-width,initial-scale=1"/>
  <link rel="stylesheet" href="../styles/styles.css">
  <!--script for rich text editor-->
  <script src="https://cdn.tiny.cloud/1/pqwdz7bddbd3ranupfkf3fghsfyr18540uxmv2kdc7w3jhub/tinymce/5/tinymce.min.js" referrerpolicy="origin"></script>
</head>

<body>
  <div id="pageWrapper">
    <?php
      include "../nav_tag.php";
  /*    if($specific_user['admin'] != 1){
        header("Location: index.php");
      }*/
      //unset old error messages for updating featured posts
      if(isset($_SESSION["update_featured_error_time"]) && time() - $_SESSION["update_featured_error_time"] >10){
        //if there is an error message set and it has been for over 10 seconds, unset the error message variables and reload the page
        unset($_SESSION["update_featured_error"]);
        unset($_SESSION["update_featured_error_time"]);
        header('Location: '.$_SERVER['REQUEST_URI']);
      }
      //unset old error messages for deleting posts
      if(isset($_SESSION['delete_post_message_time']) && time() - $_SESSION['delete_post_message_time'] >10){
        //if there is an error message set and it has been for over 10 seconds, unset the error message variables and reload the page
        unset($_SESSION['delete_post_message']);
        unset($_SESSION['delete_post_message_time']);
        header('Location: '.$_SERVER['REQUEST_URI']);
      }
     ?>
    <div id="admin_page_container">
      <h1>Admin</h1>
      <h2>Post a Blog Entry</h2>
      <?php
        echo '
          <form id="blog_form" method="POST" action="'.createBlogPreview($conn).'" enctype="multipart/form-data">
            <p>Category</p>
            <select name="blog_category">
              <option value="Action of the Day">Action of the Day</option>
              <option value="Global Climate News">Global Climate News</option>
              <option value="Editorial">Editorial</option>
            </select>
            <p>Image for Preview</p>
            <input name="blog_preview_image" type="file" accept="image/png, image/jpeg, image/jpg">
          ';
          if(isset($_SESSION['blog_preview_error'])){
            //fill in with session variable values from their previous input
            echo '
              <p>Author Name</p>
              <input name="blog_author" type="text" value="'.$_SESSION['blog_input_author'].'">
              <p>Title</p>
              <input name="blog_title" type="text" value="'.$_SESSION['blog_input_title'].'">
              <p>Description</p>
              <input name="blog_description" type="text" value="'.$_SESSION['blog_input_description'].'">
              <button name="submit_blog_preview" type="submit">Post</button>
            </form>

            <p>Error: '.$_SESSION['blog_preview_error'].'</p>
            ';
          } else{
            echo '
                <p>Author Name</p>
                <input name="blog_author" type="text">
                <p>Title</p>
                <input name="blog_title" type="text">
                <p>Description</p>
                <input name="blog_description" type="text">
                <button name="submit_blog_preview" type="submit">Post</button>
              </form>
            ';
          }
      ?>

      <h2>Update Featured Posts</h2>
      <?php
        $featured_posts_array = getFeaturedPosts($conn);

        echo '
          <form method="POST" action="'.updateFeaturedPosts($conn, $featured_posts_array).'">
            <ol>
          ';
        for ($i = 0; $i < count($featured_posts_array); $i++){
          echo '<li><input name="update_featured'.$i.'" type="text" value="'.$featured_posts_array[$i]['title'].'"></li>';
        }
        echo '
            </ol>
            <button type="submit" name="update_featured_submit">Update</button>
          </form>
          <div id="update_featured_error">'.$_SESSION["update_featured_error"].'</div>
        ';
      ?>
      <h2>Delete Blog Post</h2>
      <?php
        echo '
          <form method="POST" action="'.deletePost($conn).'">
            <h3>Post Title</h3>
            <input name="delete_post_title" type="text" placeholder="Title">
            <button name="delete_post_submit" type="submit">Delete Post</button>
          </form>
          <p>'.$_SESSION['delete_post_message'].'</p>
        ';
      ?>
    </div>
  </div>
  <?php
    include "../footer.php";
   ?>
   <script src="scripts/scripts.js"></script>
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
