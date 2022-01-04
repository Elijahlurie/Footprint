<!DOCTYPE html>
<?php
$time = date_default_timezone_set('America/Los_Angeles');
include "connection.php";
include "user_join.php";
include "blog_page_functions.php";
 ?>
<html>
<head>
  <meta charset="utf-8">
  <title>Blog</title>
  <meta name="viewport" content="width=device-width,initial-scale=1"/>
  <link rel="stylesheet" href="styles/styles.css">
</head>

<body>
  <div class="blog_page_container parallax">

    <div class="parallax_group">
    <?php
      include "nav_tag.php";
    ?>
      <div id="blog_header_container">
        <div id="blog_header_text">
          <h1>The Footprint Blog.</h1><br><br>
          <h3>Learn, read, grow.<h3>
        </div>
        <div id="blog_header_image">
          <img id="reading_doodle" src="https://blush.design/api/download?shareUri=cHOhAno8l&w=800&h=800&fm=png" alt="Doodle of character reading a book">
        </div>
      </div>
    </div>
    <div class="parallax_group">
      <div class="featured_previews_background parallax_layer">
      </div>
        <div class="featured_previews_container parallax_layer">
          <h1>Featured</h1>
          <hr>
          <div id="featured_previews">
            <?php
            //get all info on each of the featured blog posts
              $featured_posts_array = getFeaturedPosts($conn);
              //create an array of formatted previews for these posts
              $featured_previews_array = [];
              foreach($featured_posts_array as $featured_row){
                $featured_previews_array[] = formatPreview($featured_row, "");
              }
              foreach($featured_previews_array as $blog_post){
                echo $blog_post;
              }
            ?>
          </div>
        </div>
      </div>
      <div class="parallax_group">
          <div id="aotd_previews_cont">
            <div id="aotd_header">
              <div id="aotd_header_text">
                <h1>Action Deep Dives</h1>
                <p>What's the real-world impact of the actions you complete?</p>
              </div>
            </div>
            <div id="aotd_previews">
              <?php
                foreach(getPostsByCategory($conn, 'Action of the Day', 4) as $blog_post){
                  echo $blog_post;
                };
              ?>
            </div>
            <div id="aotd_category_link">
              <p><a href="category_pages/aotd.php">See More</a></p>
            </div>
          </div>
        <div class="news_previews_container">
          <div class="news_header">
            <h1>Climate News</h1>
            <hr>
            <div class="news_category_link">
              <a href="category_pages/news.php">See More >></a>
            </div>
          </div>
          <div id="news_previews">
            <?php
              foreach(getPostsByCategory($conn, 'Global Climate News', 3) as $blog_post){
                echo $blog_post;
              };
            ?>
          </div>
        </div>
        <!--Editorial section, uses same css classes as news section-->
        <div class="editorial_previews_container news_previews_container">
          <div class="news_header">
            <h1>Editorial</h1>
            <hr>
            <div class="news_category_link">
              <a href="category_pages/editorial.php">See More >></a>
            </div>
          </div>
          <div id="editorial_previews">
            <?php
              foreach(getPostsByCategory($conn, 'Editorial', 4) as $blog_post){
                echo $blog_post;
              };
            ?>
          </div>
        </div>
      <?php
        include "footer.php";
       ?>
   </div>
  </div>
   <script src="scripts/scripts.js"></script>
   <script src="scripts/preview_background.js"></script>
</body>
</html>
