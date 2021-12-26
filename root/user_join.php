<script>
	/* If browser back button was used, reload page so logged out user cannot go back and see user information from previous pages*/
	var reloadOnPageBack = function () {
	  window.onpageshow = function(event) {
	    if (event.persisted) {
	      window.location.reload();
	    }
	  };
	};
	reloadOnPageBack();
</script>

<?php
//include 'cron_functions.php';


$sql = "SELECT * FROM users;";
$result = mysqli_query($conn, $sql);
$array_users = mysqli_fetch_all($result, MYSQLI_ASSOC);


//given a zipcode, use Google geocode API to return the latitude and longitude
function getCoordinates($zip){
	$url = "https://maps.googleapis.com/maps/api/geocode/json?address=".urlencode($zip)."&key=AIzaSyBvgdF6Zqi8ax5CXNx4--PI00XqISwvFek";
	$result_string = file_get_contents($url);
	$result = json_decode($result_string, true);
	$result1[]=$result['results'][0];
	$result2[]=$result1[0]['geometry'];
	$result3[]=$result2[0]['location'];
	return $result3[0];
};

//given coordinates and a number of seconds since Jan 1, 1970, return the timzone offset from UTC
function getTimezone($timestamp, $coordinates){
	$timezone_url = "https://maps.googleapis.com/maps/api/timezone/json?location=".$coordinates['lat'].",".$coordinates['lng']."&timestamp=".$timestamp."&key=AIzaSyBvgdF6Zqi8ax5CXNx4--PI00XqISwvFek";
	$timezone_result_string = file_get_contents($timezone_url);
	$timezone_result = json_decode($timezone_result_string, true);
	//return the offset from UTC time, taking daylight savins into account by adding the raw offset from UTC (in seconds) to the daylight savings offset and converting to hours
	$offset = ($timezone_result['rawOffset'] + $timezone_result['dstOffset']) / 3600;
	return $offset;
};

