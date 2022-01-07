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
  <title>Create AOTD Post</title>
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
  <div id="aotd_page_wrapper">
    <h1>Create an Action Description Post</h1>
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
        <div id="aotd_page_header_cont">
          <div id="aotd_page_header">
            <div class="inline">
              <h1>By </h1><input name="aotd_input_header_action" type="text" value="'.$_SESSION['blog_post_input_0'].'" placeholder="[going vegan for one day]"><h1>. . .</h1>
            </div>
            <div>
              <input name="aotd_input_header_impact" type="text" value="'.$_SESSION['blog_post_input_1'].'" placeholder="[You\'ve saved an estimated 7 pounds of CO2, 14 pounds of grain, and 11 square feet of forestry. Keep reading to learn more!]">
            </div>
            <p><strong>Check the facts</strong></p>
          </div>
          <p id="aotd_page_image_path">../images/preview_images/'.$new_result_row["preview_image"].'</p>
        </div>
        <div id="aotd_page_stats_cont">
          <div class="inline">
            <h2>If everyone </h2>
            <input name="aotd_input_stats_action" type="text" value="'.$_SESSION['blog_post_input_2'].'" placeholder="[ate vegan]">
            <h2> today, we\'d save . . .</h2>
          </div>
          <div id="aotd_page_stats">
            <div>
              <input name="aotd_input_stats_number_1" type="number" value="'.$_SESSION['blog_post_input_3'].'" placeholder="100000000000">
              <input name="aotd_input_stats_unit_1" type="text" value="'.$_SESSION['blog_post_input_4'].'" placeholder="[Gallons of water]">
              <input name="aotd_input_stats_impact_1" type="text" value="'.$_SESSION['blog_post_input_5'].'" placeholder="[Enough to supply all of New England for 4 months.]">
            </div>
            <div>
              <input name="aotd_input_stats_number_2" type="number" value="'.$_SESSION['blog_post_input_5'].'" placeholder="150000000000">
              <input name="aotd_input_stats_unit_2" type="text" value="'.$_SESSION['blog_post_input_6'].'" placeholder="[Pounds of crops]">
              <input name="aotd_input_stats_impact_2" type="text" value="'.$_SESSION['blog_post_input_7'].'" placeholder="[Otherwise fed to livestock.]">
            </div>
            <div>
              <input name="aotd_input_stats_number_3" type="number" value="'.$_SESSION['blog_post_input_8'].'" placeholder="70000000">
              <input name="aotd_input_stats_unit_3" type="text" value="'.$_SESSION['blog_post_input_9'].'" placeholder="[Gallons of gas]">
              <input name="aotd_input_stats_impact_3" type="text" value="'.$_SESSION['blog_post_input_10'].'" placeholder="[Enough to fuel all the cars in California and Mexico.]">
            </div>
          </div>
          <h2>. . . And that\'s just in the USA!</h2>
        </div>
        <div id="aotd_form_content">
          <div id="aotd_content_text">
            <textarea name="aotd_input_content" type="text" value="'.$_SESSION['blog_post_input_11'].'" placeholder="Eating vegan cuts out one of the most pollutive, environmentally-unfriendly products that exist today..."></textarea>
          </div>
        </div>
      <p>By '.$new_result_row["author"].', '.$new_result_row["date"].'</p>
      <div id="aotd_submit_post_cont">
        <button name="submit_blog_post" type="submit">Create Post</button>
      </div>
    </form>
      ';

    ?>
  </div>
  <?php
    include "../footer.php";
   ?>
   <script src="scripts/scripts.js"></script>
   <script>
   //set background image of header div
   aotd_page_header = document.getElementById('aotd_page_header_cont');
   aotd_page_image_path = document.getElementById('aotd_page_image_path');
   aotd_page_header.style.backgroundImage =  "url(" + aotd_page_image_path.innerHTML + ")";


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
