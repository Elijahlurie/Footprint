<?php
include "connection.php";
$path = "../";
//include a certain amount of previews per 'page' worth of content
  $per_page =6;
//get the next page worth of blog previews from blog table to display
function getPagePreviews($connection, $page_num, $per_page, $requested_category) {
    //if page_num is not set, assume user is on the 1st page
    if(!isset($page_num)){
      $page_num = 1;
    }
    //calculate where in array of blog posts to start getting a new page worth of previews
    $page_start = ($page_num - 1) * $per_page;
    $sql = "SELECT * FROM blog WHERE category = '".$requested_category."' ORDER BY time DESC LIMIT $page_start,$per_page;";
    $result = mysqli_query($connection, $sql);
    $page_previews = mysqli_fetch_all($result, MYSQLI_ASSOC);
    return $page_previews;
}

//if a server request has been made, format each of the new page worth of previews into html
  if (isset($_POST['page_num'])):
    $requested_category = $_POST['cat_page_category'];
    $page_previews = getPagePreviews($conn, $_POST['page_num'], $per_page, $requested_category);
    $formatted_previews = [];
    $footer_exists = 0;
    foreach($page_previews as $preview){
      //append the next preview to the formated_previews array
      $formatted_previews[] =
          '
         			<div class="category_page_preview">
         				<a href="../blog_posts/'.$preview['file_name'].'">
         					<div class="cat_preview_image_cont">
                    <div class="cat_preview_desc">
                      <p>'.$preview['description'].'</p>
                    </div>
         						<p class="cat_image_path">../images/preview_images/'.$preview['preview_image'].'</p>
         					</div>
         					<div class="preview_content">
         						<div>
         							<p>'.$preview['author'].'</p>
         							<p class>'.$preview['date'].'</p>
         						</div>
         						<div>
         							<h2>'.$preview['title'].'</h2>
         						</div>
         					</div>
         				</a>
         			</div>
         	';
        //set $footer_exists to 0 because footer is not being echoed here
          $footer_exists = 0;
    }
    //get an array of all blog posts in database for requested category

    $all_cat_posts_sql = "SELECT * FROM blog WHERE category = '".$requested_category."' ORDER BY time DESC;";
    $all_cat_posts_result = mysqli_query($conn, $all_cat_posts_sql);
    $all_cat_posts_array = mysqli_fetch_all($all_cat_posts_result, MYSQLI_ASSOC);
    if(end($page_previews)['id'] == end($all_cat_posts_array)['id']){
      //if the id of the last formatted preview is the id of the very last post for this category, signal that footer should be inserted
      $footer_exists = 1;
    }
    //return an array to jquery containing an array of formatted previews and either 0 or 1 depending on if footer should or should not be echoed by jquery
    echo json_encode([$formatted_previews, $footer_exists]);
  endif;
?>
