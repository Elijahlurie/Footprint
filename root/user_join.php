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

    if($_POST['name'] != "" && $_POST['phone'] != "" && $_POST['zipcode'] != "" && $_POST['password'] != ""){
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
			$phone_stored = '+1' . $phone;

			//turn password into a hash so it is not openly displayed in the database
			$password_hash = password_hash($_POST['password'], PASSWORD_DEFAULT);

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

			if(strlen($phone) == 10){
				if(strlen($zipcode)== 5){
					if(strlen($_POST['password']) >= 8){
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
							$sql = "INSERT INTO users (name, phone, password_hash, texted, curr_action, yesterday, timezone, zipcode, last_completed_action) VALUES ('$name','$phone_stored','$password_hash',0,'$rand', '$user_days', '$timezone_stored', '$zipcode', '$user_days');";
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
						$_SESSION['signup_error'] = "Password must be at least 8 characters long.";
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

//if a user has been texted button is set when page reloads and it hasn't been pressed yet today, set texted time to time() for that user
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
//if $i equals the total users in database(meaning input dosnt match any user),then outputs "user not found"
//redierect user to user page
function loginUsers($connection){
  $sql = "SELECT * FROM users;";
  $result = mysqli_query($connection, $sql);
  $array_users = mysqli_fetch_all($result, MYSQLI_ASSOC);
  if(isset($_POST['login_submit'])){
    $i = 0;
    $no_space_number = str_replace(" ", "", $_POST['login_phone']);
    $phone = preg_replace("/[^0-9,.]/", "", $no_space_number);
		$phone = '+1' . $phone;
    foreach($array_users as $user):
      if(strtolower($user['phone']) == $phone && password_verify($_POST['login_password'], $user['password_hash'])){
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
    if($_POST['edit_name'] != "" && $_POST['edit_phone'] != "" && $_POST['edit_zipcode'] != ""){
      $no_space_name = str_replace(" ", "", $_POST['edit_name']);
      $name = ucfirst(strtolower(filter_var($no_space_name, FILTER_SANITIZE_STRING)));
      //remove all special characters other than numbers from phone number with country code added
      $no_space_number = str_replace(" ", "", $_POST['edit_phone']);
      $phone = preg_replace("/[^0-9,.]/", "", $no_space_number);
			$stored_phone = '+' . $phone;
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
              $edit_phone_sql = "UPDATE users SET phone = '".$stored_phone."' WHERE id = ".$_SESSION['id'].";";
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
					$_SESSION['edit_profile_error'] = "Postal code must be 5 digits.";
				}
			} else{
				$_SESSION['edit_profile_error'] = "Phone number length is invalid.";
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
	if($_SESSION['id']){
		$sql = "SELECT * FROM users WHERE id=".$_SESSION['id'].";";
	  $result = mysqli_query($connection, $sql);
	  $array_user = mysqli_fetch_all($result, MYSQLI_ASSOC);
	  return $array_user[0];
	}
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

//if user presses completed action button, update number of total actions completed by that user, and also completed_action and last_completed_action columns.
function completedAction($connection){
	//also update total actions completed by community
  if(isset($_POST['completed_action'])){
		//get the row in users table for the current user
	  $get_sql = "SELECT * FROM users WHERE id=".$_SESSION['id'].";";
	  $get_result = mysqli_query($connection, $get_sql);
	  $array_user = mysqli_fetch_all($get_result, MYSQLI_ASSOC)[0];

		if(!$array_user['completed_action']){
		//if the user hasn't already complteted the action
			$new_actions_completed = $array_user['actions_completed'] + 1;

			//get current seconds since jan 1 1970 in the users time zone and convert to nearest whole days.
			$user_curr_time = time() + 3600 * ($array_user['timezone']/100);
			$user_days = ($user_curr_time/86400) - ($user_curr_time%86400)/86400;

	    $sql = "UPDATE users SET actions_completed = $new_actions_completed, completed_action = TRUE, last_completed_action = $user_days WHERE id = ".$_SESSION['id'].";";
	    $result = mysqli_query($connection, $sql);

			//update the times_completed for the action that was assigned to the user
			//get the value for the times_completed column of the row of the actions table belonging to the action currently asigned to the user
			$assigned_action_sql = "SELECT * FROM actions WHERE id=".$array_user['curr_action'].";";
			$assigned_action_result = mysqli_query($connection, $assigned_action_result);
		  $old_times_completed = mysqli_fetch_all($assigned_action_result, MYSQLI_ASSOC)[0]['times_completed'];
			$new_times_completed = $old_times_completed + 1;
			//update the times_completed value in the database for the action
			$times_completed_sql = "UPDATE actions SET times_completed=".$new_times_completed." WHERE id=".$array_user['curr_action'].";";
			$times_completed_result = mysqli_query($connection, $times_completed_sql);

	    //update the main table total_actions of the whole community

	    $old_actions_sql = "SELECT * FROM main WHERE id=1;";
	    $old_actions_result = mysqli_query($connection, $old_actions_sql);
	    $array_main = mysqli_fetch_all($old_actions_result, MYSQLI_ASSOC);
	    $new_total_actions = $array_main['total_actions'] + 1;
	    $total_actions_sql = "UPDATE main SET total_actions = $new_total_actions WHERE id = 1;";
	    $total_actions_result = mysqli_query($connection, $total_actions_sql);

	    header('Location: '.$_SERVER['REQUEST_URI']);
  	} else{
    	echo "You already completed the action for today.";
  	}
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

//search blog table in database for a row that corresponds to this blog post file, if there is no row in the database for this file, redirect to blog.php because this post was deleted
function findPreview($connection){
	//if there is no row in the database for this file, redirect to blog.php because this post was deleted
	//select the row from blog table where the value in the file name column equals this name of this file
	$find_preview_sql = 'SELECT * FROM blog WHERE file_name = "'.basename($_SERVER['PHP_SELF']).'";';
	$find_preview_result = mysqli_query($connection, $find_preview_sql);
	$find_preview_array = mysqli_fetch_all($find_preview_result, MYSQLI_ASSOC);
	//if resulting array is empty, meaning there is no row in database to match this file, redirect to blog.php
	if(empty($find_preview_array)){
		header("Location: ../blog.php");
	}
};
//enter info needed for blog post preview to the database
function createBlogPreview($connection){
	if(isset($_POST['submit_blog_preview'])){
		//unset session blog preview error variable in case it was already set
		unset($_SESSION['blog_preview_error']);
		//save the inputs in session variables so if theres an error the computer can remember what they had so they dot start from scratch
		$_SESSION['blog_input_title'] = $_POST['blog_title'];
		$_SESSION['blog_input_author'] = $_POST['blog_author'];
		$_SESSION['blog_input_description'] = $_POST['blog_description'];
		$_SESSION['blog_input_timestamp'] = time();

		//check that no inputs are empty
		if($_FILES['blog_preview_image']["name"] != "" && $_POST['blog_title'] != "" && $_POST['blog_author'] !="" && $_POST['blog_description'] != ""){
			//Enter preview information into database
				//remove whitespace from beginning and end of inputs and remove special characters
				$category = $_POST['blog_category'];

				$preview_img_name = str_replace(' ', '_', $_FILES['blog_preview_image']["name"]);
				$title = trim($_POST['blog_title']);
				$title = filter_var($title, FILTER_SANITIZE_STRING);

				$author = trim($_POST['blog_author']);
				$author = filter_var($author, FILTER_SANITIZE_STRING);

				$time = time();

				$date = date("m/d/Y");

				$description = trim($_POST['blog_description']);
				$description = filter_var($description, FILTER_SANITIZE_STRING);

				$file_name = str_replace(' ', '_', $title) . '.php';

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
					$blog_sql = "INSERT INTO blog (time, date, category, author, title, description, preview_image, file_name) VALUES ('$time', '$date', '$category', '$author', '$title', '$description', '$preview_img_name', '$file_name');";
					$blog_result = mysqli_query($connection, $blog_sql);
					//if it is not already in directory, upload image to directory in the images/preview_images folder
					$image_file_path = '../images/preview_images/'.$preview_img_name;
					if(!file_exists($image_file_path)){
						move_uploaded_file($_FILES['blog_preview_image']["tmp_name"], $image_file_path);
					}
				//unset the session variables for the things they've entered because they are no longer needed
					unset($_SESSION['blog_input_title']);
					unset($_SESSION['blog_input_author']);
					unset($_SESSION['blog_input_description']);
					unset($_SESSION['blog_input_timestamp']);
				//set a session variable showing a preview has just been made so user isnt redirected from category form page
					$_SESSION['created_preview'] = time();
				//redirect user to the appropriate form page for the requested category to finish creating the content of the blog post
					if($category == "Action of the Day"){
						header("Location: aotd_form.php");
					} else if($category == "Editorial"){
						header("Location: editorial_form.php");
					}
				}	 else{
				$_SESSION['blog_preview_error'] = "This title has already been used.";
				}
		} else if($_POST['blog_category']== "" && $_POST['blog_title'] == "" && $_POST['blog_author'] =="" && $_POST['blog_description'] == "" && $_POST['blog_content'] == ""){
			//if all the inputs are empty, dont even show an error message and just clear the session variable storing the error
			unset($_SESSION['blog_preview_error']);
		} else{
			$_SESSION['blog_preview_error'] = "An input is still empty.";
		}
	}
};

//create a new file for the blog post in the blog_posts folder of directory
function createBlogPost($connection){
	if(isset($_POST['submit_blog_post'])){
		//unset session blog post error variable in case it was already set
		unset($_SESSION['blog_post_error']);
		//check that no inputs are empty
		$count = 0;
		//for each of the set post variables, add 1 to the $count counter variable if the input was left empty
		//at the same time, save the inputs in session variables so if theres an error the computer can remember what they had so they dot start from scratch
		//start $i as a counter to give a unique name to each session variable with user inputs
		$i = 0;
		foreach($_POST as $input){
			if($input == ""){
				$count += 1;
			}
			//save the value for that $_POST input in a uniquely named session variable
			$_SESSION['blog_post_input_'.$i] = $input;
			$i += 1;
		}
		//go through the entered values from the source inputs and subtract 1 from $count for each one that is blank because when page reloads due to error the inputs would dissappear anyway, so no point in alerting that an input is empty if one of the source inputs is the only reason why
		for($n = 0; $n<$_POST['sources_count']; $n++){
			if($_POST["source_input_".$n] == ""){
				$count -= 1;
			}
		}
		if($count == 1){
			//if $count = 1, meaning every input (except for the submit button) has some value, continue
			//Create new blog post file
				//get an array of all blog posts from the database
				$new_sql = "SELECT * FROM blog;";
				$new_result = mysqli_query($connection, $new_sql);
				$new_result_array = mysqli_fetch_all($new_result, MYSQLI_ASSOC);
				//get the last item of that array, the information for the newest blog post
				$new_result_row = end($new_result_array);
				//generate a string with the column names and values and store that string in a variable so the information can be held in an html comment on the blog file in case the row in the table is deleted
				$list = [];
				foreach($new_result_row as $key => $value){
					$list[] = $key." (".$value.")";
				}
				$row_contents = "Data from blog table: " . implode("; " , $list);
				//create a variable to hold the whole html content of the new file for the blog post with the specifics for this post added in as variables, formatted based on category of post
				if($new_result_row["category"] == "Action of the Day"){
					//generate bullet list of sources
					$source_ul_content = '';
					for($n = 0; $n<$_POST['sources_count']; $n++){
						$input_value = $_POST["source_input_".$n];
						if($input_value != ""){
							//make sure the input wasn't blank before adding the value as a list item
							$source_ul_content = $source_ul_content . '<li><a href="'.$input_value.'" target="_blank">'.$input_value.'</a></li>';
						}
					}
					$php_file_content = '
					<!DOCTYPE html>
					<?php
					$time = date_default_timezone_set("America/Los_Angeles");
					include "../connection.php";
					include "../user_join.php";

					//call findPreview to check if this post has been deleted and redierct user if it has
					findPreview($conn);

					//define $path variable so links inside nav tag and footer still point to the right page even though this file is in a folder
					$path = "../";
					?>
					<html>
					<head>
						<meta charset="utf-8">
						<?php echo "<title>'.$new_result_row["title"].'</title>";?>
						<meta name="viewport" content="width=device-width,initial-scale=1"/>
						<link rel="stylesheet" href="../styles/styles.css">
						<link rel="stylesheet" href="../styles/blog_post_styles.css">
					</head>
					<body>
						<!--Preview information from database in case row in table is deleted
						<div id="blog_data_meta">
							'.$row_contents.'
						</div>-->
						<?php
					    include "../nav_tag.php";
					   ?>
						 <div id="aotd_page_wrapper">
	 							<div id="aotd_page_header_cont">
	 								<div id="aotd_page_header">
	 									<div id="aotd_page_header_h1">
	 										<h1>By '.$_POST['aotd_input_header_action'].'. . .</h1>
	 									</div>
	 									<div id="aotd_page_header_subtext">
	 										'.$_POST['aotd_input_header_impact'].'
	 									</div>
	 									<div id="aotd_check_facts_btn_cont">
											<a id="aotd_check_facts_btn" href="#blog_sources_cont">Check the facts</a>
										</div>
	 								</div>
	 								<p id="aotd_page_image_path">../images/preview_images/'.$new_result_row["preview_image"].'</p>
	 							</div>
	 							<div id="aotd_page_stats_cont">
	 								<div class="inline">
	 									<h2>If everyone* '.$_POST['aotd_input_stats_action'].'&nbsp</h2>
										<div id="stat_timespan_dropdown">
											<h2 id="stat_timespan">today</h2>
											<div id="stat_timespan_content">
												<div id="today_option" class="stat_timespan_option">today</div>
												<div id="week_option" class="stat_timespan_option">this week</div>
												<div id="year_option" class="stat_timespan_option">this year</div>
											</div>
										</div>
										<h2>, we\'d save . . .</h2>
	 								</div>
	 								<div id="aotd_page_stats">
	 									<div>
	 										<h2 class="statistic_number">'.number_format($_POST['aotd_input_stats_number_1']).'</h2>
	 										<p class="statistic_unit">'.$_POST['aotd_input_stats_unit_1'].'</p>
	 										<p><em>'.$_POST['aotd_input_stats_impact_1'].'</em></p>
	 									</div>
	 									<div>
		 									<h2 class="statistic_number">'.number_format($_POST['aotd_input_stats_number_2']).'</h2>
		 									<p class="statistic_unit">'.$_POST['aotd_input_stats_unit_2'].'</p>
		 									<p><em>'.$_POST['aotd_input_stats_impact_2'].'</em></p>
	 									</div>
	 									<div>
		 									<h2 class="statistic_number">'.number_format($_POST['aotd_input_stats_number_3']).'</h2>
		 									<p class="statistic_unit">'.$_POST['aotd_input_stats_unit_3'].'</p>
		 									<p><em>'.$_POST['aotd_input_stats_impact_3'].'</em></p>
	 									</div>
	 								</div>
	 								<h2>*. . . And that\'s just in the USA!</h2>
	 							</div>
								<h2 class="blog_page_category_link"><a href="../category_pages/aotd.php">Action Description Posts</a></h2>
	 							<div id="aotd_form_content">
	 								<div id="aotd_content_text">
	 									'.$_POST['aotd_input_content'].'
										<br><em>&nbsp&nbsp&nbsp&nbsp&nbsp- '.$new_result_row["author"].', '.$new_result_row["date"].'</em>
	 								</div>
	 							</div>
							<div id="blog_sources_cont">
				        <h1>Sources</h1>
				        <hr>
				        <ul id="blog_sources">
								'.$source_ul_content.'
				        </ul>
				      </div>
	 					</div>
	 					<?php
	 						include "../footer.php";
	 					 ?>
	 					<script src="../scripts/scripts.js"></script>
						<script src="../scripts/blog_stats.js"></script>
						<script>
						 //store original statistic values in an array to not forget them when other values are calculated in
						 var original_numbers = ['.$_POST['aotd_input_stats_number_1'].', '.$_POST['aotd_input_stats_number_2'].', '.$_POST['aotd_input_stats_number_3'].'];

						 //set background image of header div
	 					 aotd_page_header = document.getElementById("aotd_page_header_cont");
	 					 aotd_page_image_path = document.getElementById("aotd_page_image_path");
	 					 aotd_page_header.style.backgroundImage =  "url(" + aotd_page_image_path.innerHTML + ")";
	 					</script>
	 				</body>
					</html>
					';
				} else if($new_result_row["category"] == "Editorial"){
					$php_file_content = '
					<!DOCTYPE html>
					<?php
					$time = date_default_timezone_set("America/Los_Angeles");
					include "../connection.php";
					include "../user_join.php";

					//call findPreview to check if this post has been deleted and redierct user if it has
					findPreview($conn);

					//define $path variable so links inside nav tag and footer still point to the right page even though this file is in a folder
					$path = "../";
					?>
					<html>
					<head>
						<meta charset="utf-8">
						<?php echo "<title>'.$new_result_row["title"].'</title>";?>
						<meta name="viewport" content="width=device-width,initial-scale=1"/>
						<link rel="stylesheet" href="../styles/styles.css">
						<link rel="stylesheet" href="../styles/blog_post_styles.css">
					</head>
					<body>
						<!--Preview information from database in case row in table is deleted
						<div id="blog_data_meta">
							'.$row_contents.'
						</div>-->
						<?php
					    include "../nav_tag.php";
					   ?>
						<div id="editorial_page_wrapper">
			        <div id="editorial_page_header_cont">
			          <div id="editorial_page_header">
			             <h1>'.$new_result_row["title"].'</h1>
			             <br><p>'.$_POST["editorial_input_subtitle"].'</p>
			          </div>
			          <p id="editorial_page_image_path">../images/preview_images/'.$new_result_row["preview_image"].'</p>
			        </div>
			        <h2 class="blog_page_category_link"><a href="../category_pages/editorial.php">Editorial</a></h2>
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
			           '.$_POST["editorial_input_content"].'
			         </div>
			        </div>
						</div>
						<?php
							include "../footer.php";
						 ?>
						<script src="../scripts/scripts.js"></script>
						<script>
				    //set background image of header div
				    var editorial_page_header = document.getElementById("editorial_page_header_cont");
				    var editorial_page_image_path = document.getElementById("editorial_page_image_path");
				    editorial_page_header.style.backgroundImage =  "url(" + editorial_page_image_path.innerHTML + ")";
				 </script>
				 </body>
				 </html>
		      ';
				}
				//create a new file in the blog_posts folder of the directory with this php file content
				$file_path = "../blog_posts/" . $new_result_row["file_name"];
				$newfile = fopen($file_path, "w") or die("Unable to open file!");
				fwrite($newfile, $php_file_content);
				//unset $_SESSION['created_preview'] so that users will be redirected from category form pages unless they make a new post
				unset($_SESSION['created_preview']);
				//unset all the session variables that fill in the blanks with users previous inputs
				//get an array of all the keys of session variables that are currently set
				$array_keys = array_keys($_SESSION);
				foreach ($array_keys as $key) {
					//for each of the keys, check if the first 16 characters of the key are blog_post_input_, and unset the session variable if that is true
    			if (substr($key, 0, 16) == 'blog_post_input_') {
        		unset($_SESSION[$key]);
    			}
				}
				//redirect user to the blog post they just made
				header('Location:../blog.php');
		}else{
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
		//get an array of featured post ids from featured_blog table from database
		$featured_sql = "SELECT featured_id FROM featured_blog;";
		$featured_result = mysqli_query($connection, $featured_sql);
		$array_featured_ids = mysqli_fetch_all($featured_result, MYSQLI_ASSOC);
			//make an array with purely a list of the featured id numbers because $array_featured_ids is an array of arrays, inside of which are the information we need
		$new_array_ids = [];
		foreach($array_featured_ids as $id_array){
			$new_array_ids[] = $id_array['featured_id'];
		}
    //start a counter variable to see if no posts match
    $i = 0;
    foreach($array_posts as $blog_post):
      if($_POST['delete_post_title'] == $blog_post['title']){
				//if the requested title matches this title from the arry of blog posts, continue
				if(!in_array($blog_post['id'], $new_array_ids)){
					//if the id of this blog post is not one of the featured posts
					//delete row from blog table in database (dont delete the file from directory in case we want to recover the blog post)
					$delete_post_sql = 'DELETE FROM blog WHERE id = '.$blog_post['id'].';';
					$delete_post_result = mysqli_query($connection, $delete_post_sql);
	        //add 1 to counter variable to show that there has been a match
	        $i += 1;
				} else {
					$_SESSION['delete_post_message'] = 'The requested post is currently featured, so it cannot be deleted.';
					$_SESSION['delete_post_message_time'] = time();
					return;
				}
      }
    endforeach;
		//display messages based on how many posts in the databse matched with user input
    if($i == 0){
      $_SESSION['delete_post_message'] =  'There are no matches in the database.';
			$_SESSION['delete_post_message_time'] = time();
    } else if($i == 1){
			$_SESSION['delete_post_message'] = 'Deleted one post from database.';
			$_SESSION['delete_post_message_time'] = time();
		} else{
			$_SESSION['delete_post_message'] = 'Deleted '.$i.' posts from database.';
			$_SESSION['delete_post_message_time'] = time();
		}
  endif;
};

//get array of all posts whose ids are featured in featured_blog table by joining blog table on featured_blog table
function getFeaturedPosts($connection){
 $join_sql = "SELECT * FROM blog JOIN featured_blog ON blog.id = featured_blog.featured_id ORDER BY featured_blog.id;";
 $join_result = mysqli_query($connection, $join_sql);
 $featured_posts_array = mysqli_fetch_all($join_result, MYSQLI_ASSOC);
 return $featured_posts_array;
}

//allows admin to change which blog posts are currently featured
function updateFeaturedPosts($connection, $featured_posts_array){
	if(isset($_POST['update_featured_submit'])):
		//get an array of all titles of blog posts in blog table in databse
		$sql = "SELECT title FROM blog;";
		$result = mysqli_query($connection, $sql);
		$array_titles = mysqli_fetch_all($result, MYSQLI_ASSOC);
			//make an array with purely a list of the title strings because $array_titles is an array of arrays, inside of which are the information we need
		$new_array_titles = [];
			foreach($array_titles as $title_array){
				$new_array_titles[] = $title_array['title'];
			}
		//get an array of featured post ids from featured_blog table from database
		$featured_sql = "SELECT featured_id FROM featured_blog;";
		$featured_result = mysqli_query($connection, $featured_sql);
		$array_featured_ids = mysqli_fetch_all($featured_result, MYSQLI_ASSOC);
			//make an array with purely a list of the featured id numbers because $array_featured_ids is an array of arrays, inside of which are the information we need
		$new_array_ids = [];
		foreach($array_featured_ids as $id_array){
			$new_array_ids[] = $id_array['featured_id'];
		}
		//start a counter to see if no inputs are changed
		$unchanged_input_counter = 0;
		//check each input separately to see if user has changed something
		for($i = 0; $i <3; $i++){
			$title_input_name = 'update_featured' . $i;
			$requested_title = $_POST[$title_input_name];
				//compare user input to the title of the currently featured post that corresponds with that input to see if it has been changed
			if($requested_title != $featured_posts_array[$i]['title']){
				if(in_array($requested_title, $new_array_titles)){
					//if the title corresponds to an existing post, continue
					//get an array with the information from the row of the blog table that matches the inputted title.
					$row_sql = "SELECT * FROM blog WHERE title='$requested_title';";
					$row_result = mysqli_query($connection, $row_sql);
					$requested_post = mysqli_fetch_all($row_result, MYSQLI_ASSOC)[0];
					$requested_id = $requested_post['id'];
					if(!in_array($requested_id, $new_array_ids)){
						//if the id of the requested post is not already listed in the featured_id column of the featured_blog table
						//clear error message variables
						unset($_SESSION["update_featured_error"]);
						unset($_SESSION["update_featured_error_time"]);
						//run sql to insert the id of the requested post into the correct row of the featured_blog table in the featured_id column, replacing the id that was previously there
						$update_featured_sql = "UPDATE featured_blog SET featured_id = $requested_id WHERE id = ($i + 1)";
						$update_featured_result = mysqli_query($connection, $update_featured_sql);
						//reload page
						header('Location: '.$_SERVER['REQUEST_URI']);
					} else{
						$_SESSION["update_featured_error"] = "The requested post is already featured.";
						$_SESSION["update_featured_error_time"] = time();
					}
				} else{
					$_SESSION["update_featured_error"] = "This title does not appear in database.";
					$_SESSION["update_featured_error_time"] = time();
				}
			} else{
				$unchanged_input_counter += 1;
			}
		}
		if($unchanged_input_counter == 3){
			//if no inputs have been changed but submit button was pressed, unset error message variable.
			unset($_SESSION["update_featured_error"]);
			unset($_SESSION["update_featured_error_time"]);
		}
	endif;
};

//get array of all urls of featured news articles in featured_news table
function getFeaturedNews($connection){
	$sql = "SELECT * FROM featured_news;";
  $result = mysqli_query($connection, $sql);
  $array_news = mysqli_fetch_all($result, MYSQLI_ASSOC);
 return $array_news;
}

//allows admin to change which blog posts are currently featured
function updateFeaturedNews($connection, $featured_news_array){
	if(isset($_POST['update_news_submit'])):
		//start a counter to see if no inputs are changed
		$unchanged_input_counter = 0;
		//check each group of inputs separately to see if user has changed something
		for($i = 0; $i <5; $i++){
			$news_title_input_name = 'update_news_title' . $i;
			$requested_title = $_POST[$news_title_input_name];
			$news_url_input_name = 'update_news_url' . $i;
			$requested_url = $_POST[$news_url_input_name];
				//compare user url input to the url of the currently featured news article that corresponds with that input,  to see if either it or the title has been changed
			if($requested_url != $featured_news_array[$i]['url'] || $requested_title != $featured_news_array[$i]['title']){
				// make an array of purely the urls of the featured news articles
				$new_url_array = [];
				foreach($featured_news_array as $featured_news){
					$new_url_array[] = $featured_news['url'];
				}
				//make sure either the requested url is not found in this array of currently featured urls, meaning it is not already featured, or the title is being changed but the url is not
					if(!in_array($requested_url, $new_url_array) || ($requested_title != $featured_news_array[$i]['title'] && $requested_url == $featured_news_array[$i]['url'])){
						//clear error message variables
						unset($_SESSION['update_news_error']);
						unset($_SESSION['update_news_error_time']);
						//run sql to insert the id of the requested post into the correct row of the featured_blog table in the featured_id column, replacing the id that was previously there
						$update_news_sql = "UPDATE featured_news SET title = '".$requested_title."', url = '".$requested_url."' WHERE id = ($i + 1);";
						$update_news_result = mysqli_query($connection, $update_news_sql);
						//reload page
						header('Location: '.$_SERVER['REQUEST_URI']);
					} else{
						$_SESSION["update_news_error"] = "The requested url is already featured.";
						$_SESSION["update_news_error_time"] = time();
					}
			} else{
				$unchanged_input_counter += 1;
			}
		}
		if($unchanged_input_counter == 5){
			//if no inputs have been changed but submit button was pressed, unset error message variable.
			unset($_SESSION['update_news_error']);
			unset($_SESSION['update_news_error_time']);
		}
	endif;
};
