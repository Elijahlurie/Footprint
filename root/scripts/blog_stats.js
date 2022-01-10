//add event listeners to timespan dropdown to allow user to see statistics on different time scales
 //get the option divs in the timespan dropdown
var today_option = document.getElementById("today_option");
var week_option = document.getElementById("week_option");
var year_option = document.getElementById("year_option");
 //get array of the divs in the timespan dropdown
var stat_timespan_text = document.getElementById("stat_timespan");
 //get array of the divs displaying the statistics
var statistic_divs = document.getElementsByClassName("statistic_number");

//store original statistic values in an array to not forget them
var original_numbers = [50000, 12000000, 70000000];
//store timespan options in an array
var timespans = [1,7,365];
//store text version of timespans in an array
var timespan_words = ['today','this week','this year'];
//given an index value for the arrays, change statistic numbers inside the divs
//since each item in the original_numbers array correponds with an div in the statistic_number array, the same index value is used
//simillarly, since each itm in the timespans array correponds with a string in the timespan_words array, the same index value is used
var changeTimespan = function(stat_div_index, timespans_index){
  //change the number inside the statistic number div
  var new_number = original_numbers[stat_div_index] * timespans[timespans_index];
  //format by adding commas
  statistic_divs[stat_div_index].innerHTML = new_number.toLocaleString("en-US");
  //change the text between 'today', 'this week', and 'this year'
  stat_timespan_text.innerHTML = timespan_words[timespans_index];
};

 //for each of the divs containing the statistic numbers, add an event listener to each of the dropdown options to multiply it by the right amount
 //loop is not used because changeTimespan must be wrapped in empty function to prevent from calling right away, but this prevents changeTimespan from accepting variales from a for loop as arguments
 today_option.addEventListener("click", function(){changeTimespan(0, 0)});
 today_option.addEventListener("click", function(){changeTimespan(1, 0)});
 today_option.addEventListener("click", function(){changeTimespan(2, 0)});

 week_option.addEventListener("click", function(){changeTimespan(0, 1)});
 week_option.addEventListener("click", function(){changeTimespan(1, 1)});
 week_option.addEventListener("click", function(){changeTimespan(2, 1)});

 year_option.addEventListener("click", function(){changeTimespan(0, 2)});
 year_option.addEventListener("click", function(){changeTimespan(1, 2)});
 year_option.addEventListener("click", function(){changeTimespan(2, 2)});