//sends user info from user join form to the users table in database
function addUsers($connection){
  if(isset($_POST['user_submit'])):
		//unset session signup error variable in case it was already set
		unset($_SESSION['signup_error']);
    //save the inputs in a session so if their inputs were wrong the computer can remember what they had so they dot start from scratch
    $_SESSION['input_name'] = $_POST['name'];
    $_SESSION['input_zipcode'] = $_POST['zipcode'];
		$_SESSION['input_phone'] =  $_POST['phone'];
    $_SESSION['input_timestamp'] = time();

    if($_POST['name'] != "" && $_POST['phone'] != "" && $_POST['zipcode'] != ""){
      //get an array of the current list of users
      $get_users_sql = "SELECT * FROM users;";
      $get_users_result = mysqli_query($connection, $get_users_sql);
      $users_array = mysqli_fetch_all($get_users_result, MYSQLI_ASSOC);
      //give a randm number within length of actions array
      $actions_sql = "SELECT * FROM actions;";
      $actions_result = mysqli_query($connection, $actions_sql);
      $array_actions = mysqli_fetch_all($actions_result, MYSQLI_ASSOC);
      $rand = mt_rand(0,(count($array_actions)-1));
      //sanitize name input
      $no_space_name = str_replace(" ", "", $_POST['name']);
      $name = ucfirst(strtolower(filter_var($no_space_name, FILTER_SANITIZE_STRING)));

			//remove all special characters other than numbers from phone and add +1 to start
			$no_space_phone = str_replace(" ", "", $_POST['phone']);
			$phone = preg_replace("/[^0-9,.]/", "", $no_space_phone);
			$phone = '+1' . $phone;

			//remove all special characters other than numbers from zip code
			$no_space_zipcode = str_replace(" ", "", $_POST['zipcode']);
			$zipcode = preg_replace("/[^0-9,.]/", "", $no_space_zipcode);

			//call getCoordinates() function to get latitude and longitude from zipcode
			$location = getCoordinates($zipcode);
			$timestamp = time();
			//call getTimezone() function to get time zone from the coordinates we just got
		  $timezone = getTimezone($timestamp,$location);

			//multiply by 100 for storage convenience if there is a timezone offset that isn't a whole number
			$timezone_stored = $timezone * 100;

			//get current seconds since jan 1 1970 in the users time zone and convert to nearest whole days.
	    $user_curr_time = time() + 3600 * ($timezone_stored/100);
	    $user_days = ($user_curr_time/86400) - ($user_curr_time%86400)/86400;

			if(strlen($phone) == 11){
				if(strlen($zipcode)== 5){
					//check if there is a duplicate phone number
					$i = 0;
					//go through array of users, if there are any matches add one to counter
					foreach($users_array as $user):
						if($phone == $user['phone']){
							$i+=1;
						}
					endforeach;
					if($i == 0){
						//if there is not a duplicate insert user into database
						$sql = "INSERT INTO users (name, phone, texted, curr_action, yesterday, timezone, zipcode, last_completed_action) VALUES ('$name','$phone',0,'$rand', '$user_days', '$timezone_stored', '$zipcode', '$user_days');";
						$result = mysqli_query($connection, $sql);

						//run this sql again to get the updated list of users
						$get_users_result = mysqli_query($connection, $get_users_sql);
						$users_array = mysqli_fetch_all($get_users_result, MYSQLI_ASSOC);

						//count the amount of users now and use that number to find the last user in our array of users, then get that user (the new user) 's id.
						$count_users = count($users_array);
						$new_session_id = $users_array[$count_users-1]['id'];
						$curr_action = $users_array[$count_users-1]['curr_action'];

						//add a row to past_actions table for the user to track the actions they'e had and not give duplicates and start the row off by filling in today's action for the day0 column
						$past_action_table_sql = "INSERT INTO past_actions (user_id, day0) VALUES ($new_session_id, $curr_action);";
						$past_action_table_result = mysqli_query($connection, $past_action_table_sql);

						//unset the session values for what they inputted because they're not needed anymore
						unset($_SESSION['input_name']);
				    unset($_SESSION['input_zipcode']);
						unset($_SESSION['input_phone']);
				    unset($_SESSION['input_timestamp']);

						//log in user
						$_SESSION['id'] = $new_session_id;
						$_SESSION['timestamp'] = time();
	         //redirect user to user page
					 header("Location: user_page.php");
					//store errors in session variables to be used in signup_error <p> in user join form
					} else{
						$_SESSION['signup_error'] = "A user with this phone number already exists.";
					}
				} else{
					$_SESSION['signup_error'] = "Postal code must be 5 digits.";
				}
			} else{
				$_SESSION['signup_error'] = "Phone number length is invalid.";
			}
    } else if($_POST['name'] == "" && $_POST['phone'] == "" && $_POST['zipcode'] == ""){
			//if all three inputs are empty don't even show an error message and unset sign up error session variable so sign up forms recet to default placeholders
			unset($_SESSION['signup_error']);
		}else{
      $_SESSION['signup_error'] = "An input is still empty.";
    }
  endif;
};

//if a user has been texted button is set when page reloads and it hasn't been
//pressed yet today, set texted time to time() for that user
foreach($array_users as $user){
  $button_name = 'texted_'.$user["id"];
  if($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST[$button_name]) && $user['texted'] == 0){
    $new_sql = "UPDATE users SET texted = ".time()." WHERE id=".$user['id'].";";
    $new_result = mysqli_query($conn, $new_sql);
    header('Location: '.$_SERVER['REQUEST_URI']);
  }
}


