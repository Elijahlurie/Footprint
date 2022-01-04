//given class of divs that need a specific background image and a class of hidden divs containing the file path of the specified image, set the background image of the containers
var setPreviewImage = function(container_class, image_path){
  //get array of blog post preview divs
  var preview_image_cont = document.getElementsByClassName(container_class);
  //get array of the hidden p tags inside the previews that contain the file path to the specified image for each preview
  var image_paths = document.getElementsByClassName(image_path);

  //set each preview's background image to the file path written in the image path element
  for(var i = 0; i < preview_image_cont.length; i++){
    preview_image_cont[i].style.backgroundImage = "url(" + image_paths[i].innerHTML + ")";
  }
};
//call for featured previews
setPreviewImage("preview_image_cont", "image_path");
//call for action deep dive previews
setPreviewImage("aotd_preview_image_cont", "aotd_image_path");
//call for climate news previews
setPreviewImage("news_preview_image_cont", "news_image_path");
//call for editorial previews
setPreviewImage("editorial_preview_image_cont", "editorial_image_path");
