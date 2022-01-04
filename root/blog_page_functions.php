<?php
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
          $newarray[] = formatPreview($posts_array[$i], "");
        } else{
          //if the corresponding file does not exist, delete the row from the database
          $file_name = $posts_array[$i]['file_name'];
          $delete_post_sql = "DELETE FROM blog WHERE file_name = '$file_name';";
          $delete_post_result = mysqli_query($connection, $delete_post_sql);
          header("Location: blog.php");
        }
      endif;
    }
    return $newarray;
  };

  //get all the blog posts for a specified category and echo the [number specified] most recent posts formatted in html .
  function getPostsByCategory($connection, $categoryWanted, $number){
    //get an array of all blog posts in requested category
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
           $newarray[] = formatPreview($category_posts[$i],$categoryWanted);;
         } else{
           //if the corresponding file does not exist, delete the row from the database
           $file_name = $category_posts[$i]['file_name'];
           $delete_post_sql = "DELETE FROM blog WHERE file_name = '$file_name';";
           $delete_post_result = mysqli_query($connection, $delete_post_sql);
           header("Location: blog.php");
         }
       endif;
     }
     return $newarray;
 };


 //Separate function for formatting previews into html so I dont't have to have separate functions for each category
 //a single row from blog table is inputted, function outputs a formatted html preview for the row
 function formatPreview($blog_post_row, $category){
 	if($blog_post_row['preview_image']){
 	//if post has an image file for its preview, include the image in the preview
 		$image_html = '<img class = "preview_image" src="images/preview_images/'.$blog_post_row['preview_image'].'" alt="Preview image">';
 	} else{
 		$image_html = "";
 	}

//format the preview html depending on what section of the blog.php page it will be displayed on
  if($category == "Action of the Day"){
    $preview= '
   			<div class="aotd_post_preview">
   				<a href="blog_posts/'.$blog_post_row['file_name'].'">
   					<div class="aotd_preview_image_cont">
              <p class="aotd_image_path">images/preview_images/'.$blog_post_row['preview_image'].'</p>
   					</div>
   					<div class="aotd_preview_content">
   						<h2>'.$blog_post_row['title'].'</h2>
   					</div>
   				</a>
   			</div>
   	';
  } else if($category == "Global Climate News"){
    $preview= '
   			<div class="news_post_preview">
          <a class="news_preview_image_cont"href="blog_posts/'.$blog_post_row['file_name'].'">
   					<p class="news_image_path">images/preview_images/'.$blog_post_row['preview_image'].'</p>
          </a>
   				<div class="news_preview_content">
   					<div class="news_preview_title_cont">
              <a href="blog_posts/'.$blog_post_row['file_name'].'">
							  <h2>'.$blog_post_row['title'].'</h2>
              </a>
   					</div>
   					<div class="news_preview_desc_cont">
   						<p>'.$blog_post_row['description'].'</p>
   					</div><br>
            <div class="news_preview_author_date_cont">
   						<p>'.$blog_post_row['author'].', '.$blog_post_row['time'].'</p>
   					</div>
   				</div>
   			</div>
   	';
  } else if($category == "Editorial"){
    $preview= '
   			<div class="editorial_post_preview">
   				<a href="blog_posts/'.$blog_post_row['file_name'].'">
   					<div class="editorial_preview_image_cont">
   						<p class="editorial_image_path">images/preview_images/'.$blog_post_row['preview_image'].'</p>
   					</div>
   					<div class="editorial_preview_content">
              <div class="editorial_author_date_cont">
   							<p>'.$blog_post_row['author'].'</p>
   							<p>'.$blog_post_row['time'].'</p>
              </div>
   						<div class="editorial_title_cont">
   							<h2>'.$blog_post_row['title'].'</h2>
   						</div>
   						<div class="editorial_desc_cont">
   							<p>'.$blog_post_row['description'].'</p>
   						</div>
   					</div>
   				</a>
   			</div>
   	';
  } else{
    //if no category was specified, format html for featured posts section
    $preview= '
   			<div class="blog_post_preview">
   				<a href="blog_posts/'.$blog_post_row['file_name'].'">
   					<div class="preview_image_cont">
   						<p class="image_path">images/preview_images/'.$blog_post_row['preview_image'].'</p>
   					</div>
   					<div class="preview_content">
   						<div class="preview_author_date_cont">
   							<p>'.$blog_post_row['author'].'</p>
   							<p>'.$blog_post_row['time'].'</p>
   						</div>
   						<div class="preview_title_cont">
   							<h2>'.$blog_post_row['title'].'</h2>
   						</div>
   						<div class="preview_desc_cont">
   							<p>'.$blog_post_row['description'].'</p>
   						</div>
   					</div>
   				</a>
   			</div>
   	';
  }

 	return $preview;
 };