//Login for users:
//adds 1 to $i for each user in database that the input doesn't match,
//if $i equals the total users in database(meaning input dosnt match any user),
//then outputs "user not found"
//redierect user to user page
function loginUsers($connection){
  $sql = "SELECT * FROM users;";
  $result = mysqli_query($connection, $sql);
  $array_users = mysqli_fetch_all($result, MYSQLI_ASSOC);
  if(isset($_POST['login_submit'])){
    $i = 0;
    $no_space_name = str_replace(" ", "", $_POST['login_name']);
    $name = strtolower(filter_var($no_space_name, FILTER_SANITIZE_STRING));
    $no_space_number = str_replace(" ", "", $_POST['login_phone']);
    $phone = preg_replace("/[^0-9,.]/", "", $no_space_number);
		$phone = '+1' . $phone;
    foreach($array_users as $user):
      if(strtolower($user['name']) == $name && $user['phone'] == $phone){
          $_SESSION['id'] = $user['id'];
          $_SESSION['timestamp'] = time();
          header("Location: user_page.php");
      } else{
        $i += 1;
      }
    endforeach;
    if($i === count($array_users)){
      $_SESSION['login_error'] = "User not found";
    }
  }
};

//when logout button is pressed end the session and reload the page
function logOut(){
  if(isset($_POST['logout'])){
    session_unset();
    session_destroy();
    header('Location: index.php');
  }
};

//lets users change their profiles
function editProfile($connection){
  if(isset($_POST['edit_profile_submit'])):
    if($_POST['edit_name'] != "" && $_POST['edit_phone'] != ""){
      $no_space_name = str_replace(" ", "", $_POST['edit_name']);
      $name = ucfirst(strtolower(filter_var($no_space_name, FILTER_SANITIZE_STRING)));
      //remove all special characters other than numbers from phone number with country code added
      $no_space_number = str_replace(" ", "", $_POST['edit_phone']);
      $phone = preg_replace("/[^0-9,.]/", "", $no_space_number);
			$phone = '+' . $phone;
			//remove all special characters other than numbers from zip code
			$no_space_zipcode = str_replace(" ", "", $_POST['edit_zipcode']);
			$zipcode = preg_replace("/[^0-9,.]/", "", $no_space_zipcode);

      //check if phone number is the right length
      if(strlen($phone)== 11){
				if(strlen($zipcode)== 5){
          //check if there is a duplicate phone number

          //get an array of the current list of users
          $users_sql = "SELECT * FROM users;";
          $users_result = mysqli_query($connection, $users_sql);
          $users_array = mysqli_fetch_all($users_result, MYSQLI_ASSOC);

          $i = 0;
          foreach($users_array as $user):
            //add one to the counter if the number entered matches another user's number who isn't the logged in user
          if($phone == $user['phone'] && $user['id'] != $_SESSION['id']){
              $i+=1;
            }
          endforeach;
          if($i == 0){
            if($name != $specific_user['name']){
							//if name has changed update name column for that user
              $edit_name_sql = "UPDATE users SET name = '".$name."' WHERE id = ".$_SESSION['id'].";";
              $edit_name_result = mysqli_query($connection, $edit_name_sql);
            }
            if($phone != $specific_user['phone']){
							//if phone number has changed update phone column for that user
              $edit_phone_sql = "UPDATE users SET phone = '".$phone."' WHERE id = ".$_SESSION['id'].";";
              $edit_phone_result = mysqli_query($connection, $edit_phone_sql);
            }
            if($zipcode != $specific_user['zipcode']){
							//if zipcode has changed update zipcode column for that user
              $edit_zipcode_sql = "UPDATE users SET zipcode = ".$zipcode." WHERE id = ".$_SESSION['id'].";";
              $edit_zipcode_result = mysqli_query($connection, $edit_zipcode_sql);

							//we need to update their timezone too if zipcode was changed
							//call getCoordinates() function to get latitude and longitude from zipcode
							$location = getCoordinates($zipcode);
							$timestamp = time();
							//call getTimezone() function to get time zone from the coordinates we just got
						  $timezone = getTimezone($timestamp,$location);
							//multiply by 100 for storage convenience if there is a timezone offset that isn't a whole number
							$timezone_stored = $timezone * 100;

							$edit_timezone_sql = "UPDATE users SET timezone = ".$timezone_stored." WHERE id = ".$_SESSION['id'].";";
              $edit_timezone_result = mysqli_query($connection, $edit_timezone_sql);
            }

            header('Location: '.$_SERVER['REQUEST_URI']);
          } else{
            $_SESSION['edit_profile_error'] = "Another user with this phone number already exists.";
          }
				} else{
					$_SESSION['signup_error'] = "Postal code must be 5 digits.";
				}
			} else{
				$_SESSION['signup_error'] = "Phone number length is invalid.";
			}
    } else{
      $_SESSION['edit_profile_error'] = "An input is still empty.";
    }
  endif;
};

