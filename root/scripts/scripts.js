
var hello_user = document.getElementById('hello_user');
var user_links = document.getElementById('user_links');

var delete_div_container = document.getElementById('delete_div_container');
var delete_div_content = document.getElementById('delete_div_content');
var delete_link = document.getElementsByClassName('delete_link')[0];
var cancel_delete = document.getElementById('cancel_delete');

var user_join_form = document.getElementById('user_join_form');
var why_zipcode_link = document.getElementById('why_zipcode_link');

//opens a hidden div when a certain element is clicked on and hides the div again when the element clicked on or any space ouside the div are clicked on
//doesnt close the opened div if one of the optional_third or optional_fourth are clicked
//if true is entered for closes_itself argument, clicking on the opened div closes it.
var openDivs = function(clicked, div, display, closes_itself, optional_third, optional_fourth){
  var open = function(){
    div.style.display = display;
    clicked.removeEventListener("click", open);
    clicked.addEventListener("click", close_clicked);
  };
  //if the div is opened, close it by pressing the element clicked on to open it
  var close_clicked = function(){
    div.style.display = "none";
    clicked.removeEventListener("click", close_clicked);
    clicked.addEventListener("click", open);
  };
  //close the div by clicking anywhere except the div and the element clicked on to open it
  var close = function(){
    //get the element bing clicked on
    targetElement = event.target;
    do{
      if((targetElement == div && closes_itself == false)|| targetElement == clicked || targetElement == optional_third || targetElement == optional_fourth){
        //end the program, don't close anything
        return;
      }
      // Go up the DOM
      targetElement = targetElement.parentNode;
    } while(targetElement);
    clicked.addEventListener("click", open);
    div.style.display = "none";
  };
  clicked.addEventListener("click", open);
  document.addEventListener("click", close);
};

//code for add sources button on blog post form pages
var addSourceInput = function(){
  var blog_sources = document.getElementById('blog_sources');
  var sources_count = document.getElementById('sources_count');
  //start counter variable to give each input a unique name
  var counter = 0;
  //if the counter has already been started, get the current value of the counter so it doesnt start back at 0
  if(sources_count.value){
    counter = sources_count.value;
  }

  //get an array of the current source inputs
  source_input = document.getElementsByClassName('source_input');
  //go through each of the current source inputs to properly recreate the current html of the blog_sources <ul> so that the new input html can be appended
  //use loop instead of simply using .innerHTML because we need to preserve the value attributes for each input so the links users haveinputted so far are not lost
  var blog_sources_html = "";
  for(var i = 0; i<source_input.length; i++){
   blog_sources_html += '<li><input class="source_input" name="source_input_' + i + '" type="text" value="' + source_input[i].value + '" placeholder="Paste link here"></li>';
  }
  blog_sources.innerHTML = blog_sources_html + '<li><input class="source_input" name="source_input_' + counter + '" type="text" placeholder="Paste link here"></li>';
  counter ++;
  //set innerhtml of hidden sources_count input to the current number of sources so php createPost function knows how many source inputs to process
  sources_count.value = counter;
};
//only call the function for the user links stuff if the user is logged in
//and only call the function for the sign up form zipcode explanation if user not logged in
if(hello_user){
  //allow user to click name in top right to open menu
  openDivs(hello_user, user_links, 'table', true, delete_link, delete_div_container);
  //call for the delete acount link
  openDivs(delete_link, delete_div_container, 'block', true, delete_div_content);
}

//opendivs didnt work perfectly in this case so had to add some code here
var cancelDelete = function(){
  delete_div_container.style.display = "none";
  //call the same openDivs function again so delete div will open right away if delete link is clicked again
  openDivs(delete_link, delete_div_container, 'block', true, delete_div_content);
};
if(hello_user){
  cancel_delete.addEventListener("click", cancelDelete);
}

//call for menu dropdown for mobile dimensions
var nav_tag_menu_dropdown = document.getElementById('nav_tag_menu_dropdown_h4');
var dropdown_main_links = document.getElementById('dropdown_main_links');
openDivs(nav_tag_menu_dropdown, dropdown_main_links, 'block', true);
