<!DOCTYPE html>
<?php
$time = date_default_timezone_set('America/Los_Angeles');
include "connection.php";
include "user_join.php";
 ?>
<html>
<head>
  <meta charset="utf-8">
  <title>Admin</title>
  <meta name="viewport" content="width=device-width,initial-scale=1"/>
  <link rel="stylesheet" href="styles/styles.css">
  <script src="https://cdn.tiny.cloud/1/pqwdz7bddbd3ranupfkf3fghsfyr18540uxmv2kdc7w3jhub/tinymce/5/tinymce.min.js" referrerpolicy="origin"></script>
</head>

<body>
  <div id="pageWrapper">
    <?php
      include "nav_tag.php";
  /*    if($specific_user['admin'] != 1){
        header("Location: index.php");
      }*/
     ?>
    <div id="admin_page_container">
      <h1>Admin</h1>
      <h2>Blog Form</h2>
      <?php
        echo '
          <form id="blog_form" method="POST" action="'.blogPost($conn).'">
            <p>Category</p>
            <select name="blog_category">
              <option value="Action of the Day">Action of the Day</option>
              <option value="Global Climate News">Global Climate News</option>
              <option value="Editorial">Editorial</option>
            </select>
          ';
          if(isset($_SESSION['blog_post_error'])){
            //fill in with session variable values from their previous input
            echo '
              <p>Author Name</p>
              <input name="blog_author" type="text" value="'.$_SESSION['blog_input_author'].'">
              <p>Title</p>
              <input name="blog_title" type="text" value="'.$_SESSION['blog_input_title'].'">
              <p>Description</p>
              <input name="blog_description" type="text" value="'.$_SESSION['blog_input_description'].'">
              <p>Content:</p>
              <textarea name="blog_content" type="text">'.$_SESSION['blog_input_content'].'</textarea>
              <button name="submit_blog" type="submit">Post</button>
            </form>

            <p>Error: '.$_SESSION['blog_post_error'].'</p>
            ';
          } else{
            echo '
                <p>Author Name</p>
                <input name="blog_author" type="text">
                <p>Title</p>
                <input name="blog_title" type="text">
                <p>Description</p>
                <input name="blog_description" type="text">
                <p>Content:</p>
                <textarea name="blog_content" type="text">Write content here</textarea>
                <button name="submit_blog" type="submit">Post</button>
              </form>
            ';
          }


      ?>

    </div>
  </div>
  <?php
    include "footer.php";
   ?>
   <script src="scripts/scripts.js"></script>
   <script>
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