//when delete_account button is pressed log out, delete account, go to home page
function deleteAccount($connection){
  //i should add an "are you sure" message

  if(isset($_POST['delete_account'])){
    $sql = "DELETE FROM users WHERE id = ".$_SESSION['id'].";";
		$past_actions_sql = "DELETE FROM past_actions WHERE user_id = ".$_SESSION['id'].";";
    session_unset();
    session_destroy();
    $result = mysqli_query($connection, $past_actions_sql);
		$past_actions_result = mysqli_query($connection, $sql);
    header('Location: index.php');
  }
};

//Get the profile of the logged in user
function getTheUser($connection){
  $sql = "SELECT * FROM users WHERE id=".$_SESSION['id'].";";
  $result = mysqli_query($connection, $sql);
  $array_user = mysqli_fetch_all($result, MYSQLI_ASSOC);
  return $array_user[0];
};

//if logged in user doesn't exist, end session and go to home page
//adds 1 to $i for each user in database that doesn't match the current session id
//if $i equals the total users in database(meaning session id dosnt match any user),
//then ends current session and goes to home page
function logOutDeletedUsers($connection){
  $sql = "SELECT * FROM users;";
  $result = mysqli_query($connection, $sql);
  $array_users = mysqli_fetch_all($result, MYSQLI_ASSOC);
  if($_SESSION['id'] != ""){
    $i = 0;
    foreach($array_users as $user):
      if($user['id'] != $_SESSION['id']){
        $i += 1;
      }
    endforeach;
    if($i == count($array_users)):
      session_unset();
      session_destroy();
      header('Location: index.php');
    endif;
  }
};
logOutDeletedUsers($conn);

//if user clicks check add 1 to their actions_completed in database and disable the completed action button
function completedAction($connection){
	//get the row in users table for the current user
  $get_sql = "SELECT * FROM users WHERE id=".$_SESSION['id'].";";
  $get_result = mysqli_query($connection, $get_sql);
  $array_user = mysqli_fetch_all($get_result, MYSQLI_ASSOC);
	//if user presses completed action button, update number of total actions completed by that user, and also completed_action and last_completed_action columns.
	//also update total actions completed by community
  if(isset($_POST['completed_action']) && !$array_user[0]['completed_action']){
		$new_actions_completed = $array_user[0]['actions_completed'] + 1;

		//get current seconds since jan 1 1970 in the users time zone and convert to nearest whole days.
		$user_curr_time = time() + 3600 * ($array_user[0]['timezone']/100);
		$user_days = ($user_curr_time/86400) - ($user_curr_time%86400)/86400;

    $sql = "UPDATE users SET actions_completed = $new_actions_completed, completed_action = TRUE, last_completed_action = $user_days WHERE id = ".$_SESSION['id'].";";
    $result = mysqli_query($connection, $sql);
    //update the main table total_actions of the whole community
    $old_actions_sql = "SELECT * FROM main WHERE id=1;";
    $old_actions_result = mysqli_query($connection, $old_actions_sql);
    $array_main = mysqli_fetch_all($old_actions_result, MYSQLI_ASSOC);
    $new_total_actions = $array_main[0]['total_actions'] + 1;
    $total_actions_sql = "UPDATE main SET total_actions = $new_total_actions WHERE id = 1;";
    $total_actions_result = mysqli_query($connection, $total_actions_sql);

    header('Location: '.$_SERVER['REQUEST_URI']);
  } else if(isset($_POST['completed_action']) && $array_user[0]['completed_action']){
    echo "You already completed the action for today.";
  }
};

