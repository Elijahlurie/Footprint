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
  <title>Create Editorial Post</title>
  <meta name="viewport" content="width=device-width,initial-scale=1"/>
  <link rel="stylesheet" href="../styles/styles.css">
  <link rel="stylesheet" href="../styles/blog_post_styles.css">
  <!--script for rich text editor-->
  <script src="https://cdn.tiny.cloud/1/pqwdz7bddbd3ranupfkf3fghsfyr18540uxmv2kdc7w3jhub/tinymce/5/tinymce.min.js" referrerpolicy="origin"></script>
</head>

<body>
<?php
  include "../nav_tag.php";
 ?>
 <div id="editorial_page_wrapper">
   <h1>Create an Editorial Post</h1>
   <h3>--Just fill in the blanks--</h3>
   <?php
   //get an array of all blog posts from the database
   $new_sql = "SELECT * FROM blog;";
   $new_result = mysqli_query($conn, $new_sql);
   $new_result_array = mysqli_fetch_all($new_result, MYSQLI_ASSOC);
   //get the last item of that array, the information for the newest blog post
   $new_result_row = end($new_result_array);

   //display form with subbed in user inputs if user made an error
   //session variables with user inputs were set in createBlogPost()
   if($_SESSION['blog_post_error']){
     echo '<p>Error: '.$_SESSION['blog_post_error'].'</p>';
   }
     echo '
     <form method="POST" action="'.createBlogPost($conn).'">
       <div id="editorial_page_header_cont">
         <div id="editorial_page_header">
            <h1>'.$new_result_row["title"].'</h1>
            <br><input type="text" name="editorial_input_subtitle" value="'.$_SESSION['blog_post_input_0'].'" placeholder="Type subtitle here . . .">
         </div>
         <p id="editorial_page_image_path">../images/preview_images/'.$new_result_row["preview_image"].'</p>
       </div>
       <h2 class="blog_page_category_link">Editorial</h2>
       <div id="editorial_page_content">
        <div id="editorial_page_tags">
          <p class="editorial_page_tag">Written By</p>
          <p class="editorial_page_tag_content">'.$new_result_row["author"].'</p>
          <p class="editorial_page_tag">Published</p>
          <p class="editorial_page_tag_content">'.$new_result_row["date"].'</p>
          <p class="editorial_page_tag">Category</p>
          <p class="editorial_page_tag_content">'.$new_result_row["category"].'</p>
        </div>
        <div id="editorial_content_text">
          <textarea name="editorial_input_content" type="text" placeholder="Type editorial content here . . .">'.$_SESSION['blog_post_input_1'].'</textarea>
        </div>
       </div>
     <div id="editorial_submit_post_cont">
       <button name="submit_blog_post" type="submit">Create Post</button>
     </div>
   </form>
     ';

   ?>
 </div>
  <?php
    include "../footer.php";
   ?>
   <script src="../scripts/scripts.js"></script>
   <script>
   //set background image of header div
   var editorial_page_header = document.getElementById('editorial_page_header_cont');
   var editorial_page_image_path = document.getElementById('editorial_page_image_path');
   editorial_page_header.style.backgroundImage =  "url(" + editorial_page_image_path.innerHTML + ")";

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
