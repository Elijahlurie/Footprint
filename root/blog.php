<!DOCTYPE html>
<?php
$time = date_default_timezone_set('America/Los_Angeles');
include "connection.php";
include "user_join.php";
 ?>
<html>
<head>
  <meta charset="utf-8">
  <title>Blog</title>
  <meta name="viewport" content="width=device-width,initial-scale=1"/>
  <link rel="stylesheet" href="styles/styles.css">

</head>

<body>
  <?php
    //have a separate function for formatting previews into html so I dont't have to repeat the code in multiple functions
    //a single row from blog table is inputted, function outputs a formatted html preview for the row
    function formatPreview($blog_post_row){
      if($blog_post_row['preview_image']){
      //if post has an image file for its preview, include the image in the preview
        $image_html = '<img src="images/preview_images/'.$blog_post_row['preview_image'].'" width="200px" alt="Preview image">';
      } else{
        $image_html = "";
      }

      $preview= '
        <div class="blog_post_preview">
          <h2>'.$blog_post_row['title'].'</h2>
          <p>Author: '.$blog_post_row['author'].'</p>
          <p>Published: '.$blog_post_row['time'].'</p>
          <p>Description: '.$blog_post_row['description'].'</p>
          <p><a href="blog_posts/'.$blog_post_row['file_name'].'">Link to post</a></p>
          '.$image_html.'
        </div>
      ';
      return $preview;
    };
    //get the full list of blog posts and return an array of formatted html blog posts
    function getBlogPosts($connection, $number){
    	$sql = "SELECT * FROM blog ORDER BY time DESC;";
    	$result = mysqli_query($connection, $sql);
    	$posts_array = mysqli_fetch_all($result, MYSQLI_ASSOC);
      $newarray = [];
      for($i = 0; $i<$number; $i++){
        //if there is a blog post in the array (maybe there arent enough posts in this category yet)
        if($posts_array[$i]):
          //if the corresponding file exists
          $file_path = 'blog_posts/'.$posts_array[$i]['file_name'];
          if(file_exists($file_path)){
            //append another preview html to the array so it can be printed in the page
            $newarray[] = formatPreview($posts_array[$i]);
          } else{
            $file_name = $posts_array[$i]['file_name'];
            //if the corresponding file does not exist, delete the row from the database
            $delete_post_sql = "DELETE FROM blog WHERE file_name = '$file_name';";
            $delete_post_result = mysqli_query($connection, $delete_post_sql);
          }
        endif;
      }
      return $newarray;
    };

    //get all the blog posts for a specified category and echo the [number specified] most recent posts formatted in html .
    function getPostsByCategory($connection, $categoryWanted, $number){
       $sql = "SELECT * FROM blog WHERE category = '$categoryWanted' ORDER BY time DESC;";
       $result = mysqli_query($connection, $sql);
       $category_posts = mysqli_fetch_all($result, MYSQLI_ASSOC);
       //create an array to store all the previews created in this function
       $newarray = [];
       //for the specified number of times
       for($i = 0; $i<$number; $i++){
         //if there is a blog post in the array (maybe there arent enough posts in this category yet)
         if($category_posts[$i]):
           //if the corresponding file exists
           $file_path = 'blog_posts/'.$category_posts[$i]['file_name'];
           if(file_exists($file_path)){
             //append another preview html to the array so it can be printed in the page
             $newarray[] = formatPreview($category_posts[$i]);;
           } else{
             $file_name = $category_posts[$i]['file_name'];
             //if the corresponding file does not exist, delete the row from the database
             $delete_post_sql = "DELETE FROM blog WHERE file_name = '$file_name';";
             $delete_post_result = mysqli_query($connection, $delete_post_sql);
           }
         endif;
       }
       return $newarray;
   };

  ?>
  <div class="blog_page_container parallax">

    <div class="parallax_group">
    <?php
      include "nav_tag.php";
    ?>
      <div class="spacer"></div>
    </div>
    <div class="parallax_group">
        <div class="featured_previews_background parallax_layer">
        </div>
        <div class="featured_previews_container parallax_layer">
          <h1>Blog</h1>
          <div id="featured_previews">
            <?php
              foreach(getBlogPosts($conn, 3) as $blog_post){
                echo $blog_post;
              }
            ?>
          </div>
        </div>
      </div>
      <div class="parallax_group">
      <div id="aotd_previews_container">
        <h1>Action Description Posts</h1>
        <div class="previews_container">
          <?php

            foreach(getPostsByCategory($conn, 'Action of the Day', 3) as $blog_post){
              echo $blog_post;
            };
          ?>
        </div>
        <p><a href="category_pages/aotd.php">See More</a></p>
      </div>
      <div id="news_previews_container">
        <h1>Global News Posts</h1>
        <div class="previews_container">
          <?php
            foreach(getPostsByCategory($conn, 'Global Climate News', 3) as $blog_post){
              echo $blog_post;
            };
          ?>
        </div>
        <p><a href="category_pages/news.php">See More</a></p>
      </div>
      <div id="editorial_previews_container">
        <h1>Editorial Posts</h1>
        <div class="previews_container">
          <?php
            foreach(getPostsByCategory($conn, 'Editorial', 3) as $blog_post){
              echo $blog_post;
            };
          ?>
        </div>
        <p><a href="category_pages/editorial.php">See More</a></p>
      </div>
      <?php
        include "footer.php";
       ?>
     </div>
  </div>
   <script src="scripts/scripts.js"></script>
</body>
</html>