//Adds the actions of every user to see how many total the community has done
function addAllActions($connection){
  $sql = "SELECT * FROM main WHERE id=1;";
  $result = mysqli_query($connection, $sql);
  $array_main = mysqli_fetch_all($result, MYSQLI_ASSOC);
  return $array_main[0]['total_actions'];
};

//check for 1hr of inactivity, if so then logout, if page reloaded with time to spare,
//then reset the 'timer'
if(isset($_SESSION['id'])):
  if(time() - $_SESSION['timestamp'] > 3600){
    session_unset();
    session_destroy();
    header('Location: '.$_SERVER['REQUEST_URI']);
  } else{
    $_SESSION['timestamp'] = time();
  }
endif;

//enter info needed for blog post preview to the database and create a new file for the blog post in the blog_posts folder of directory
function blogPost($connection){
	if(isset($_POST['submit_blog'])){
		//unset session lbog post error variable in case it was already set
		unset($_SESSION['blog_post_error']);
		//save the inputs in session variables so if theres an error the computer can remember what they had so they dot start from scratch
		$_SESSION['blog_input_title'] = $_POST['blog_title'];
		$_SESSION['blog_input_author'] = $_POST['blog_author'];
		$_SESSION['blog_input_description'] = $_POST['blog_description'];
		$_SESSION['blog_input_content'] = $_POST['blog_content'];
		$_SESSION['blog_input_timestamp'] = time();

		//check that no inputs are empty
		if($_FILES['blog_preview_image'] != "" && $_POST['blog_title'] != "" && $_POST['blog_author'] !="" && $_POST['blog_description'] != "" && $_POST['blog_content'] != ""){
		//Enter preview information into database
			//remove whitespace from beginning and end of inputs and remove special characters
			$category = trim($_POST['blog_category']);
			$category = filter_var($category, FILTER_SANITIZE_STRING);

			$preview_img_name = str_replace(' ', '_', $_FILES['blog_preview_image']["name"]);

			$title = trim($_POST['blog_title']);
			$title = filter_var($title, FILTER_SANITIZE_STRING);

			$author = trim($_POST['blog_author']);
			$author = filter_var($author, FILTER_SANITIZE_STRING);

			$time = date("m/d/Y");

			$description = trim($_POST['blog_description']);
			$description = filter_var($description, FILTER_SANITIZE_STRING);

			$file_name = str_replace(' ', '_', $title) . '.php';

			$content = $_POST['blog_content'];

			//get an array of all the current blog posts in the database to make sure the title (and file name) is not already taken
			$all_posts_sql = "SELECT * FROM blog;";
			$all_posts_result = mysqli_query($connection, $all_posts_sql);
			$all_posts_array = mysqli_fetch_all($all_posts_result, MYSQLI_ASSOC);
			//go through array of posts, if there are any matches (regardless of upercase/lowercase) add one to counter
			$i = 0;
			$lowercase_title = strtolower($title);
			foreach($all_posts_array as $blog_post):
				if($lowercase_title == strtolower($blog_post['title'])){
					$i+=1;
				}
			endforeach;
			//if the title hasnt been used, proceed with creating blog post
			if($i == 0){
			//enter information needed for preview into database
				$blog_sql = "INSERT INTO blog (time, category, author, title, description, preview_image, file_name) VALUES ('$time', '$category', '$author', '$title', '$description', '$preview_img_name', '$file_name');";
				$blog_result = mysqli_query($connection, $blog_sql);
				//if it is not already in directory, upload image to directory in the images/preview_images folder
				$image_file_path = 'images/preview_images/'.$preview_img_name;
				if(!file_exists($image_file_path)){
					move_uploaded_file($_FILES['blog_preview_image']["tmp_name"], $image_file_path);
				}
			//Create new blog post file
				//create a variable to hold the whole html content of the new file for the blog post with the specifics for this post added in as variables
				$php_file_content = '
				<!DOCTYPE html>
				<?php
				$time = date_default_timezone_set("America/Los_Angeles");
				include "../connection.php";
				include "../user_join.php";

				//define $path variable so links inside nav tag and footer still point to the right page even though this file is in a folder
				$path = "../";
				?>
				<html>
				<head>
					<meta charset="utf-8">
					<?php echo "<title>'.$title.'</title>";?>
					<meta name="viewport" content="width=device-width,initial-scale=1"/>
					<link rel="stylesheet" href="../styles/styles.css">

				</head>
				<body>
					<!--Preview information from database in case row in table is deleted-->
					<div id="blog_data_meta">
						<ul>
							<li>Time: '.$time.'</li>
							<li>Category: '.$category.'</li>
							<li>Author: '.$author.'</li>
							<li>Title: '.$title.'</li>
							<li>Description: '.$description.'</li>
							<li>Preview image: '.$preview_img_name.'</li>
							<li>File name: '.$file_name.'</li>
						</ul>
					</div>
					<?php
						include "../nav_tag.php";
					?>
					<div id="pageWrapper">
						<div id="blog_post_page">
							<p><a href="../blog.php"><--Back</a></p>
									<h1>Title: '.$title.'</h1>
									<p>Author: '.$author.'</p>
									<p>Published: '.$time.'</p>
									<p>Category: '.$category.'</p>
									'.$content.'
						</div>
					</div>
					<?php
						include "../footer.php";
					 ?>
					 <script src="../scripts/scripts.js"></script>
				</body>
				</html>
				';
				//create a new file in the blog_posts folder of the directory with this php file content
				$file_path = "blog_posts/" . $file_name;
				$newfile = fopen($file_path, "w") or die("Unable to open file!");
				fwrite($newfile, $php_file_content);

				//unset the session variables for the things they've entered because they are no longer needed
				unset($_SESSION['blog_input_title']);
				unset($_SESSION['blog_input_author']);
				unset($_SESSION['blog_input_description']);
				unset($_SESSION['blog_input_content']);
				unset($_SESSION['blog_input_timestamp']);
			} else{
			$_SESSION['blog_post_error'] = "This title has already been used.";
			}
		} else if($_POST['blog_category']== "" && $_POST['blog_title'] == "" && $_POST['blog_author'] =="" && $_POST['blog_description'] == "" && $_POST['blog_content'] == ""){
			//if all the inputs are empty, dont even show an error message and just clear the session variable storing the error
			unset($_SESSION['blog_post_error']);
		} else{
			$_SESSION['blog_post_error'] = "An input is still empty.";
		}
	}
};

