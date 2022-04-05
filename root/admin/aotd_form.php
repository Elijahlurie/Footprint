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

    if($specific_user['admin'] != 1){
      header("Location: index.php");
    }

    //check if $_SESSION["new_post_data"] variable was set by the createBlogPreview function; if it wasn't, redirect user from this page to admin page
    if(!$_SESSION["new_post_data"]){
      header('Location:admin.php');
    }
   ?>
  <div id="aotd_page_wrapper">
    <div id="aotd_form_instructions">
      <h1>Create an Action Description Post</h1>
      <h3>--Just fill in the blanks--</h3>
    </div>
    <?php
    //display form with subbed in user inputs if user made an error
    //session variables with user inputs were set in createBlogPost()
    if($_SESSION['blog_post_error']){
      echo '<p>Error: '.$_SESSION['blog_post_error'].'</p>';
    }
      echo '
      <form method="POST" action="'.createBlogPost($conn).'">
        <div id="aotd_page_header_cont">
          <div id="aotd_page_header">
            <div id="aotd_page_header_h1">
              <h1>By </h1><input name="aotd_input_header_action" type="text" value="'.$_SESSION['blog_post_input_0'].'" placeholder="[going vegan for one day]"><h1>. . .</h1>
            </div>
            <div id="aotd_page_header_subtext">
              <input name="aotd_input_header_impact" type="text" value="'.$_SESSION['blog_post_input_1'].'" placeholder="[You\'ve saved an estimated 7 pounds of CO2, 14 pounds of grain, and 11 square feet of forestry. Keep reading to learn more!]">
            </div>
            <div id="aotd_check_facts_btn_cont">
              <strong>Check the facts</strong>
            </div>
          </div>
          <p id="aotd_page_image_path">../images/preview_images/'.$_SESSION["new_post_data"]["preview_image"].'</p>
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
              <input name="aotd_input_stats_number_2" type="number" value="'.$_SESSION['blog_post_input_6'].'" placeholder="150000000000">
              <input name="aotd_input_stats_unit_2" type="text" value="'.$_SESSION['blog_post_input_7'].'" placeholder="[Pounds of crops]">
              <input name="aotd_input_stats_impact_2" type="text" value="'.$_SESSION['blog_post_input_8'].'" placeholder="[Otherwise fed to livestock.]">
            </div>
            <div>
              <input name="aotd_input_stats_number_3" type="number" value="'.$_SESSION['blog_post_input_9'].'" placeholder="70000000">
              <input name="aotd_input_stats_unit_3" type="text" value="'.$_SESSION['blog_post_input_10'].'" placeholder="[Gallons of gas]">
              <input name="aotd_input_stats_impact_3" type="text" value="'.$_SESSION['blog_post_input_11'].'" placeholder="[Enough to fuel all the cars in California and Mexico.]">
            </div>
          </div>
          <h2>. . . And that\'s just in the USA!</h2>
        </div>
        <h2 class="blog_page_category_link">Action Description Posts</h2>
        <div id="aotd_form_content">
          <div id="aotd_content_text">
            <textarea name="aotd_input_content" type="text" placeholder="Eating vegan cuts out one of the most pollutive, environmentally-unfriendly products that exist today...">'.$_SESSION['blog_post_input_12'].'</textarea>
          </div>
          <em id="aotd_form_author">&nbsp&nbsp&nbsp&nbsp&nbsp- '.$_SESSION["new_post_data"]["author"].', '.$_SESSION["new_post_data"]["date"].'</em>
        </div>
      <div id="blog_sources_cont">
        <h1>Sources</h1>
        <hr>
        <ul id="blog_sources">
        </ul>
        <p id="add_source_btn">Add a Source</p>
        <input name="sources_count" id="sources_count" value="0">
      </div>
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
   <script src="../scripts/scripts.js"></script>
   <script>
   //set background image of header div
   var aotd_page_header = document.getElementById('aotd_page_header_cont');
   var aotd_page_image_path = document.getElementById('aotd_page_image_path');
   aotd_page_header.style.backgroundImage =  "url(" + aotd_page_image_path.innerHTML + ")";

   //allow user to add source inputs by pressing add source button
   var add_source_btn = document.getElementById('add_source_btn');
   add_source_btn.addEventListener('click', addSourceInput);

   //code for initializing the rich text editor plugin
    tinymce.init({
      selector: 'textarea',
      plugins: 'a11ychecker advcode casechange export formatpainter linkchecker autolink lists checklist media mediaembed pageembed permanentpen powerpaste table advtable tinycomments tinymcespellchecker',
      toolbar: 'undo redo | bold italic underline | alignleft aligncenter alignright | fontsizeselect | indent outdent | bullist numlist',
      toolbar_mode: 'floating',
      tinycomments_mode: 'embedded',
      tinycomments_author: 'Author name',
      invalid_styles: 'font-size font-family font-weight font-style font-variant-ligatures font-variant-caps font-variant-east-asian font-variant-position'
    });
</script>
</body>
</html>
