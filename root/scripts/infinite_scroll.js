//fill another page worth of previews
var loadPage = function(page_num, loading, category){
  if(loading == false){
    loading = true;
    //send a server post request to infinite_scroll php to get data from blog table, setting post variables for page_num and category
    $.ajax({
        url: '../infinite_scroll.php',
        type: "post",
        dataType: 'JSON',
        data: {
          page_num: page_num,
          cat_page_category: category
        },
        beforeSend: function(){
          //before server request is completed, display loading message div
          $('ajax_loader').show();
          return;
        }
    }).done(function(data) {
      //once the server request is complete, hide the loader and append the data to the div cointaining the previews
      $('#ajax_loader').hide();
      loading = false
      //for eacho of the items returned in the array of formatted divs, output the item to the all_previews_cont div
      for(var i = 0; i < data[0].length; i++){
        $('#all_previews_cont').append(data[0][i]);
      }

      if(data[1] == 1){
        //if php set the $footer_exists variable to 1, output the footer by sending a server request to footer.php and appending the result to the body of the page
        $.ajax({
           url: '../footer.php',
           type: "post",
           data: {
             cat_page_path: "../"
           }
         }).done(function(data) {
           //append footer to body
           $('body').append(data);
           //set global footer_exists variable so that loadPages is not called for now on
           window.footer_exists = 1;
         });
      }

      //call setBackgroundImage for all the previews now that more have been added
      setBackgroundImage("cat_preview_image_cont", "cat_image_path");
    }).fail(function(jgXHR, ajaxOptions, thrownError) {
      $('ajax_loader').hide();
    });
  }
};


//script for calling previews to be loaded on initial load of document and when user scrolls
  //call function on initial page load
  $(document).ready(function(){
    //variable to track number of 'pages' down the user has scrolled
    var page_num = 1;
    //specific category of blog posts being requested, gotten from hidden cat_page_category div
    cat_page_category = document.getElementById('cat_page_category');
    var category = cat_page_category.innerHTML;
    //call loadPage to get first page of previews
    loadPage(page_num, false, category);

    //call function on scroll
    $(window).scroll(function(){
      //check if the amount the user has scrolled plus the amount displayed in their window is greater than the height of the whole document - 100 (hey have scrolled to the bottom)
      //and check that footer is not present, because if it is there is no need to load anything more since it signals the bottom of the page
      if($(window).scrollTop() + $(window).height() > $(document).height() - 100 && !window.footer_exists){
        page_num ++;
        loadPage(page_num, false, category);
      }
    });
  });