//delete a blog post
function deletePost($connection){
	if(isset($_POST['delete_post_submit'])):
    //get an array of all blog posts in blog table in databse
    $sql = "SELECT * FROM blog;";
    $result = mysqli_query($connection, $sql);
    $array_posts = mysqli_fetch_all($result, MYSQLI_ASSOC);
    //turn input to all lowercase characters to make inputs not case sensitive
    $lowercase_input = strtolower($_POST['delete_post_title']);
    //start a counter variable to see if no posts match
    $i = 0;
    foreach($array_posts as $blog_post):
      //make this post's title lowercase to compare without case being a factor
      $lowercase_title = strtolower($blog_post['title']);
      if($lowercase_input == $lowercase_title){
				//delete row from blog table in database (dont delete the file from directory in case we want to recover the blog post)
				$delete_post_sql = 'DELETE FROM blog WHERE id = '.$blog_post['id'].';';
				$delete_post_result = mysqli_query($connection, $delete_post_sql);
        //add 1 to counter variable to show that there has been a match
        $i += 1;
      }
    endforeach;
		//display messages based on how many posts in the databse matched with user input
    if($i == 0){
      echo '
        <p>There are no matches in the database.</p>
      ';
    } else if($i == 1){
			echo '
				<p>Deleted one post from database.</p>
			';
		} else{
			echo '
				<p>Deleted '.$i.' posts from database.</p>
			';
		}
  endif;
};
