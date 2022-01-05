//given class of divs that need a specific background image and a class of hidden divs containing the file path of the specified image, set the background image of the containers
var setBackgroundImage = function(container_class, image_path){
  //get array of container divs
  var background_image_cont = document.getElementsByClassName(container_class);
  //get array of the hidden p tags inside the divs that contain the file path to the specified background image for each div
  var image_paths = document.getElementsByClassName(image_path);

  //set each div's background image to the file path written in the image path element
  for(var i = 0; i < background_image_cont.length; i++){
    background_image_cont[i].style.backgroundImage = "url(" + image_paths[i].innerHTML + ")";
  }
};
//call for featured previews
setBackgroundImage("preview_image_cont", "image_path");
//call for action deep dive previews
setBackgroundImage("aotd_preview_image_cont", "aotd_image_path");
//call for climate news previews
setBackgroundImage("news_preview_image_cont", "news_image_path");
//call for editorial previews
setBackgroundImage("editorial_preview_image_cont", "editorial_image_path");
